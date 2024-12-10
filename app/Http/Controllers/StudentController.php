<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\User;
use App\Models\Profile;
use App\Models\Requirement;
use App\Models\AcademicYear;
use App\Models\Priority;
use App\Models\SkillTag;
use App\Models\Application;
use App\Models\ApplicationStatus;
use App\Models\AcceptedInternship;
use App\Models\DailyTimeRecord;
use App\Models\EndOfDayReport;
use App\Models\InternshipHours;
use App\Models\Penalty;
use App\Models\Evaluation;
use App\Models\EvaluationRecipient;
use App\Models\PenaltiesAwarded;
use App\Models\Request as StudentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;



class StudentController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user()->load('course', 'profile');
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();
        $schoolYear = $currentAcademicYear ? $currentAcademicYear->start_year . '-' . $currentAcademicYear->end_year : 'Not Set';

        $acceptedInternship = AcceptedInternship::where('student_id', $student->id)->first();

        if (!$acceptedInternship) {
            return view('student.dashboard', compact('student', 'schoolYear'))->with('noInternship', true);
        }

        $dailyRecords = DailyTimeRecord::where('student_id', $student->id)->get();
        $internshipHours = InternshipHours::where('course_id', $student->course_id)->first();
        $latestDailyRecord = DailyTimeRecord::where('student_id', $student->id)->latest('log_date')->first();
        $remainingHours = $latestDailyRecord ? $latestDailyRecord->remaining_hours : $internshipHours->hours;
        $totalWorkedHours = $dailyRecords->sum('total_hours_worked');
        $currentDate = Carbon::now();
        $startDate = Carbon::parse($acceptedInternship->start_date);

        // Schedule and Days Calculation
        $schedule = $acceptedInternship->schedule;
        if (!is_array($schedule)) {
            $schedule = json_decode($schedule, true);
        }
        $scheduledDays = $this->getScheduledDays($student, $acceptedInternship, $schedule);

        // Reports for current month
        $selectedMonth = $request->input('month', $currentDate->month);
        $reports = EndOfDayReport::where('student_id', $student->id)
            ->whereMonth('date_submitted', $selectedMonth)
            ->get();

        // Missing dates calculation if remaining hours are above zero
        $missingDates = $remainingHours > 0 ? $this->getMissingSubmissionDates($student->id, $scheduledDays, $startDate, $currentDate) : collect();

        // Monthly hours calculation for chart
        $monthlyHours = [];
        $monthIterator = $startDate->copy()->startOfMonth();
        while ($monthIterator->lte($currentDate)) {
            $month = $monthIterator->format('m');
            $year = $monthIterator->format('Y');
            $monthName = $monthIterator->format('F');
            $monthlyHours[$monthName] = $dailyRecords->filter(function ($record) use ($month, $year) {
                return Carbon::parse($record->log_date)->month == $month && Carbon::parse($record->log_date)->year == $year;
            })->sum('total_hours_worked');
            $monthIterator->addMonth();
        }

        // Check if today is scheduled and if report submitted
        $hasSubmittedToday = EndOfDayReport::where('student_id', $student->id)
            ->whereDate('date_submitted', $currentDate->format('Y-m-d'))
            ->exists();
            

        // Check if DTR has been submitted today (log_times is not null or no record for today)
        $hasSubmittedDTRToday = DailyTimeRecord::where('student_id', $student->id)
            ->whereDate('log_date', $currentDate->format('Y-m-d'))
            ->whereNotNull('log_times')
            ->exists();

                    
        $isScheduledDay = in_array($currentDate->format('l'), $scheduledDays) && $currentDate->gte($startDate);

        // Estimate Finish Date
        // $estimatedFinishDate = $this->calculateFinishDate($remainingHours, $startDate, $scheduledDays);

        // Retrieve pending evaluations
        $pendingEvaluations = $this->listPendingEvaluations();

        $pendingRequests = StudentRequest::where('student_id', Auth::id())->get();

        $penaltiesGained = PenaltiesAwarded::where('student_id', Auth::id())->get();

        return view('student.dashboard', compact(
            'student', 
            'schoolYear', 
            'acceptedInternship', 
            'totalWorkedHours', 
            'remainingHours', 
            'scheduledDays', 
            'startDate', 
            'currentDate', 
            'reports', 
            'missingDates', 
            'monthlyHours', 
            'hasSubmittedToday',
            'hasSubmittedDTRToday', 
            'isScheduledDay', 
            'pendingEvaluations',
            'pendingRequests',
            'penaltiesGained'
        ));
    }


    private function getScheduledDays($student, $acceptedInternship, $schedule)
    {
        if ($student->profile->is_irregular && $acceptedInternship->custom_schedule) {
            $customSchedule = is_array($acceptedInternship->custom_schedule) ? $acceptedInternship->custom_schedule : json_decode($acceptedInternship->custom_schedule, true);
            return array_keys($customSchedule);
        } else {
            return $acceptedInternship->work_type === 'Hybrid'
                ? array_merge($schedule['onsite_days'] ?? [], $schedule['remote_days'] ?? [])
                : $schedule['days'] ?? [];
        }
    }
    
    private function getMissingSubmissionDates($studentId, $scheduleDays, $startDate, $currentDate)
    {
        $allScheduledDays = collect();
        for ($date = $startDate->copy(); $date->lte($currentDate); $date->addDay()) {
            if (in_array($date->format('l'), $scheduleDays)) {
                $allScheduledDays->push($date->copy()->format('Y-m-d'));
            }
        }
    
        $submissionDates = EndOfDayReport::where('student_id', $studentId)
            ->whereDate('date_submitted', '>=', $startDate)
            ->whereDate('date_submitted', '<=', $currentDate)
            ->pluck('date_submitted')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            });
    
        return $allScheduledDays->diff($submissionDates);
    }
    

    
    private function listPendingEvaluations()
    {
        $user = auth()->user();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        // Fetch evaluations that have been sent to the user based on role or specifically to the user
        $evaluations = Evaluation::where('academic_year_id', $currentAcademicYear->id)
            ->whereHas('recipients', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('creator')
            ->get()
            ->filter(function ($evaluation) use ($user) {
                $recipient = EvaluationRecipient::where('evaluation_id', $evaluation->id)
                    ->where('user_id', $user->id)
                    ->first();
                return $recipient && !$recipient->is_answered; // Only include unanswered evaluations
            })
            ->map(function ($evaluation) {
                $evaluation->type_label = $this->getEvaluationTypeLabel($evaluation->evaluation_type);
                return $evaluation;
            });

        return $evaluations;
    }

    // Helper method for evaluation type labels
    private function getEvaluationTypeLabel($type)
    {
        switch ($type) {
            case 'program':
                return 'Program';
            case 'intern_student':
                return 'Intern Performance';
            case 'intern_company':
                return 'Exit Form';
            default:
                return 'Evaluation';
        }
    }


    // private function calculateFinishDate($remainingHours, $startDate, $scheduledDays)
    // {
    //     $estimatedDays = ceil($remainingHours / 8);
    //     // $date = Carbon::parse($startDate);
    //     $date = Carbon::now();  // Start from today
    //     $daysWorked = 0;
    
    //     while ($daysWorked < $estimatedDays) {
    //         if (in_array($date->format('l'), $scheduledDays)) {
    //             $daysWorked++;
    //         }
    //         $date->addDay();
    //     }
    
    //     return $date;
    // }


    // Internship Listings Page
    public function internshipListings()
    {
        $student = Auth::user();
        $profileSkills = $student->profile->skillTags->pluck('id'); // Skills of student

        // Recommended Listings: Jobs that match the student's skills
        $recommendedJobs = Job::whereHas('skillTags', function ($query) use ($profileSkills) {
            $query->whereIn('skill_tag_id', $profileSkills);
        })->where('positions_available', '>', 0)->get();

        // Other Listings: Jobs that don't match student's skills
        $otherJobs = Job::whereDoesntHave('skillTags', function ($query) use ($profileSkills) {
            $query->whereIn('skill_tag_id', $profileSkills);
        })->where('positions_available', '>', 0)->get();

        return view('student.listings', compact('student', 'recommendedJobs', 'otherJobs'));
    }

    // Internship Applications Page
    public function internshipApplications()
    {
        $student = Auth::user();

        // Fetch priority job listings
        $priorityListings = Priority::where('student_id', $student->id)
            ->with('job')
            ->orderBy('priority')  // Sort by priority (1st, 2nd)
            ->get();
    
        // Check if the 1st priority job is submitted or rejected
        $firstPriorityJob = $priorityListings->where('priority', 1)->first();
    
        // Determine if the student can apply for the second priority
        $firstPriorityApplication = Application::where('student_id', $student->id)
            ->where('job_id', $firstPriorityJob->job_id ?? null)
            ->first();
    
        // Determine if the first priority is either submitted or rejected
        $firstPrioritySubmittedOrRejected = $firstPriorityApplication 
            && in_array($firstPriorityApplication->status->status, ['Submitted', 'Rejected']);
    
        // Fetch submitted applications
        $submittedApplications = Application::where('student_id', $student->id)
            ->with('job', 'status')
            ->get();
    
        return view('student.applications', compact(
            'student', 
            'priorityListings', 
            'submittedApplications', 
            'firstPrioritySubmittedOrRejected'
        ));
    }


    // Set Priority for a Job
    public function setPriority(Request $request)
    {
        $request->validate([
            'priority' => 'required|in:1,2',
            'job_id' => 'required|exists:jobs,id',
        ]);

        $student = Auth::user();

        // Check if student already has two priorities
        $existingPriorities = Priority::where('student_id', $student->id)->count();

        if ($existingPriorities >= 2) {
            return redirect()->back()->withErrors('You already have two priority listings.');
        }

        // Create or update the priority
        Priority::updateOrCreate(
            [
                'student_id' => $student->id,
                'job_id' => $request->job_id
            ],
            [
                'priority' => $request->priority,
            ]
        );

        return redirect()->back()->with('success', 'Priority set successfully.');
    }

        // Submit Application for a Job
        public function submitApplication(Request $request, $jobId)
        {
            $student = Auth::user();
            $job = Job::findOrFail($jobId);
    
            // Check if CV exists in the profile
            if (!$student->profile->cv_file_path) {
                return redirect()->back()->withErrors('Please upload your CV in your profile first.');
            }

            // Check if endorsement letter exists in the requirements
            if (!$student->requirements || !$student->requirements->endorsement_letter) {
                return redirect()->back()->withErrors('Please wait for the endorsement letter to be uploaded in the requirements.');
            }
            
    
            // Retrieve the endorsement letter from the student's requirements
            $endorsementLetterPath = $student->requirements->endorsement_letter;
    
            // Update or create the application
            Application::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'job_id' => $job->id,
                ],
                [
                    'endorsement_letter_path' => $endorsementLetterPath, // Use endorsement letter from requirements
                    'cv_path' => $student->profile->cv_file_path, // Use CV from the profile
                    'status_id' => 1, // "To Review"
                    'date_posted' => now(),
                ]
            );

            return redirect()->route('internship.applications')->with('success', 'Application submitted successfully.');
        }

        // File Preview Method
        public function previewFile($type, $id)
        {
            $application = Application::findOrFail($id);

            if ($type == 'endorsement_letter' && $application->endorsement_letter_path) {
                $filePath = storage_path('app/' . $application->endorsement_letter_path);
            } elseif ($type == 'cv' && $application->cv_path) {
                $filePath = storage_path('app/' . $application->cv_path);
            } else {
                abort(404);
            }

            $fileMimeType = mime_content_type($filePath);

            return response()->file($filePath, [
                'Content-Type' => $fileMimeType,
            ]);
        }

        // Remove Priority for a Job
        public function removePriority($jobId)
        {
            $student = Auth::user();

            // Check if the application for this job has already been submitted
            $submittedApplication = Application::where('student_id', $student->id)
                ->where('job_id', $jobId)
                ->first();

            if ($submittedApplication && $submittedApplication->endorsement_letter_path) {
                return redirect()->back()->withErrors('You cannot remove priority for a job you have already submitted an application for.');
            }

            // Remove priority if no application has been submitted
            Priority::where('student_id', $student->id)
                ->where('job_id', $jobId)
                ->delete();

            return redirect()->back()->with('error', 'Priority removed successfully.');
        }

        //Priority Management for Admins

        public function adminSetPriority(Request $request, $studentId)
        {
            $request->validate([
                'priority' => 'required|in:1,2',
                'job_id' => 'required|exists:jobs,id',
            ]);

            $student = User::findOrFail($studentId);

            // Check if the selected job is already prioritized by the student
            $existingJobPriority = Priority::where('student_id', $student->id)
                ->where('job_id', $request->job_id)
                ->first();

            if ($existingJobPriority) {
                return redirect()->back()->with('error', 'This job is already set as a priority for the student.');
            }

            // Check if the selected priority level is already taken by another job
            $existingPriorityLevel = Priority::where('student_id', $student->id)
                ->where('priority', $request->priority)
                ->first();

            if ($existingPriorityLevel) {
                return redirect()->back()->with('error', "The {$request->priority} priority level is already assigned to another job.");
            }

            // Create or update the priority
            Priority::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'job_id' => $request->job_id
                ],
                [
                    'priority' => $request->priority,
                ]
            );

            return redirect()->back()->with('success', 'Priority set successfully for the student.');
        }

        // Remove Priority for Admins
        public function adminRemovePriority($studentId, $jobId)
        {
            $student = User::findOrFail($studentId);

            // Check if the application for this job has already been submitted
            $submittedApplication = Application::where('student_id', $student->id)
                ->where('job_id', $jobId)
                ->first();

            if ($submittedApplication) {
                return redirect()->back()->with('error', 'Cannot remove priority for a job with a submitted application.');
            }

            // Remove priority if no application has been submitted
            Priority::where('student_id', $student->id)
                ->where('job_id', $jobId)
                ->delete();

            return redirect()->back()->with('success', 'Priority removed successfully.');
        }

        public function showManagePriorityPage($studentId)
        {
            $student = User::findOrFail($studentId);

            // Fetch all jobs for the dropdown
            $jobs = Job::where('positions_available', '>', 0)->get();

            // Fetch the student's current priority listings
            $priorityListings = Priority::where('student_id', $student->id)
                ->with('job.company')
                ->orderBy('priority')
                ->get();

            return view('administrative.admin-priority', compact('student', 'jobs', 'priorityListings'));
        }


}
