<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use App\Models\AcademicYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\StudentApprovalMail;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $courses = \App\Models\Course::all();
        return view('auth.register', compact('courses'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
    // Validate the input
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email_username' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z]+\.[a-zA-Z]+$/', // Ensure it's in lastname.firstname format
            ],
            'id_number' => [
                'required', 
                'string', 
                'max:255', 
                'unique:profiles,id_number',
                'regex:/^\d{2}-\d{4}-\d{3}$/', // Regex for format 00-0000-000
            ],
            'course_id' => ['required', 'exists:courses,id'],
        ], [
            'email_username.regex' => 'Email must be in the format lastname.firstname',
            'id_number.regex' => 'The ID number must follow the format 00-0000-000.', // Custom error message
            'id_number.unique' => 'This ID number is already registered.', // Custom error message for duplicate ID
        ]);

        // Append @auf.edu.ph to the email username
        $email = $request->email_username . '@auf.edu.ph';

        // Generate a unique password with aufCCSInternship + random characters
        $randomPassword = 'aufCCSInternship' . Str::random(5);

        // Create the profile first
        $profile = Profile::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'id_number' => $request->id_number,
        ]);

        // Get the current academic year
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        // Create the user and link the profile
        $user = User::create([
            'name' => $request->first_name,
            'email' => $email, // Save the generated school email
            'password' => Hash::make($randomPassword), // Store hashed password
            'course_id' => $request->course_id,
            'role_id' => 5, // Student role
            'status_id' => 3, // Pending registration status
            'profile_id' => $profile->id, // Link the profile
            'academic_year_id' => $currentAcademicYear->id, // Assign current academic year
        ]);

        return redirect()->route('register.success');
    }

        /**
     * Method to approve a student and send approval email.
     */
    public function approveRegistration($userId)
    {
        $student = User::with('profile', 'course')->findOrFail($userId);

        // Update student status to active
        $student->status_id = 1;
        $student->save();

        // Send approval email with login details
        Mail::to($student->email)->send(new StudentApprovalMail(
            $student->profile->first_name . ' ' . $student->profile->last_name, 
            $student->email, 
            $randomPassword, // Send the random password created at registration
            $student->course->course_name
        ));

        return redirect()->route('registrations.pending')->with('success', 'Student registration approved successfully and email sent.');
    }
}