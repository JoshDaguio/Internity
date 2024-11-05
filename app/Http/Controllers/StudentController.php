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
use App\Models\InternshipHours;
use App\Models\Penalty;
use App\Models\Evaluation;
use App\Models\EvaluationRecipient;
use App\Models\PenaltiesAwarded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


class StudentController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user()->load('course', 'profile');
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        // Set school year display message
        $schoolYear = $currentAcademicYear ? $currentAcademicYear->start_year . '-' . $currentAcademicYear->end_year : 'Not Set';

        // Fetch the student's accepted internship
        $acceptedInternship = AcceptedInternship::where('student_id', $student->id)->first();

        // Check if there's no accepted internship and immediately return the view with a flag
        if (!$acceptedInternship) {
            return view('student.dashboard', compact('student', 'schoolYear'))->with('noInternship', true);
        }


        // Fetch all penalties
        $penalties = Penalty::all();
        $penaltiesAwarded = PenaltiesAwarded::where('student_id', $student->id)->get();

        $dailyRecords = DailyTimeRecord::where('student_id', $student->id)->get();

        $internshipHours = InternshipHours::where('course_id', $student->course_id)->first();

        // Fetch the latest Daily Time Record for this student (even if it's not today)
        $latestDailyRecord = DailyTimeRecord::where('student_id', $student->id)
            ->orderBy('log_date', 'desc')
            ->first();

        // Fallback for remaining hours if there is no DTR yet
        $remainingHours = $latestDailyRecord ? $latestDailyRecord->remaining_hours : $internshipHours->hours;

        // Get current date and start date for filtering
        $currentDate = Carbon::now();
        $startDate = Carbon::parse($acceptedInternship->start_date);

        // Calculate total worked hours
        $totalWorkedHours = $dailyRecords->sum('total_hours_worked');
        $remainingHours = $latestDailyRecord ? $latestDailyRecord->remaining_hours : $internshipHours->hours;

        // Fetch the schedule
        $schedule = $acceptedInternship->schedule;

        // Initialize $scheduledDays as an empty array in case it's not set later
        $scheduledDays = [];

        // If the schedule is not an array (i.e., it's stored as JSON), decode it
        if (!is_array($schedule)) {
            $schedule = json_decode($schedule, true);
        }

        // Check if the student is irregular and if there is a custom schedule
        if ($student->profile->is_irregular && $acceptedInternship->custom_schedule) {
            $customSchedule = $acceptedInternship->custom_schedule;

            // If custom schedule is not an array, decode it
            if (!is_array($customSchedule)) {
                $schedule = json_decode($customSchedule, true);
            } else {
                $schedule = $customSchedule; // Use the custom schedule directly
            }

            // Extract the days from the custom schedule
            $scheduledDays = array_keys($schedule);

        } else {
            // Handle Hybrid schedules
            if ($acceptedInternship->work_type === 'Hybrid') {
                $scheduledDays = array_merge($schedule['onsite_days'], $schedule['remote_days']);
            } else {
                // For On-site or Remote, use the standard 'days' array
                $scheduledDays = $schedule['days'] ?? [];
            }
        }

        // Filter the logs by month based on the user's input or current month
        $selectedMonth = $request->input('month', $currentDate->month); // Default to current month

        // Generate the range of months from the start date to the current date
        $monthsRange = collect();
        $monthIterator = $startDate->copy()->startOfMonth();

        // Monthly hours calculation for the line chart
        $monthlyHours = [];
        $monthlyPenalties = [];

        while ($monthIterator->lte($currentDate)) {
            $month = $monthIterator->format('m');
            $year = $monthIterator->format('Y');
            $monthName = $monthIterator->format('F');
    
            $monthlyHours[$monthName] = $dailyRecords->filter(function ($record) use ($month, $year) {
                return Carbon::parse($record->log_date)->month == $month && Carbon::parse($record->log_date)->year == $year;
            })->sum('total_hours_worked');

            // Total penalties gained per month
            $monthlyPenalties[$monthName] = $penaltiesAwarded->filter(function ($penalty) use ($month, $year) {
                return Carbon::parse($penalty->awarded_date)->month == $month && Carbon::parse($penalty->awarded_date)->year == $year;
            })->sum('penalty_hours');
    
            $monthsRange->push(['month' => $month, 'monthName' => $monthName]);
            $monthIterator->addMonth();
        }
        
        $filteredRecords = $dailyRecords->filter(function ($record) use ($selectedMonth) {
            return Carbon::parse($record->log_date)->month == $selectedMonth;
        });
        
        // Get the days for the selected month for the whole month to display in the table
        $filteredDates = collect();
        $monthStart = Carbon::createFromDate($startDate->year, $selectedMonth, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        // Adjust monthStart for the first month if it is the month of the start date
        if ($selectedMonth == $startDate->month) {
            $monthStart = $startDate;
        }
        
        // Show only up to today for the current month
        if ($selectedMonth == $currentDate->month) {
            $monthEnd = $currentDate;
        }

        while ($monthStart->lte($monthEnd)) {
            // Only include scheduled days
            $dayOfWeek = $monthStart->format('l');
            if (in_array($dayOfWeek, $scheduledDays)) {
                $filteredDates->push($monthStart->copy());
            }
            $monthStart->addDay();
        }

        // Calculate completion percentage
        $completionPercentage = $remainingHours > 0 ? ($totalWorkedHours / $remainingHours) * 100 : 100;


        // Estimate the finish date
        $estimatedFinishDate = $this->calculateFinishDate($remainingHours, $currentDate, $scheduledDays);


        // Retrieve pending evaluations
        $pendingEvaluations = $this->listPendingEvaluations();
    
        return view('student.dashboard', compact(
            'student', 
            'currentAcademicYear',
            'schoolYear',
            'acceptedInternship',
            'penaltiesAwarded', 
            'completionPercentage',
            'totalWorkedHours', 
            'penalties', 
            'acceptedInternship', 
            'internshipHours', 
            'schedule', 
            'currentDate', 
            'startDate', 
            'selectedMonth', 
            'scheduledDays', 
            'remainingHours', 
            'estimatedFinishDate', 
            'filteredDates', 
            'monthsRange', 
            'monthlyHours', 
            'monthlyPenalties',
             'pendingEvaluations'
        ));
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
                return 'Program Evaluation';
            case 'intern_student':
                return 'Intern Performance Evaluation';
            case 'intern_company':
                return 'Intern Exit Form';
            default:
                return 'Evaluation';
        }
    }


    private function calculateFinishDate($remainingHours, $startDate, $scheduledDays)
    {
        $estimatedDays = ceil($remainingHours / 8);
        // $date = Carbon::parse($startDate);
        $date = Carbon::now();  // Start from today
        $daysWorked = 0;
    
        while ($daysWorked < $estimatedDays) {
            if (in_array($date->format('l'), $scheduledDays)) {
                $daysWorked++;
            }
            $date->addDay();
        }
    
        return $date;
    }


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
