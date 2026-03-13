<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // DEBUG: Log authentication failure
        Log::info('=== AUTHENTICATION REDIRECT DEBUG ===');
        Log::info('URL: ' . $request->fullUrl());
        Log::info('Method: ' . $request->method());
        Log::info('Route Name: ' . ($request->route() ? $request->route()->getName() : 'No route'));
        Log::info('Route Pattern: ' . ($request->route() ? $request->route()->uri() : 'No route'));
        
        // Check all authentication guards
        $guards = ['web', 'student', 'admin', 'teacher', 'staff'];
        foreach ($guards as $guard) {
            $isAuthenticated = auth($guard)->check();
            Log::info("Guard '{$guard}' authenticated: " . ($isAuthenticated ? 'YES' : 'NO'));
            if ($isAuthenticated) {
                $user = auth($guard)->user();
                Log::info("Guard '{$guard}' user: " . ($user ? get_class($user) . ' ID: ' . $user->id : 'No user'));
            }
        }
        
        Log::info('Default guard: ' . auth()->getDefaultDriver());
        Log::info('Session ID: ' . session()->getId());
        Log::info('Session data: ' . json_encode(session()->all()));

        // Check if the request is using student guard
        if ($request->routeIs('student.*')) {
            Log::info('Redirecting to student.login because route matches student.* pattern');
            Log::info('=== END AUTHENTICATION REDIRECT DEBUG ===');
            
            // Add flash message to explain why redirected
            session()->flash('auth_debug', 'You must be logged in as a student to access this page.');
            
            return route('student.login');
        }

        // Check if the request is for admin routes
        if ($request->is('admin/*') || $request->routeIs('admin.*')) {
            Log::info('Redirecting to admin.login.form because route matches admin pattern');
            Log::info('=== END AUTHENTICATION REDIRECT DEBUG ===');
            
            session()->flash('auth_debug', 'You must be logged in as an admin to access this page.');
            
            return route('admin.login.form');
        }

        // Check if the request is for staff routes
        if ($request->is('staff/*') || $request->routeIs('staff.*')) {
            Log::info('Redirecting to staff.login because route matches staff pattern');
            Log::info('=== END AUTHENTICATION REDIRECT DEBUG ===');
            
            session()->flash('auth_debug', 'You must be logged in as staff to access this page.');
            
            return route('staff.login');
        }

        // Check if the request is for teacher routes
        if ($request->is('teacher/*') || $request->routeIs('teacher.*')) {
            Log::info('Redirecting to teacher.login because route matches teacher pattern');
            Log::info('=== END AUTHENTICATION REDIRECT DEBUG ===');
            
            session()->flash('auth_debug', 'You must be logged in as a teacher to access this page.');
            
            return route('teacher.login');
        }

        Log::info('Redirecting to default login route');
        Log::info('=== END AUTHENTICATION REDIRECT DEBUG ===');
        
        session()->flash('auth_debug', 'You must be logged in to access this page.');

        return route('login');
    }
}
