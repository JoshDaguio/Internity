<?php

namespace App\Http\Controllers;
use App\Models\Job;
use App\Models\Application;
use App\Models\ApplicationStatus;
use App\Models\AcceptedInternship;
use App\Models\InternshipHours;
use App\Models\DailyTimeRecord;
use App\Models\StudentAccepted;
use App\Models\AcademicYear;
use App\Models\StudentRejected;
use App\Models\Interview;
use App\Models\User;
use App\Models\Pullout;
use App\Models\Profile;
use App\Models\EndOfDayReport;
use App\Models\Evaluation;
use App\Models\EvaluationRecipient;
use App\Models\ActivityLog;
use App\Mail\CompanyApprovalMail;
use App\Mail\InterviewScheduled; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel; 
use Illuminate\Support\Facades\Storage; 
use App\Imports\CompanyImport; 


use Illuminate\Http\Request;

class CompanyController extends Controller
{

    public function dashboard()
    {
        $company = Auth::user(); // Assuming the logged-in user is the company
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();
    
        // Ensure current academic year is available
        if (!$currentAcademicYear) {
            return view('company.dashboard')->withErrors(['msg' => 'Current academic year is not set']);
        }
    
        // Fetch students with accepted internships under this company, active status, and current academic year
        $acceptedInternships = AcceptedInternship::where('company_id', $company->id)
            ->whereHas('student', function ($query) use ($currentAcademicYear) {
                $query->where('status_id', 1) // Only active students
                      ->where('academic_year_id', $currentAcademicYear->id);
            })
            ->with(['student.profile', 'student.course'])
            ->get();
    
        // Calculate the count of active interns and details for each student
        $activeInternsCount = 0;
        $students = [];
    
        foreach ($acceptedInternships as $internship) {
            $student = $internship->student;
    
            // Fetch daily records and calculate total worked hours and remaining hours
            $dailyRecords = DailyTimeRecord::where('student_id', $student->id)->get();
            $latestDailyRecord = DailyTimeRecord::where('student_id', $student->id)
                ->orderBy('log_date', 'desc')
                ->first();
            
            $internshipHours = InternshipHours::where('course_id', $student->course_id)->first();
            // $totalWorkedHours = $dailyRecords->sum('total_hours_worked');
            // $latestDailyRecord = $dailyRecords->sortByDesc('log_date')->first();
            // $remainingHours = $latestDailyRecord ? $latestDailyRecord->remaining_hours : 0;
    
            // $student->totalWorkedHours = $totalWorkedHours;
            // $student->remainingHours = $remainingHours;
            // $student->completionPercentage = $remainingHours > 0 
            //     ? ($totalWorkedHours / ($totalWorkedHours + $remainingHours)) * 100 
            //     : 100;

            // $student->hasInternship = true;

             // Calculate total worked hours
             $totalWorkedHours = $dailyRecords->sum('total_hours_worked');

             // Determine remaining hours based on the latest DTR or fallback to the total internship hours
             $remainingHours = $latestDailyRecord ? $latestDailyRecord->remaining_hours : ($internshipHours->hours ?? 0);

             if ($remainingHours > 0) {
                 $student->completionPercentage = ($totalWorkedHours / $remainingHours) * 100;
             } else {
                 // If remaining hours are 0, consider the internship completed
                 $student->completionPercentage = 100;
             }

             $student->totalWorkedHours = $totalWorkedHours;
             $student->remainingHours = $remainingHours;
             $student->hasInternship = true;
    
            // Check if the student is actively interning (remaining hours > 0)
            if ($remainingHours > 0) {
                $activeInternsCount++;
            }
    
            $students[] = $student;
        }
    
        // Get the number of jobs posted by this company
        $postedJobsCount = Job::where('company_id', $company->id)->count();
    
        // Calculate total applicants across all jobs posted by this company
        $totalApplicantsCount = Job::where('company_id', $company->id)
            ->withCount('applications')
            ->get()
            ->sum('applications_count');

        $studentsWithNoDTRToday = $this->getStudentsWithNoDTRToday();
        $studentsWithNoEODToday = $this->getStudentsWithNoEODToday();

        // Retrieve pending evaluations
        $pendingEvaluations = $this->listPendingEvaluations();
        
        // Retrieve pending pullout requests
        $pendingPullouts = Pullout::where('company_id', $company->id)
            ->where('academic_year_id', $currentAcademicYear->id)
            ->where('status', 'Pending') // Assuming "Pending" is the status for pending pullouts
            ->with(['students.profile', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();
    
    
    
        return view('company.dashboard', compact(
            'students',
            'company', 
            'postedJobsCount', 
            'totalApplicantsCount', 
            'activeInternsCount',
            'studentsWithNoDTRToday',
            'studentsWithNoEODToday',
            'pendingEvaluations',
            'pendingPullouts'
        ));
    }
    

    public function getStudentsWithNoDTRToday()
    {
        // Get the current academic year
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        $today = Carbon::now(new \DateTimeZone('Asia/Manila'))->format('l'); // e.g., 'Monday'
        $todayDate = Carbon::now(new \DateTimeZone('Asia/Manila'))->toDateString(); // Format as 'YYYY-MM-DD'
    
        // Get all active students with accepted internships
        $students = User::where('role_id', 5) // Assuming role_id 5 is for students
                        ->where('status_id', 1) // Assuming status_id 1 is for active students
                        ->whereHas('acceptedInternship')
                        ->with(['acceptedInternship', 'profile', 'course'])
                        ->where('academic_year_id', $currentAcademicYear->id) // Students under the current Academic Year
                        ->get();
    
        $studentsWithNoDTRToday = [];
    
        foreach ($students as $student) {
            $acceptedInternship = $student->acceptedInternship;
    
            // Determine schedule based on student type (regular or irregular)
            if ($student->profile->is_irregular && $acceptedInternship->custom_schedule) {
                // Handle custom schedule (decode if stored as JSON)
                $schedule = is_string($acceptedInternship->custom_schedule)
                    ? json_decode($acceptedInternship->custom_schedule, true)
                    : $acceptedInternship->custom_schedule;
                
                $scheduledDays = is_array($schedule) ? array_keys($schedule) : [];
            } else {
                // Handle standard schedule (decode if stored as JSON)
                $schedule = is_string($acceptedInternship->schedule)
                    ? json_decode($acceptedInternship->schedule, true)
                    : $acceptedInternship->schedule;
    
                $scheduledDays = is_array($schedule) 
                    ? ($acceptedInternship->work_type === 'Hybrid'
                        ? array_merge($schedule['onsite_days'] ?? [], $schedule['remote_days'] ?? [])
                        : ($schedule['days'] ?? []))
                    : [];
            }
    
            // Check if today is a scheduled day for the student
            if (in_array($today, $scheduledDays)) {
                // Get the latest DTR record to check remaining hours
                $latestDTR = DailyTimeRecord::where('student_id', $student->id)
                                            ->latest('log_date')
                                            ->first();
    
                // Ensure the student has remaining hours greater than 0 and they have not yet completed their internship
                if ($latestDTR && $latestDTR->remaining_hours > 0) {
                    // Check if there is a DTR entry for today with no log times
                    $todayDTR = DailyTimeRecord::where('student_id', $student->id)
                                ->whereDate('log_date', $todayDate)
                                ->first();
    
                    // Add student to the list if there's no DTR for today or if `log_times` is null
                    if (!$todayDTR || is_null($todayDTR->log_times)) {
                        $studentsWithNoDTRToday[] = $student;
                    }
                }
            }
        }
    
        return $studentsWithNoDTRToday;
    }


    
    public function getStudentsWithNoEODToday()
    {
        // Get the current academic year
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        $today = Carbon::now(new \DateTimeZone('Asia/Manila'))->format('l'); // e.g., 'Monday'
        $todayDate = Carbon::now(new \DateTimeZone('Asia/Manila'))->toDateString(); // Format as 'YYYY-MM-DD'
    
        // Get all active students with accepted internships
        $students = User::where('role_id', 5) // Assuming role_id 5 is for students
                        ->where('status_id', 1) // Assuming status_id 1 is for active students
                        ->whereHas('acceptedInternship')
                        ->with(['acceptedInternship', 'profile', 'course'])
                        ->where('academic_year_id', $currentAcademicYear->id) // Students under the current Academic Year
                        ->get();
    
        $studentsWithNoEODToday = [];
    
        foreach ($students as $student) {
            $acceptedInternship = $student->acceptedInternship;
    
            // Determine schedule based on student type (regular or irregular)
            if ($student->profile->is_irregular && isset($acceptedInternship->custom_schedule)) {
                $schedule = is_string($acceptedInternship->custom_schedule) ? json_decode($acceptedInternship->custom_schedule, true) : $acceptedInternship->custom_schedule;
                $scheduledDays = array_keys($schedule); // Days are keys in custom schedules
            } else {
                $schedule = is_string($acceptedInternship->schedule) ? json_decode($acceptedInternship->schedule, true) : $acceptedInternship->schedule;
                $scheduledDays = $acceptedInternship->work_type === 'Hybrid'
                    ? array_merge($schedule['onsite_days'] ?? [], $schedule['remote_days'] ?? [])
                    : ($schedule['days'] ?? []);
            }
    
            // Check if today is a scheduled day for the student
            if (in_array($today, $scheduledDays)) {
                // Get the latest DTR record to check remaining hours
                $latestDTR = DailyTimeRecord::where('student_id', $student->id)
                                            ->latest('log_date')
                                            ->first();
    
                // Ensure the student has remaining hours greater than 0 and has not completed their internship
                if ($latestDTR && $latestDTR->remaining_hours > 0) {
                    // Check if there is an EOD report entry for today
                    $eodToday = EndOfDayReport::where('student_id', $student->id)
                                ->whereDate('created_at', $todayDate)
                                ->exists();
    
                    // Add student to the list if there's no EOD report for today
                    if (!$eodToday) {
                        $studentsWithNoEODToday[] = $student;
                    }
                }
            }
        }
    
        return $studentsWithNoEODToday;
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
    
    
    public function index(Request $request)
    {
        $query = User::where('role_id', 4); // Assuming role_id 4 is for companies

        // Apply the status filter
        if ($request->has('status_id') && in_array($request->status_id, ['1', '2'])) {
            $query->where('status_id', $request->status_id);
        }
    
        $companies = $query->get();
    
        return view('company.index', compact('companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'expiry_date' => 'required|date|after_or_equal:today', 
            'moa_file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);


        // Handle MOA file upload
        $moaPath = null;
        if ($request->hasFile('moa_file')) {
            $moaPath = $request->file('moa_file')->store('moa_files', 'public'); // Save file in the public disk
        }

        // Create profile for the contact person
        $profile = Profile::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'id_number' => null, // No ID number for companies
            'contact_number' => $request->contact_number,
            'moa_file_path' => $moaPath, // Save the MOA file path
        ]);

        // Generate a random password with "aufCCSInternshipCompany" + 5 random characters
        $password = 'aufCCSInternshipCompany' . Str::random(5);

        // Create company account
        $company = User::create([
            'name' => $request->name, // Company name
            'email' => $request->email,
            'password' => Hash::make($password), // Store hashed password
            'role_id' => 4, // Company role
            'status_id' => 1, // Active status
            'profile_id' => $profile->id,
            'expiry_date' => $request->expiry_date, // Store the expiry date
        ]);

        // Log the creation of the company
        ActivityLog::create([
            'admin_id' => Auth::id(),
            'action' => 'Created Company',
            'target' => $company->name, // Company name as the target
            'changes' => json_encode([
                'name' => $company->name,
                'email' => $company->email,
            ]),
        ]);

            // Send the email with login details
        \Mail::to($company->email)->send(new \App\Mail\CompanyApprovalMail(
            $company->name,
            $company->email,
            $password
        ));

        return redirect()->route('company.index')->with('success', 'Company account created successfully.');
    }

    public function create()
    {
        return view('company.create');
    }
    
    public function show(User $company)
    {
        // Ensure we're displaying a company account (role_id = 4)
        if ($company->role_id !== 4) {
            abort(404);
        }

        // Fetch the jobs posted by the company
        $company->load('jobs');

        return view('company.show', compact('company'));
    }

    public function edit($id)
    {
        $company = User::where('role_id', 4)->findOrFail($id);
        return view('company.edit', compact('company'));
    }

    public function update(Request $request, User $company)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $company->id,
            'first_name' => 'required|string|max:255',
            'password' => ['nullable', 'string', 'min:8'],
            'last_name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'expiry_date' => 'required|date|after_or_equal:today', 
            'moa_file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $updatedFields = [];
        $newPassword = null; 
        $sendEmail = false;

        // Check and log changes to the company name
        if ($company->name != $request->name) {
            $updatedFields['Company Name'] = ['old' => $company->name, 'new' => $request->name];
            $company->name = $request->name;
        }

        // Check and log specific changes to profile
        if ($company->profile->first_name != $request->first_name) {
            $updatedFields['First Name'] = ['old' => $company->profile->first_name, 'new' => $request->first_name];
        }
        if ($company->profile->last_name != $request->last_name) {
            $updatedFields['Last Name'] = ['old' => $company->profile->last_name, 'new' => $request->last_name];
        }
        if ($company->profile->contact_number != $request->contact_number) {
            $updatedFields['Contact Number'] = ['old' => $company->profile->contact_number, 'new' => $request->contact_number];
        }

        // Update profile for the contact person
        $company->profile->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'contact_number' => $request->contact_number,
        ]);

        // Handle MOA file upload
        if ($request->hasFile('moa_file')) {
            // Delete the old MOA file if it exists
            if ($company->profile->moa_file_path) {
                Storage::disk('public')->delete($company->profile->moa_file_path);
            }

            $moaPath = $request->file('moa_file')->store('moa_files', 'public');
            $company->profile->update(['moa_file_path' => $moaPath]);
            $updatedFields['MOA File'] = 'Updated';
        }

        // Check if email is updated
        if ($request->email != $company->email) {
            $updatedFields['email'] = 'Email Changed';
            $company->email = $request->email;

            // Auto-generate a new password if only the email is updated
            $newPassword = 'aufCCSInternshipCompany' . Str::random(5); // Generate new password
            $company->password = Hash::make($newPassword); // Hash the new password
            $updatedFields['password'] = 'Generated Neww';

            $sendEmail = true;
        }

        // Check if password is updated
        if ($request->filled('password')) {
            $newPassword = $request->password; // Use the password from the request
            $company->password = Hash::make($request->password); // Hash the new password
            $updatedFields['password'] = 'Manually Changed';

            $sendEmail = true;
        }

        // Update expiry date if provided
        if ($request->expiry_date != $company->expiry_date) {
            $updatedFields['Expiry Date'] = ['old' => $company->expiry_date, 'new' => $request->expiry_date];
            $company->expiry_date = $request->expiry_date;
        }


        $company->save();


        // Log the update with detailed changes
        ActivityLog::create([
            'admin_id' => Auth::id(),
            'action' => 'Updated Company',
            'target' => $company->name, // Company name as target
            'changes' => json_encode($updatedFields),
        ]);

        // Send email if email or password is updated
        if ($sendEmail) {
            if (isset($updatedFields['email']) && isset($updatedFields['password'])) {
                \Mail::to($company->email)->send(new \App\Mail\CompanyUpdateNotificationMail(
                    $company->name,
                    $company->email,
                    ['email', 'password'],
                    $newPassword
                ));
            } elseif (isset($updatedFields['email'])) {
                \Mail::to($company->email)->send(new \App\Mail\CompanyUpdateNotificationMail(
                    $company->name,
                    $company->email,
                    ['email', 'password'],
                    $newPassword
                ));
            } elseif (isset($updatedFields['password'])) {
                \Mail::to($company->email)->send(new \App\Mail\CompanyUpdateNotificationMail(
                    $company->name,
                    $company->email,
                    ['password'],
                    $newPassword
                ));
            }
        }


        return redirect()->route('company.index')->with('success', 'Company account updated successfully.');
    }

