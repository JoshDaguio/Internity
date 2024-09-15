<?php

namespace App\Http\Controllers;
use App\Models\Job;
use App\Models\Application;
use App\Models\User;
use App\Models\Profile;
use App\Mail\CompanyApprovalMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


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
        ]);

        $updatedFields = [];
        $newPassword = null; // To store the new auto-generated password

        // Update profile for the contact person
        $company->profile->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
        ]);

        // Check if email is updated
        if ($request->email != $company->email) {
            $company->email = $request->email;
            $updatedFields[] = 'email';

            // Auto-generate a new password if only the email is updated
            $newPassword = 'aufCCSInternshipCompany' . Str::random(5); // Generate new password
            $company->password = Hash::make($newPassword); // Hash the new password
            $updatedFields[] = 'password';
        }

        // Check if password is updated
        if ($request->filled('password')) {
            $newPassword = $request->password; // Use the password from the request
            $company->password = Hash::make($request->password); // Hash the new password
            $updatedFields[] = 'password';
        }

        $company->save();

        // Send email if either email or password is updated
        if (!empty($updatedFields)) {
            \Mail::to($company->email)->send(new \App\Mail\CompanyUpdateNotificationMail(
                $company->name,
                $company->email,
                $updatedFields,
                $newPassword // Pass the new password if updated
            ));
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

        return redirect()->route('company.index')->with('success', 'Company account deactivated successfully.');
    }

    public function reactivate(User $company)
    {
        // Set the company's status to active
        $company->update(['status_id' => 1]); // 1 means Active

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
                'applicants_count' => $job->applications_count,
            ];
        });

        return view('company.intern_applications', compact('jobs', 'applicantsData'));
    }

    public function jobApplications($jobId)
    {
        $job = Job::with(['applications.student.profile.skillTags', 'applications.status'])->findOrFail($jobId);
    
        // Separate recommended and other applicants based on matching skills
        $recommendedApplicants = [];
        $otherApplicants = [];
    
        foreach ($job->applications as $application) {
            $studentSkills = $application->student->profile->skillTags->pluck('id')->toArray();
            $jobSkills = $job->skillTags->pluck('id')->toArray();
    
            $matchingSkills = array_intersect($studentSkills, $jobSkills);
    
            if (!empty($matchingSkills)) {
                $recommendedApplicants[] = $application;
            } else {
                $otherApplicants[] = $application;
            }
        }
    
        return view('company.job_applications', compact('job', 'recommendedApplicants', 'otherApplicants'));
    }
    


}
