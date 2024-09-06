<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\SkillTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    public function index()
    {
        // Retrieve jobs for the logged-in company or all jobs for Super Admin and Admin
        $jobs = (Auth::user()->role_id === 4) // Company
            ? Job::where('company_id', Auth::id())->get()
            : Job::all();

        return view('jobs.index', compact('jobs'));
    }

    public function create()
    {
        $skillTags = SkillTag::all(); // Retrieve all skill tags for selection
        return view('jobs.create', compact('skillTags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'positions_available' => 'required|integer|min:1',
            'location' => 'required|string|max:255',
            'work_type' => 'required|string',
            'schedule_days' => 'nullable|array',
            'onsite_days' => 'nullable|array',
            'remote_days' => 'nullable|array',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'description' => 'required|string',
            'qualification' => 'required|string',
            'skill_tags' => 'nullable|array',
        ]);
    
        $schedule = [
            'days' => $request->schedule_days ?? [],  // For Remote or On-site jobs
            'onsite_days' => $request->onsite_days ?? [], // For Hybrid jobs
            'remote_days' => $request->remote_days ?? [], // For Hybrid jobs
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ];
    
        // Check if the job is Hybrid and validate that both onsite and remote days are provided
        if ($request->work_type === 'Hybrid') {
            $request->validate([
                'onsite_days' => 'required|array',
                'remote_days' => 'required|array',
            ]);
        }
    
        $job = Job::create([
            'company_id' => Auth::id(),
            'title' => $request->title,
            'industry' => $request->industry,
            'positions_available' => $request->positions_available,
            'location' => $request->location,
            'work_type' => $request->work_type,
            'schedule' => json_encode($schedule),
            'description' => $request->description,
            'qualification' => $request->qualification,
        ]);

        $job->skillTags()->sync($request->input('skill_tags', [])); // Sync selected skill tags

        return redirect()->route('jobs.index');
    }

    public function show(Job $job)
    {
        return view('jobs.show', compact('job'));
    }

    public function edit(Job $job)
    {
        $skillTags = SkillTag::all(); // Retrieve all skill tags for selection
        return view('jobs.edit', compact('job', 'skillTags'));
    }

    public function update(Request $request, Job $job)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'positions_available' => 'required|integer|min:1',
            'location' => 'required|string|max:255',
            'work_type' => 'required|string',
            'schedule_days' => 'nullable|array',
            'onsite_days' => 'nullable|array',
            'remote_days' => 'nullable|array',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
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
    
        // Validate Hybrid job schedule
        if ($request->work_type === 'Hybrid') {
            $request->validate([
                'onsite_days' => 'required|array',
                'remote_days' => 'required|array',
            ]);
        }
    
        $job->update([
            'title' => $request->title,
            'industry' => $request->industry,
            'positions_available' => $request->positions_available,
            'location' => $request->location,
            'work_type' => $request->work_type,
            'schedule' => json_encode($schedule),
            'description' => $request->description,
            'qualification' => $request->qualification,
        ]);
        $job->skillTags()->sync($request->input('skill_tags', []));
    
        return redirect()->route('jobs.index');
    }

    public function destroy(Job $job)
    {
        $job->delete();
        return redirect()->route('jobs.index');
    }
}