    public function destroy(User $company)
    {
        if ($company->role_id !== 4) {
            abort(404);
        }

        // Set the company's status to inactive instead of deleting
        $company->update(['status_id' => 2]); // 2 means Inactive

        // Log the deactivation
        ActivityLog::create([
            'admin_id' => Auth::id(),
            'action' => 'Deactivated Company',
            'target' => $company->name,
            'changes' => json_encode(['status' => 'Deactivated']),
        ]);

        return redirect()->route('company.index')->with('success', 'Company account deactivated successfully.');
    }

    public function reactivate(User $company)
    {
        // Set the company's status to active
        $company->update(['status_id' => 1]); // 1 means Active

        // Log the reactivation
        ActivityLog::create([
            'admin_id' => Auth::id(),
            'action' => 'Reactivated Company',
            'target' => $company->name,
            'changes' => json_encode(['status' => 'Reactivated']),
        ]);

        return redirect()->route('company.index')->with('success', 'Company account reactivated successfully.');
    }




    // Internship Listing Applicants
        // Intern Applications Page
    public function internApplications()
    {
        // Fetch jobs posted by the logged-in company
        $companyId = Auth::id();
        $jobs = Job::where('company_id', $companyId)->withCount('applications')->get();

        // Prepare data for the pie chart (applicants per job)
        $applicantsData = $jobs->map(function ($job) {
            return [
                'job_title' => $job->title,
                'applicants_count' => $job->nonAcceptedApplicationsCount(), // Use the new method here
                'accepted_count' => $job->acceptedApplicantsCount(), // Accepted applicants count
            ];
        });

        return view('company.intern_applications', compact('jobs', 'applicantsData'));
    }

