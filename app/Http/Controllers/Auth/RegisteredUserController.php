<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

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
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'id_number' => ['required', 'string', 'max:255', 'unique:profiles'],
            'course_id' => ['required', 'exists:courses,id'],
        ]);

        // Create the profile first
        $profile = Profile::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'id_number' => $request->id_number,
        ]);

        // Create the user and link the profile
        $user = User::create([
            'name' => $request->first_name,
            'email' => $request->email,
            'password' => Hash::make('aufCCSInternship'), // Default password
            'course_id' => $request->course_id,
            'role_id' => 5, // Student role
            'status_id' => 3, // Pending registration status
            'profile_id' => $profile->id, // Link the profile
        ]);

        return redirect()->route('register.success');
    }
}
