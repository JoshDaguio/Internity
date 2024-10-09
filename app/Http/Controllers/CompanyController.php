<?php

namespace App\Http\Controllers;
use App\Models\Job;
use App\Models\Application;
use App\Models\ApplicationStatus;
use App\Models\AcceptedInternship;
use App\Models\StudentAccepted;
use App\Models\StudentRejected;
use App\Models\Interview;
use App\Models\User;
use App\Models\Profile;
use App\Models\ActivityLog;
use App\Mail\CompanyApprovalMail;
use App\Mail\InterviewScheduled; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;



use Illuminate\Http\Request;

class CompanyController extends Controller
{

    public function dashboard()
    {
        // You can pass any necessary data to the dashboard view
        return view('company.dashboard');
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
            'expiry_date' => 'required|date|after_or_equal:today', 
        ]);

        // Create profile for the contact person
        $profile = Profile::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'id_number' => null, // No ID number for companies
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
            'expiry_date' => 'required|date|after_or_equal:today', 
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

        // Update profile for the contact person
        $company->profile->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
        ]);

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

        return view('company.interns', compact('acceptedInterns'));
    }


}
