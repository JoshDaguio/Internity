<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FacultyController extends Controller
{

    public function dashboard()
    {
        return view('faculty.dashboard');
    }

    public function index(Request $request)
    {
        $query = User::where('role_id', 3); // Ensure we're querying only faculty accounts

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }
    
        $faculties = $query->get();
        $courses = Course::all();
    
        return view('faculty.index', compact('faculties', 'courses'));
    }

    public function create()
    {
        $courses = Course::all();
        return view('faculty.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'id_number' => ['required', 'string', 'max:255', 'unique:profiles'],
            'course_id' => ['required', 'exists:courses,id'],
        ]);

        // Create profile
        $profile = Profile::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'id_number' => $request->id_number,
        ]);

        // Create faculty account
        User::create([
            'name' => $request->first_name,
            'email' => $request->email,
            'password' => Hash::make('aufCCSInternshipFaculty'), // Default password for faculty
            'role_id' => 3, // Faculty role
            'status_id' => 1, // Active status
            'profile_id' => $profile->id,
            'course_id' => $request->course_id,
        ]);

        return redirect()->route('faculty.index')->with('success', 'Faculty account created successfully.');
    }

    public function show(User $faculty)
    {
        // Ensure we are showing a faculty user (role_id = 3)
        if ($faculty->role_id !== 3) {
            abort(404);
        }
        return view('faculty.show', compact('faculty'));
    }

    public function edit(User $faculty)
    {
        $courses = Course::all(); // Get all courses for dropdown
        return view('faculty.edit', compact('faculty', 'courses'));
    }   


    public function update(Request $request, User $faculty)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $faculty->id],
            'id_number' => ['required', 'string', 'max:255', 'unique:profiles,id_number,' . $faculty->profile_id],
            'password' => ['nullable', 'string', 'min:8'], // Removed the 'confirmed' rule
            'course_id' => ['required', 'exists:courses,id'],
        ]);
    
        // Update profile
        $faculty->profile->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'id_number' => $request->id_number,
        ]);
    
        // Update faculty account
        $faculty->update([
            'name' => $request->first_name,
            'email' => $request->email,
            'course_id' => $request->course_id,
        ]);
    
        // If password is provided, update it
        if ($request->filled('password')) {
            $faculty->password = Hash::make($request->password);
            $faculty->save();
        }
    
        return redirect()->route('faculty.index')->with('success', 'Faculty account updated successfully.');
    }

    public function destroy(User $faculty)
    {
        // Set the faculty's status to inactive instead of deleting
        $faculty->update(['status_id' => 2]); // Status 2 is Inactive

        return redirect()->route('faculty.index')->with('success', 'Faculty account deactivated successfully.');
    }

    public function reactivate(User $faculty)
    {
        // Set the faculty's status to active
        $faculty->update(['status_id' => 1]); // Status 1 is Active

        return redirect()->route('faculty.index')->with('success', 'Faculty account reactivated successfully.');
    }
    
}
