<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Profile;
use App\Models\Requirement;
use App\Models\ActivityLog;
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
    
        $query = User::with('profile', 'course', 'requirements')
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

        // Apply requirements filter (completed, to complete, or no submission)
        if ($request->filled('requirements_status')) {
            if ($request->requirements_status == 'complete') {
                $query->whereHas('requirements', function ($q) {
                    $q->whereNotNull('waiver_form')
                    ->whereNotNull('medical_certificate')
                    ->where('status_id', 2); // 'Accepted'
                });
            } elseif ($request->requirements_status == 'incomplete') {
                $query->whereHas('requirements', function ($q) {
                    $q->whereNull('waiver_form')
                    ->orWhereNull('medical_certificate')
                    ->orWhere('status_id', '!=', 2); // Not 'Accepted'
                });
            } elseif ($request->requirements_status == 'no_submission') {
                $query->whereDoesntHave('requirements'); // Students with no submission
            }
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

        // Manually log the approval action
        ActivityLog::create([
            'admin_id' => $user->id,
            'action' => 'Approved Student Registration',
            'target' => $student->profile->first_name . ' ' . $student->profile->last_name, // Full name as target
            'changes' => json_encode(['status' => 'Approved']),
        ]);

        return redirect()->route('registrations.pending')->with('success', 'Student registration approved successfully.');
    }

    // Method to deactivate a student account
    public function deactivateStudent(User $student)
    {
        $student->update(['status_id' => 2]); // Set status to Inactive

        // Log the deactivation
        ActivityLog::create([
            'admin_id' => Auth::id(),
            'action' => 'Deactivated Student',
            'target' => $student->profile->first_name . ' ' . $student->profile->last_name, // Full name as target
            'changes' => json_encode(['status' => 'Deactivated']),
        ]);



        return redirect()->route('students.list')->with('success', 'Student account deactivated successfully.');
    }

    // Method to reactivate a student account
    public function reactivateStudent(User $student)
    {
        $student->update(['status_id' => 1]); // Set status to Active

        // Log the reactivation
        ActivityLog::create([
            'admin_id' => Auth::id(),
            'action' => 'Reactivated Student',
            'target' => $student->profile->first_name . ' ' . $student->profile->last_name, // Full name as target
            'changes' => json_encode(['status' => 'Reactivated']),
        ]);


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

        // Log the creation of the student
        ActivityLog::create([
            'admin_id' => Auth::id(),
            'action' => 'Created Student',
            'target' => $student->profile->first_name . ' ' . $student->profile->last_name, // Full name as target
            'changes' => json_encode([
                'name' => $student->name,
                'email' => $student->email,
                'course_id' => $student->course_id
            ]),
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
        $sendEmail = false;

        // Check and log specific changes
        if ($student->profile->first_name != $request->first_name) {
            $updatedFields['First Name'] = ['old' => $student->profile->first_name, 'new' => $request->first_name];
        }
        if ($student->profile->last_name != $request->last_name) {
            $updatedFields['Last Name'] = ['old' => $student->profile->last_name, 'new' => $request->last_name];
        }
        if ($student->profile->id_number != $request->id_number) {
            $updatedFields['ID'] = ['old' => $student->profile->id_number, 'new' => $request->id_number];
        }

        // Update profile
        $student->profile->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'id_number' => $request->id_number,
        ]);

        // Update course if changed and log the change with course code
        if ($request->course_id != $student->course_id) {
            $oldCourse = $student->course->course_code;
            $newCourse = \App\Models\Course::find($request->course_id); // Fetch the course details
            $updatedFields['course'] = ['old' => $student->course->course_code, 'new' => $newCourse->course_code];
            $student->course_id = $request->course_id;
        }

        // Check if email is updated
        if ($request->email != $student->email) {
            $updatedFields['email'] = 'Email Changed';
            $student->email = $request->email;

            // Generate a new password when email changes
            $newPassword = 'aufCCSInternship' . Str::random(5);
            $student->password = Hash::make($newPassword);
            $updatedFields['password'] = 'Generated New';

            // Set flag to send email with new email and password
            $sendEmail = true;
        }

        // Check if password is updated independently
        if ($request->filled('password')) {
            $newPassword = $request->password; // Use the password from the request
            $student->password = Hash::make($newPassword);
            $updatedFields['password'] = 'Manually Changed';

            // Set flag to send email with new password only
            $sendEmail = true;
        }

        // Save the student changes
        $student->save();

        // Log the update with proper target name and detailed changes
        ActivityLog::create([
            'admin_id' => Auth::id(),
            'action' => 'Updated Student',
            'target' => $student->profile->first_name . ' ' . $student->profile->last_name, // Full name as target
            'changes' => json_encode($updatedFields),
        ]);

        // Send email based on changes
        if ($sendEmail) {
            // Send email only if either email or password or both are updated
            if (isset($updatedFields['email']) && isset($updatedFields['password'])) {
                // Both email and password changed
                \Mail::to($student->email)->send(new \App\Mail\StudentUpdateNotificationMail(
                    $student->name,
                    $student->email,
                    ['email', 'password'], // Specify both in updated fields
                    $newPassword // Pass the new password
                ));
            } elseif (isset($updatedFields['email'])) {
                // Only email changed
                \Mail::to($student->email)->send(new \App\Mail\StudentUpdateNotificationMail(
                    $student->name,
                    $student->email,
                    ['email', 'password'], // Pass both email and new password
                    $newPassword
                ));
            } elseif (isset($updatedFields['password'])) {
                // Only password changed
                \Mail::to($student->email)->send(new \App\Mail\StudentUpdateNotificationMail(
                    $student->name,
                    $student->email,
                    ['password'], // Pass only password
                    $newPassword
                ));
            }
        }

        return redirect()->route('students.list')->with('success', 'Student account updated successfully.');
    }


}
