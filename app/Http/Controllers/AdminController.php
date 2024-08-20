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
    public function pendingRegistrations()
    {
        $user = Auth::user();
        
        $pendingRegistrations = User::with('profile', 'course')
            ->where('role_id', 5) // Students
            ->where('status_id', 2); // Pending/Inactivated

        // If user is Faculty, filter by their course_id
        if ($user->role_id == 3) {
            $pendingRegistrations->where('course_id', $user->course_id);
        }

        $pendingRegistrations = $pendingRegistrations->get();

        return view('administrative.pending-registrations', compact('pendingRegistrations'));
    }

    // Method to show approved students
    public function approvedStudents()
    {
        $user = Auth::user();
        
        $approvedStudents = User::with('profile', 'course')
            ->where('role_id', 5) // Students
            ->where('status_id', 1); // Active

        // If user is Faculty, filter by their course_id
        if ($user->role_id == 3) {
            $approvedStudents->where('course_id', $user->course_id);
        }

        $approvedStudents = $approvedStudents->get();

        return view('administrative.student-list', compact('approvedStudents'));
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
}
