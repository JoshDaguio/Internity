<?php

namespace App\Http\Controllers;

use App\Models\Job;
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
        return view('jobs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'positions_available' => 'required|integer|min:1',
            'location' => 'required|string|max:255',
            'work_type' => 'required|string',
            'schedule' => 'required|string',
            'description' => 'required|string',
            'qualification' => 'required|string',
            'preferred_skills' => 'required|string',
        ]);

        Job::create(array_merge($request->all(), ['company_id' => Auth::id()]));

        return redirect()->route('jobs.index');
    }

    public function show(Job $job)
    {
        return view('jobs.show', compact('job'));
    }

    public function edit(Job $job)
    {
        return view('jobs.edit', compact('job'));
    }

    public function update(Request $request, Job $job)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'positions_available' => 'required|integer|min:1',
            'location' => 'required|string|max:255',
            'work_type' => 'required|string',
            'schedule' => 'required|string',
            'description' => 'required|string',
            'qualification' => 'required|string',
            'preferred_skills' => 'required|string',
        ]);

        $job->update($request->all());

        return redirect()->route('jobs.index');
    }

    public function destroy(Job $job)
    {
        $job->delete();
        return redirect()->route('jobs.index');
    }
}
