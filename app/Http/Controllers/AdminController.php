<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    // Method to show pending student registrations
    public function pendingRegistrations()
    {
        $pendingRegistrations = User::where('role_id', 5)->where('status_id', 2)->get();
        return view('administrative.pending-registrations', compact('pendingRegistrations'));
    }

    // Method to show approved students
    public function approvedStudents()
    {
        $approvedStudents = User::where('role_id', 5)->where('status_id', 1)->get();
        return view('administrative.student-list', compact('approvedStudents'));
    }

    // Method to approve a student registration
    public function approveRegistration($userId)
    {
        $user = User::findOrFail($userId);
        $user->status_id = 1; // Set status to active
        $user->save();

        return redirect()->route('registrations.pending')->with('success', 'Student registration approved successfully.');
    }
}
