<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        // Validate the input
        $request->validate([
            'current_password' => [
                'required', 
                'current_password' // This rule checks if the provided password matches the user's current password
            ],
            'password' => [
                'required', 
                'confirmed', // Checks if the password and password_confirmation match
                Password::min(8)
                    ->letters() // Must include at least one letter
                    ->numbers() // Must include at least one number
                    ->symbols() // Must include at least one symbol
            ],
        ], [
            // Custom error messages
            'current_password.current_password' => 'The current password you entered is incorrect.',
            'password.min' => 'The new password must be at least 8 characters.',
            'password.letters' => 'The new password must contain at least one letter.',
            'password.numbers' => 'The new password must contain at least one number.',
            'password.symbols' => 'The new password must contain at least one special character.',
            'password.confirmed' => 'The new password and confirm password do not match.'
        ]);

        // If validation passes, update the password
        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'password-updated');
    }
}
