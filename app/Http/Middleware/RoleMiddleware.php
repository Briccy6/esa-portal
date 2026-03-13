<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();
        
        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        // Check if user is admin (using admin guard)
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }

        // Check if user is staff and has the required role
        if (Auth::guard('staff')->check()) {
            $staff = Auth::guard('staff')->user();
            
            // Allow access if staff role is in the allowed roles
            if (in_array($staff->role, $roles)) {
                return $next($request);
            }
        }

        // If no valid role found, deny access
        return redirect()->route('login')->with('error', 'You do not have permission to access this page.');
    }
}
