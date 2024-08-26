<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
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
        $query = User::where('role_id', 3);

        // Filter by course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by whether a faculty has a course assigned
        if ($request->has('no_course')) {
            $query->whereNull('course_id');
        } elseif ($request->has('has_course')) {
            $query->whereNotNull('course_id');
        }

        // Sorting
        if ($request->filled('sort_by') && in_array($request->sort_by, ['name', 'email'])) {
            $query->orderBy($request->sort_by, $request->get('order', 'asc'));
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'course_id' => 'required|exists:courses,id',
        ]);

        // Create a new faculty member in the 'users' table
        $faculty = new User();
        $faculty->name = $request->name;
        $faculty->email = $request->email;
        $faculty->password = Hash::make($request->password); // Store hashed password
        $faculty->role_id = 3; // Set role_id to 3 for faculty
        $faculty->status_id = 1; // Assuming 1 is the default active status in the 'account_statuses' table
        $faculty->course_id = $request->course_id;
        $faculty->save();

        return redirect()->route('faculty.index')->with('success', 'Faculty created successfully.');
    }

    public function show(User $faculty)
    {
        // Ensure we are showing a faculty user (role_id = 3)
        if ($faculty->role_id !== 3) {
            abort(404);
        }
        return view('faculty.show', compact('faculty'));
    }

    public function edit($id)
    {
        $faculty = User::where('role_id', 3)->findOrFail($id);
        $courses = Course::all();  // This will work now
        return view('faculty.edit', compact('faculty', 'courses'));
    }   


    public function update(Request $request, User $faculty)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $faculty->id,
            'password' => 'nullable|string|min:8',
            'course_id' => 'required|exists:courses,id',
        ]);

        // Update the faculty member in the 'users' table
        $faculty->name = $request->name;
        $faculty->email = $request->email;
        if ($request->filled('password')) {
            $faculty->password = Hash::make($request->password);
        }
        $faculty->course_id = $request->course_id;
        $faculty->save();

        return redirect()->route('faculty.index')->with('success', 'Faculty updated successfully.');
    }

    public function destroy(User $faculty)
    {
        // Ensure we are deleting a faculty user (role_id = 3)
        if ($faculty->role_id !== 3) {
            abort(404);
        }

        // Delete the faculty member from the 'users' table
        $faculty->delete();

        return redirect()->route('faculty.index')->with('success', 'Faculty deleted successfully.');
    }

    
}
