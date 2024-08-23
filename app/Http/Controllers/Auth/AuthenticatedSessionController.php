<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Check if the authenticated user is inactive
        if ($request->user()->status_id == 2) { 
            Auth::logout(); // Log out the user if they are inactive
            return redirect()->route('login')->withErrors(['email' => 'This account is inactive.']);
        }

        $request->session()->regenerate();

        //authentication for roles
        if($request->user()->role_id == 1)
        {
            return redirect('super_admin/dashboard');
        } 
        else if ($request->user()->role_id == 2)
        {
            return redirect('admin/dashboard');
        } 
        else if ($request->user()->role_id == 3)
        {
            return redirect('faculty/dashboard');
        } 
        else if ($request->user()->role_id == 4)
        {
            return redirect('company/dashboard');
        } 
        else if ($request->user()->role_id == 5)
        {
            return redirect('student/dashboard');
        }
        else
        {
            return redirect()->intended(route('dashboard', absolute: false));
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
