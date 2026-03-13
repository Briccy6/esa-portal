<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugStudentAuth
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('=== DEBUG STUDENT AUTH MIDDLEWARE ===');
        Log::info('URL: ' . $request->fullUrl());
        Log::info('Method: ' . $request->method());
        Log::info('Route: ' . $request->route()->getName());
        
        $studentAuth = auth('student')->check();
        $webAuth = auth()->check();
        
        Log::info('Student Auth: ' . ($studentAuth ? 'YES' : 'NO'));
        Log::info('Web Auth: ' . ($webAuth ? 'YES' : 'NO'));
        Log::info('Default Guard: ' . auth()->getDefaultDriver());
        
        if ($studentAuth) {
            $student = auth('student')->user();
            Log::info('Student ID: ' . $student->id);
            Log::info('Student Name: ' . $student->full_name);
        }
        
        Log::info('Session ID: ' . session()->getId());
        Log::info('=== END DEBUG STUDENT AUTH MIDDLEWARE ===');
        
        return $next($request);
    }
}
