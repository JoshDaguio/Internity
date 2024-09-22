<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\Profile;
use App\Models\ActivityLog;
use App\Mail\FacultyApprovalMail;
use App\Mail\FacultyUpdateNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

        // Generate a random password with "aufCCSInternshipFaculty" + 5 random characters
        $password = 'aufCCSInternshipFaculty' . Str::random(5);

        // Create faculty account
        $faculty = User::create([
            'name' => $request->first_name,
            'email' => $request->email,
            'password' => Hash::make($password), // Store hashed password
            'role_id' => 3, // Faculty role
            'status_id' => 1, // Active status
            'profile_id' => $profile->id,
            'course_id' => $request->course_id,
        ]);

        // Log the creation of the faculty account
        ActivityLog::create([
            'admin_id' => Auth::id(),
            'action' => 'Created Faculty',
            'target' => $faculty->profile->first_name . ' ' . $faculty->profile->last_name, // Full name as target
            'changes' => json_encode([
                'name' => $faculty->name,
                'email' => $faculty->email,
                'course' => Course::find($request->course_id)->course_code // Log course code
            ]),
        ]);

        // Send the email with login details
        \Mail::to($faculty->email)->send(new \App\Mail\FacultyApprovalMail(
            $faculty->name,
            $faculty->email,
            $password,
            $faculty->course->course_name
        ));

        return redirect()->route('faculty.index')->with('success', 'Faculty account created successfully.');
    }

    public function show(User $faculty)
    {
        // Ensure we are showing a faculty user (role_id = 3)
        if ($faculty->role_id !== 3) {
            abort(404);
        }

        // Fetch logs for the faculty user
        $logs = ActivityLog::where('admin_id', $faculty->id)->latest()->get();
        
        return view('faculty.show', compact('faculty', 'logs'));
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
            'password' => ['nullable', 'string', 'min:8'],
            'course_id' => ['required', 'exists:courses,id'],
        ]);
    
        $updatedFields = [];
        $newPassword = null;
        $sendEmail = false;

        // Check and log specific changes to profile (first name, last name, id number)
        if ($faculty->profile->first_name != $request->first_name) {
            $updatedFields['First Name'] = ['old' => $faculty->profile->first_name, 'new' => $request->first_name];
        }
        if ($faculty->profile->last_name != $request->last_name) {
            $updatedFields['Last Name'] = ['old' => $faculty->profile->last_name, 'new' => $request->last_name];
        }
        if ($faculty->profile->id_number != $request->id_number) {
            $updatedFields['ID Number'] = ['old' => $faculty->profile->id_number, 'new' => $request->id_number];
        }
    
        // Update profile
        $faculty->profile->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'id_number' => $request->id_number,
        ]);
    
        // Check if course is updated and log the change
        if ($request->course_id != $faculty->course_id) {
            $oldCourse = $faculty->course->course_code;
            $newCourse = Course::find($request->course_id); // Fetch course details
            $updatedFields['Course'] = ['old' => $oldCourse, 'new' => $newCourse->course_code];
            $faculty->course_id = $request->course_id;
        }
    
        // Check if email is updated
        if ($request->email != $faculty->email) {
            $updatedFields['email'] = 'Changed';
            $faculty->email = $request->email;
    
            // Auto-generate a new password if only the email is updated
            $newPassword = 'aufCCSInternshipFaculty' . Str::random(5);
            $faculty->password = Hash::make($newPassword);
            $updatedFields['password'] = 'Generated New';

            $sendEmail = true;
        }
    
        // Check if password is updated
        if ($request->filled('password')) {
            $newPassword = $request->password; // Use the password from the request
            $faculty->password = Hash::make($newPassword);
            $updatedFields['password'] = 'Manually Changed';

            $sendEmail = true;
        }
    
        $faculty->save();

        // Log the update with proper target name and detailed changes
        ActivityLog::create([
            'admin_id' => Auth::id(),
            'action' => 'Updated Faculty',
            'target' => $faculty->profile->first_name . ' ' . $faculty->profile->last_name, // Full name as target
            'changes' => json_encode($updatedFields),
        ]);
    
        // Send email based on changes
        if ($sendEmail) {
            if (isset($updatedFields['email']) && isset($updatedFields['password'])) {
                // Both email and password changed
                \Mail::to($faculty->email)->send(new \App\Mail\FacultyUpdateNotificationMail(
                    $faculty->name,
                    $faculty->email,
                    ['email', 'password'], // Specify both in updated fields
                    $newPassword // Pass the new password
                ));
            } elseif (isset($updatedFields['email'])) {
                // Only email changed
                \Mail::to($faculty->email)->send(new \App\Mail\FacultyUpdateNotificationMail(
                    $faculty->name,
                    $faculty->email,
                    ['email', 'password'], // Pass both email and new password
                    $newPassword
                ));
            } elseif (isset($updatedFields['password'])) {
                // Only password changed
                \Mail::to($faculty->email)->send(new \App\Mail\FacultyUpdateNotificationMail(
                    $faculty->name,
                    $faculty->email,
                    ['password'], // Pass only password
                    $newPassword
                ));
            }
        }
    
        return redirect()->route('faculty.index')->with('success', 'Faculty account updated successfully.');
    }

    public function destroy(User $faculty)
    {
        // Set the faculty's status to inactive instead of deleting
        $faculty->update(['status_id' => 2]); // Status 2 is Inactive

        // Log the deactivation
        ActivityLog::create([
            'admin_id' => Auth::id(),
            'action' => 'Deactivated Faculty',
            'target' => $faculty->profile->first_name . ' ' . $faculty->profile->last_name, // Full name as target
            'changes' => json_encode(['status' => 'Deactivated']),
        ]);

        return redirect()->route('faculty.index')->with('success', 'Faculty account deactivated successfully.');
    }

    public function reactivate(User $faculty)
    {
        // Set the faculty's status to active
        $faculty->update(['status_id' => 1]); // Status 1 is Active
        
        // Log the reactivation
        ActivityLog::create([
            'admin_id' => Auth::id(),
            'action' => 'Reactivated Faculty',
            'target' => $faculty->profile->first_name . ' ' . $faculty->profile->last_name, // Full name as target
            'changes' => json_encode(['status' => 'Reactivated']),
        ]);

        return redirect()->route('faculty.index')->with('success', 'Faculty account reactivated successfully.');
    }
    
}
