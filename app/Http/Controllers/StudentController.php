<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\User;
use App\Models\Profile;
use App\Models\Requirement;
use App\Models\Priority;
use App\Models\SkillTag;
use App\Models\Application;
use App\Models\ApplicationStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class StudentController extends Controller
{
    public function index()
    {
        return view('student.dashboard');
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
}
