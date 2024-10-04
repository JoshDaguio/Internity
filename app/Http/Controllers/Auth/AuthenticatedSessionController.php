<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; // Add this line for the Http facade
use Carbon\Carbon;
use App\Models\User;
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
        }  elseif ($request->user()->status_id == 3) {
            Auth::logout(); // Log out the user if they are waiting for approval
            return redirect()->route('login')->withErrors(['email' => 'This account is waiting for approval.']);
        }


        // Check for expired company accounts
        $this->checkForExpiredCompanies();

        $request->session()->regenerate();

        // Handle 'Remember Me' functionality
        // Auth attempt with 'remember' checkbox
        $remember = $request->filled('remember');

        Auth::attempt($request->only('email', 'password'), $remember);

        //redirect base on roles
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

    private function checkForExpiredCompanies()
    {
        try {
            $response = Http::get('http://worldtimeapi.org/api/timezone/Asia/Manila');
            $currentDateTime = Carbon::parse($response->json('datetime'));
        } catch (\Exception $e) {
            $currentDateTime = Carbon::now(new \DateTimeZone('Asia/Manila'));
        }

        // Find all active companies with expiry dates
        $expiredCompanies = User::where('role_id', 4) // Only companies
                                ->where('status_id', 1) // Active
                                ->whereNotNull('expiry_date') // Has expiry date
                                ->where('expiry_date', '<', $currentDateTime) // Expired
                                ->update(['status_id' => 2]); // Set to Inactive
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