    public function jobApplications($jobId)
    {
        $job = Job::with(['applications.student.profile.skillTags', 'applications.status'])->findOrFail($jobId);

        // Fetch status ID for 'For Interview'
        $interviewStatusId = ApplicationStatus::where('status', 'For Interview')->first()->id;

        // Fetch applicants scheduled for interview
        $interviewApplicants = $job->applications()->where('status_id', $interviewStatusId)->get();
    
        // Separate recommended and other applicants based on matching skills
        $recommendedApplicants = [];
        $otherApplicants = [];
    
        foreach ($job->applications as $application) {
            $studentSkills = $application->student->profile->skillTags->pluck('id')->toArray();
            $jobSkills = $job->skillTags->pluck('id')->toArray();
    
            $matchingSkills = array_intersect($studentSkills, $jobSkills);
    
            if (!empty($matchingSkills) && $application->status_id == 1) { // Status 1 is "To Review"
                $recommendedApplicants[] = $application;
            } elseif ($application->status_id == 1) {
                $otherApplicants[] = $application;
            }
        }
    
        return view('company.job_applications', compact('job', 'interviewApplicants', 'recommendedApplicants', 'otherApplicants'));
    }
    
    public function changeStatus(Request $request, $applicationId, $status)
    {
        $application = Application::findOrFail($applicationId);
        $statusId = ApplicationStatus::where('status', $status)->first()->id;
    
        $application->update(['status_id' => $statusId]);
    
        $job = $application->job; // Get the job associated with the application
    
        // If the status is "Accepted"
        if ($status === 'Accepted') {
            // Validate the start date
            $request->validate([
                'start_date' => 'required|date|after_or_equal:today',
            ]);
    
            // Decrease the number of available positions for the job
            if ($job->positions_available > 0) {
                $job->decrement('positions_available', 1); // Decrease by 1
            }
    
            // Decode the schedule JSON from the job
            $schedule = json_decode($job->schedule, true);
            
            // Convert time to 24-hour format for start_time and end_time
            $startTime = Carbon::createFromFormat('H:i', $schedule['start_time'])->format('H:i');
            $endTime = Carbon::createFromFormat('H:i', $schedule['end_time'])->format('H:i');
    
            // Store the accepted internship details in the new table
            AcceptedInternship::create([
                'student_id' => $application->student_id,
                'company_id' => $job->company_id,
                'job_id' => $job->id,
                'schedule' => $job->schedule, 
                'work_type' => $job->work_type,
                'start_time' => $startTime, // Use 24-hour format
                'end_time' => $endTime, // Use 24-hour format
                'start_date' => $request->start_date,
            ]);
    
            // Send an email notification to the accepted intern, including the start date, schedule, and time
            \Mail::to($application->student->email)->send(new \App\Mail\StudentAccepted(
                $application, 
                $request->start_date, 
                $schedule['days'], 
                $job->work_type, 
                $startTime, // Passing 24-hour start time
                $endTime // Passing 24-hour end time
            ));
        } elseif ($status === 'Rejected') {
            // Send an email notification to the rejected intern
            \Mail::to($application->student->email)->send(new \App\Mail\StudentRejected($application));
        }
    
        // Update the applicant count and notify
        $job->loadCount(['applications' => function($query) {
            $acceptedStatusId = ApplicationStatus::where('status', 'Accepted')->first()->id;
            $rejectedStatusId = ApplicationStatus::where('status', 'Rejected')->first()->id;
    
            $query->whereNotIn('status_id', [$acceptedStatusId, $rejectedStatusId]);
        }]);
    
        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    public function scheduleInterview(Request $request, $applicationId)
    {
        $request->validate([
            'interview_type' => 'required',
            'interview_datetime' => 'required|date|after_or_equal:now',
            'interview_link' => 'nullable|string',
            'message' => 'nullable|string',
        ]);

        $application = Application::findOrFail($applicationId);

        // Store interview details
        $interview = Interview::create([
            'application_id' => $application->id,
            'interview_type' => $request->interview_type,
            'interview_link' => $request->interview_link,
            'interview_datetime' => $request->interview_datetime,
            'message' => $request->message,
        ]);

        // Update status to "For Interview"
        $statusId = ApplicationStatus::where('status', 'For Interview')->first()->id;
        $application->update(['status_id' => $statusId]);

        // Send email notification to the student about the interview details
        Mail::to($application->student->email)->send(new \App\Mail\InterviewScheduled($application, $interview));

        return redirect()->back()->with('success', 'Interview scheduled successfully.');
    }

    public function interns()
    {
        // Fetch accepted interns from the accepted_internships table
        $acceptedInterns = AcceptedInternship::with(['student.profile', 'job'])
            ->where('company_id', Auth::id()) // Make sure to fetch only for the current company
            ->get();

        foreach ($acceptedInterns as $intern) {
            $student = $intern->student;
            $startDate = Carbon::parse($intern->start_date);

            // Determine if the student has a custom schedule for irregular students
            if ($student->profile->is_irregular && $intern->custom_schedule) {
                $schedule = $intern->custom_schedule;
                $scheduledDays = array_keys($schedule); // Extract days from keys
            } else {
                $schedule = json_decode($intern->schedule, true);

                if ($intern->work_type === 'Hybrid') {
                    // Combine onsite and remote days for hybrid schedules
                    $scheduledDays = array_merge($schedule['onsite_days'], $schedule['remote_days']);
                } else {
                    // Use regular days for On-site or Remote work types
                    $scheduledDays = $schedule['days'];
                }
            }

            // Fetch daily records and calculate total worked hours
            $dailyRecords = DailyTimeRecord::where('student_id', $student->id)->get();
            $totalWorkedHours = $dailyRecords->sum('total_hours_worked');

            // Fetch total internship hours for the student's course
            $internshipHours = InternshipHours::where('course_id', $student->course_id)->first();

            // Determine remaining hours
            $latestDailyRecord = $dailyRecords->last();
            $remainingHours = $latestDailyRecord ? $latestDailyRecord->remaining_hours : ($internshipHours->hours ?? 0);

            // Calculate estimated finish date
            $intern->estimatedFinishDate = $remainingHours > 0
                ? $this->calculateFinishDate($remainingHours, $startDate, $scheduledDays)
                : Carbon::now();
        }

        return view('company.interns', compact('acceptedInterns'));
    }

    private function calculateFinishDate($remainingHours, $startDate, $scheduledDays)
    {
        $estimatedDays = ceil($remainingHours / 8);
        $date = Carbon::now(); // Start from today
        $daysWorked = 0;

        while ($daysWorked < $estimatedDays) {
            if (in_array($date->format('l'), $scheduledDays)) {
                $daysWorked++;
            }
            $date->addDay();
        }

        return $date;
    }


    // Creating Accounts Via Excel Import
    // Method to show the upload form
    public function showImportForm()
    {
        return view('company.import-companies');
    }

    // Method to handle file upload and import companies
    public function uploadCompanies(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls',
        ]);

        try {
            Excel::import(new CompanyImport, $request->file('file'));

            return redirect()->route('company.index')->with('success', 'Companies imported successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing companies: ' . $e->getMessage());
        }
    }

    // Method to download the company template
    public function downloadTemplate()
    {
        $filePath = 'public/templates/company_template.xlsx';
        $fileName = 'company_template.xlsx';

        if (!Storage::exists($filePath)) {
            abort(404, 'Template file not found.');
        }

        return Storage::download($filePath, $fileName);
    }




}
