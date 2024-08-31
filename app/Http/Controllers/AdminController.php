<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    // Method to show pending student registrations
    public function pendingRegistrations(Request $request)
    {
        $user = Auth::user();
    
        $pendingRegistrationsQuery = User::with('profile', 'course')
            ->where('role_id', 5) // Students
            ->where('status_id', 3); // Pending
    
        // Apply Course Filter
        if ($request->has('course_id') && $request->course_id != '') {
            $pendingRegistrationsQuery->where('course_id', $request->course_id);
        }
    
        // If user is Faculty, filter by their course_id
        if ($user->role_id == 3) {
            $pendingRegistrationsQuery->where('course_id', $user->course_id);
        }
    
        $pendingRegistrations = $pendingRegistrationsQuery->get();
        
        $courses = \App\Models\Course::all();
    
        return view('administrative.pending-registrations', compact('pendingRegistrations', 'courses'));
    }

    // Method to show approved students
    public function approvedStudents(Request $request)
    {
        $user = Auth::user();
    
        $query = User::with('profile', 'course')
            ->where('role_id', 5); // Students
    
        // Apply status filter if provided
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        // Apply course filter if provided
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
    
        // If user is Faculty, filter by their course_id
        if ($user->role_id == 3) {
            $query->where('course_id', $user->course_id);
        }
    
        $approvedStudents = $query->get();
    
        // Get all courses for filtering purposes
        $courses = \App\Models\Course::all();
    
        return view('administrative.student-list', compact('approvedStudents', 'courses'));
    }

    // Method to approve a student registration
    public function approveRegistration($userId)
    {
        $user = Auth::user();
        $student = User::with('profile', 'course')->findOrFail($userId);

        // Check if Faculty is approving student from their course
        if ($user->role_id == 3 && $student->course_id != $user->course_id) {
            return redirect()->route('registrations.pending')->with('error', 'You can only approve students from your own course.');
        }

        $student->status_id = 1; // Set status to active
        $student->save();

        return redirect()->route('registrations.pending')->with('success', 'Student registration approved successfully.');
    }

    // Method to deactivate a student account
    public function deactivateStudent(User $student)
    {
        $student->update(['status_id' => 2]); // Set status to Inactive
        return redirect()->route('students.list')->with('success', 'Student account deactivated successfully.');
    }

    // Method to reactivate a student account
    public function reactivateStudent(User $student)
    {
        $student->update(['status_id' => 1]); // Set status to Active
        return redirect()->route('students.list')->with('success', 'Student account reactivated successfully.');
    }

    // Method to show student details
    public function showStudent(User $student)
    {
        return view('administrative.show-student', compact('student'));
    }

    // Method to edit a student account
    public function editStudent(User $student)
    {
        $courses = \App\Models\Course::all();
        return view('administrative.edit-student', compact('student', 'courses'));
    }

    // Method to update a student account
    public function updateStudent(Request $request, User $student)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $student->id],
            'id_number' => ['required', 'string', 'max:255', 'unique:profiles,id_number,' . $student->profile_id],
            'password' => ['nullable', 'string', 'min:8'],
            'course_id' => ['required', 'exists:courses,id'],
        ]);

        // Update profile
        $student->profile->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'id_number' => $request->id_number,
        ]);

        // Update student account
        $student->update([
            'email' => $request->email,
            'password' => $request->filled('password') ? Hash::make($request->password) : $student->password,
            'course_id' => $request->course_id,
        ]);

        return redirect()->route('students.list')->with('success', 'Student account updated successfully.');
    }

}
