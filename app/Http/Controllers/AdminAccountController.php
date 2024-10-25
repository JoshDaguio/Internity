<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use App\Models\Course;
use App\Models\ActivityLog;
use App\Mail\AdminApprovalMail;
use App\Mail\AdminUpdateNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str; 

class AdminAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role_id', 2); // Fetch only admin accounts

        // Apply status filter
        if ($request->has('status')) {
            if ($request->status == 'active') {
                $query->where('status_id', 1);
            } elseif ($request->status == 'inactive') {
                $query->where('status_id', 2);
            }
        }
    
        $admins = $query->get();
        return view('super_admin.admin-accounts.index', compact('admins'));
    }

    public function create()
    {
        return view('super_admin.admin-accounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'id_number' => ['required', 'string', 'max:255', 'unique:profiles'],
        ]);

        // Create profile
        $profile = Profile::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'id_number' => $request->id_number,
        ]);

        // Generate a random password with "aufCCSInternshipAdmin" + 5 random characters
        $password = 'aufCCSInternshipAdmin' . Str::random(5);

        // Create admin account
        $admin = User::create([
            'name' => $request->first_name,
            'email' => $request->email,
            'password' => Hash::make($password), // Store hashed password
            'role_id' => 2, // Admin role
            'status_id' => 1, // Active status
            'profile_id' => $profile->id,
        ]);

        // Send the email with login details
        \Mail::to($admin->email)->send(new \App\Mail\AdminApprovalMail(
            $admin->name,
            $admin->email,
            $password
        ));

        return redirect()->route('admin-accounts.index')->with('success', 'Admin account created successfully.');
    }

    // Method to show admin details
    public function show(User $admin)
    {
        $courses = Course::all();

        // Make sure the user is an admin
        if ($admin->role_id !== 2) {
            return redirect()->route('admin-accounts.index')->with('error', 'User is not an admin.');
        }
        $logs = ActivityLog::where('admin_id', $admin->id)->latest()->get();

        return view('super_admin.admin-accounts.show', compact('admin', 'logs', 'courses'));
    }

    public function edit(User $admin)
    {
        return view('super_admin.admin-accounts.edit', compact('admin'));
    }

    public function update(Request $request, User $admin)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $admin->id],
            'id_number' => ['required', 'string', 'max:255', 'unique:profiles,id_number,' . $admin->profile_id],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $updatedFields = [];
        $newPassword = null;

        // Update profile
        $admin->profile->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'id_number' => $request->id_number,
        ]);

        // Check if email is updated
        if ($request->email != $admin->email) {
            $admin->email = $request->email;
            $updatedFields[] = 'email';

            // Auto-generate a new password if only the email is updated
            $newPassword = 'aufCCSInternshipAdmin' . Str::random(5);
            $admin->password = Hash::make($newPassword);
            $updatedFields[] = 'password';
        }

        // Check if password is updated
        if ($request->filled('password')) {
            $newPassword = $request->password; // Use the password from the request
            $admin->password = Hash::make($request->password);
            $updatedFields[] = 'password';
        }

        $admin->save();

        // Send email if either email or password is updated
        if (!empty($updatedFields)) {
            \Mail::to($admin->email)->send(new \App\Mail\AdminUpdateNotificationMail(
                $admin->name,
                $admin->email,
                $updatedFields,
                $newPassword
            ));
        }

        return redirect()->route('admin-accounts.index')->with('success', 'Admin account updated successfully.');
    }

    public function destroy(User $admin)
    {
        // Set the admin's status to inactive instead of deleting
        $admin->update(['status_id' => 2]); // Status 1 is Inactive

        return redirect()->route('admin-accounts.index')->with('success', 'Admin account deactivated successfully.');
    }

    public function reactivate(User $admin)
    {
        // Set the admin's status to active
        $admin->update(['status_id' => 1]); // Status 1 is Active

        return redirect()->route('admin-accounts.index')->with('success', 'Admin account reactivated successfully.');
    }

    public function promote(Request $request, $facultyId)
    {
        $request->validate(['password' => 'required|string']);
    
        // Confirm Super Admin's password
        if (!Hash::check($request->password, auth()->user()->password)) {
            return redirect()->back()->withErrors(['password' => 'Incorrect password. Please try again.']);
        }
    
        // Retrieve the faculty user instance
        $faculty = User::findOrFail($facultyId);
        
        // Update role and clear course
        $faculty->role_id = 2;
        $faculty->course_id = null;
    
        // Save changes
        $faculty->save();
    
        // Redirect to Admin index with success message
        return redirect()->route('admin-accounts.index')->with('success', 'Account promoted to Admin.');
    }
    
    public function demote(Request $request, $adminId)
    {
        $request->validate([
            'password' => 'required|string',
            'course_id' => 'required|exists:courses,id',
        ]);
    
        // Confirm Super Admin's password
        if (!Hash::check($request->password, auth()->user()->password)) {
            return redirect()->back()->withErrors(['password' => 'Incorrect password. Please try again.']);
        }
    
        // Retrieve the admin user instance
        $admin = User::findOrFail($adminId);
        
        // Update role and assign course
        $admin->role_id = 3;
        $admin->course_id = $request->course_id;
    
        // Save changes
        $admin->save();
    
        // Redirect to Faculty index with success message
        return redirect()->route('faculty.index')->with('success', 'Account demoted to Faculty.');
    }
    

}
