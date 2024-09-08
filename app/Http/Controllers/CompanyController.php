<?php

namespace App\Http\Controllers;
use App\Models\Job;
use App\Models\Application;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


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

        // Create company account
        User::create([
            'name' => $request->name, // Company name
            'email' => $request->email,
            'password' => Hash::make('aufCCSInternshipCompany'), // Default password for companies
            'role_id' => 4, // Company role
            'status_id' => 1, // Active status
            'profile_id' => $profile->id,
        ]);

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

        // Update profile for the contact person
        $company->profile->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
        ]);

        // Update company account
        $company->update([
            'name' => $request->name, // Company name
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $company->password = Hash::make($request->password);
            $company->save();
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
