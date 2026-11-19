<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }

        if (Auth::guard('staff')->check()) {
            $staff = Auth::guard('staff')->user();

            if (in_array(strtolower((string) $staff->role), array_map('strtolower', $roles))) {
                return $next($request);
            }

            abort(403, 'You do not have permission to access this page.');
        }

        return redirect()->route('login')->with('error', 'Please login to access this page.');
    }
}
