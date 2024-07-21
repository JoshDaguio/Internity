<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class JobAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow access to Super Admin, Admin, and Company users
        if (Auth::check() && in_array(Auth::user()->role_id, [1, 2, 4])) {
            return $next($request);
        }

        // Redirect to dashboard if not authorized
        return redirect('dashboard');
    }
}
