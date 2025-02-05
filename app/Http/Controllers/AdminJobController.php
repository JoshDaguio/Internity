<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\User;
use App\Models\AcceptedInternship;
use App\Models\Application;
use App\Models\SkillTag;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminJobController extends Controller
{
    public function index()
    {
        // Retrieve all job listings and calculate the number of applicants and active interns
        $jobs = Job::with('company', 'applications')->get();

        return view('administrative.jobs.index', compact('jobs'));
    }

    public function create()
    {
        // Fetch users with the company role (role_id 4 is assumed for companies)
        $companies = User::where('role_id', 4)->get();
        $skillTags = SkillTag::all(); // Fetch all available skill tags
        return view('administrative.jobs.create', compact('companies', 'skillTags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'positions_available' => 'required|integer|min:1',
            'location' => 'required|string|max:255',
            'work_type' => 'required|string',
            'company_id' => 'required|exists:users,id', // Validate that the company exists
            'description' => 'required|string',
            'qualification' => 'required|string',
            'skill_tags' => 'nullable|array',
        ]);

        $schedule = [
            'days' => $request->schedule_days ?? [], // Default for Remote or On-site
            'onsite_days' => $request->onsite_days ?? [], // For Hybrid jobs
            'remote_days' => $request->remote_days ?? [], // For Hybrid jobs
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ];

        $job = Job::create([
            'company_id' => $request->company_id,
            'title' => $request->title,
            'industry' => $request->industry,
            'positions_available' => $request->positions_available,
            'location' => $request->location,
            'work_type' => $request->work_type,
            'schedule' => json_encode($schedule), // Save schedule as JSON
            'description' => $request->description,
            'qualification' => $request->qualification,
        ]);

        // Sync skill tags with the job
        $job->skillTags()->sync($request->input('skill_tags', []));

        return redirect()->route('admin.jobs.index')->with('success', 'Job created successfully.');
    }

    public function edit(Job $job)
    {
        $companies = User::where('role_id', 4)->get(); // Fetch all companies
        $skillTags = SkillTag::all(); // Fetch all available skill tags
        return view('administrative.jobs.edit', compact('job', 'companies', 'skillTags'));
    }

    public function update(Request $request, Job $job)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'positions_available' => 'required|integer|min:1',
            'location' => 'required|string|max:255',
            'work_type' => 'required|string',
            'company_id' => 'required|exists:users,id',
            'description' => 'required|string',
            'qualification' => 'required|string',
            'skill_tags' => 'nullable|array',
        ]);

        $schedule = [
            'days' => $request->schedule_days ?? [],
            'onsite_days' => $request->onsite_days ?? [],
            'remote_days' => $request->remote_days ?? [],
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ];

        $job->update([
            'company_id' => $request->company_id,
            'title' => $request->title,
            'industry' => $request->industry,
            'positions_available' => $request->positions_available,
            'location' => $request->location,
            'work_type' => $request->work_type,
            'schedule' => json_encode($schedule),
            'description' => $request->description,
            'qualification' => $request->qualification,
        ]);

        // Sync skill tags with the job
        $job->skillTags()->sync($request->input('skill_tags', []));
        
        // Call the new function to update accepted internships
        $this->updateAcceptedInternships($job);

        return redirect()->route('admin.jobs.index')->with('success', 'Job updated successfully.');
    }

    public function updateAcceptedInternships(Job $job)
    {
        // Fetch all accepted internships for this job
        $acceptedInternships = AcceptedInternship::where('job_id', $job->id)->get();

        foreach ($acceptedInternships as $internship) {
            // Decode the schedule from the job to update in accepted internship
            // Decode the schedule JSON from the job
            $schedule = json_decode($job->schedule, true);

            // Convert time to 24-hour format for start_time and end_time
            $startTime = Carbon::createFromFormat('H:i', $schedule['start_time'])->format('H:i');
            $endTime = Carbon::createFromFormat('H:i', $schedule['end_time'])->format('H:i');

            // Now update the schedule for the accepted internship as you need it
            $internship->update([
                'schedule' => $job->schedule, 
                'work_type' => $job->work_type,
                'start_time' => $startTime, // Use 24-hour format
                'end_time' => $endTime, // Use 24-hour format
            ]);
        }
    }


    public function destroy(Job $job)
    {
        $job->delete();
        return redirect()->route('admin.jobs.index')->with('success', 'Job deleted successfully.');
    }
}
