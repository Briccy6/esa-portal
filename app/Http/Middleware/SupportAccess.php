<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SupportAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow access to admins and staff for the support dashboard
        if (Auth::guard('admin')->check() || Auth::guard('staff')->check()) {
            return $next($request);
        }

        // Redirect unauthorized users to their appropriate dashboard
        if (Auth::guard('student')->check()) {
            return redirect()->route('student.dashboard');
        }

        if (Auth::guard('teacher')->check()) {
            return redirect()->route('teacher.dashboard');
        }

        return redirect()->route('login');
    }
}
