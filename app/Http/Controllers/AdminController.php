<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Profile;
use App\Mail\StudentApprovalMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


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

        // Fetch all pending registrations for progress bars, regardless of the filter
        $allPendingRegistrations = User::with('profile', 'course')
            ->where('role_id', 5)
            ->where('status_id', 3)
            ->get();
        
        $courses = \App\Models\Course::all();
    
        return view('administrative.pending-registrations', compact('pendingRegistrations', 'allPendingRegistrations', 'courses'));
    }

    // Method to show approved students
    public function approvedStudents(Request $request)
    {
        $user = Auth::user();
    
        $query = User::with('profile', 'course')
            ->where('role_id', 5) // Students
            ->where('status_id', '!=', 3); // Exclude pending students (status_id = 3)
    
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

        // Generate a random password (update if password was changed)
        $password = 'aufCCSInternship' . \Str::random(5);

        // Hash the password and update the student user
        $student->update(['password' => Hash::make($password)]);

        // Send approval email
        \Mail::to($student->email)->send(new \App\Mail\StudentApprovalMail(
            $student->name,
            $student->email,
            $password,
            $student->course->course_name
        ));

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

    // Method to show the form for creating a new student
    public function createStudent()
    {
        $courses = Course::all();
        return view('administrative.create-student', compact('courses'));
    }

    // Method to store a new student
    public function storeStudent(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'id_number' => ['required', 'string', 'max:255', 'unique:profiles,id_number'],
            'course_id' => ['required', 'exists:courses,id'],
        ]);

        // Create profile
        $profile = Profile::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'id_number' => $request->id_number,
        ]);

        // Generate a random password
        $password = 'aufCCSInternship' . Str::random(5);

        // Create student account
        $student = User::create([
            'name' => $request->first_name,
            'email' => $request->email,
            'password' => Hash::make($password), // Use the generated password
            'role_id' => 5, // Student role
            'status_id' => 1, // Active status
            'profile_id' => $profile->id,
            'course_id' => $request->course_id,
        ]);

        // Send the email with the login details
        \Mail::to($student->email)->send(new \App\Mail\StudentApprovalMail(
            $student->name,
            $student->email,
            $password,
            $student->course->course_name
        ));

        return redirect()->route('students.list')->with('success', 'Student account created successfully.');
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

        $updatedFields = [];
        $newPassword = null;

        // Update profile
        $student->profile->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'id_number' => $request->id_number,
        ]);

        // Check if email is updated
        if ($request->email != $student->email) {
            $student->email = $request->email;
            $updatedFields[] = 'email';

            // Auto-generate a new password if only the email is updated
            $newPassword = 'aufCCSInternship' . Str::random(5);
            $student->password = Hash::make($newPassword);
            $updatedFields[] = 'password';
        }

        // Check if password is updated
        if ($request->filled('password')) {
            $newPassword = $request->password; // Use the password from the request
            $student->password = Hash::make($request->password);
            $updatedFields[] = 'password';
        }

        $student->save();

        // Send email if either email or password is updated
        if (!empty($updatedFields)) {
            \Mail::to($student->email)->send(new \App\Mail\StudentUpdateNotificationMail(
                $student->name,
                $student->email,
                $updatedFields,
                $newPassword
            ));
        }

        return redirect()->route('students.list')->with('success', 'Student account updated successfully.');
    }

}
