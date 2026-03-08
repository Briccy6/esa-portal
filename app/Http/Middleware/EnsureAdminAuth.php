<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureAdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Check if admin is authenticated
        if (!Auth::guard('admin')->check()) {
            // Store the intended URL for redirect after login
            session(['admin_intended' => $request->fullUrl()]);
            
            return redirect()->route('admin.login.form')
                ->with('error', 'You must be logged in to access this page.');
        }

        return $next($request);
    }
}
