<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class ValidateSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        // Skip validation for guest routes
        if (!Auth::guard($guard)->check()) {
            return $next($request);
        }

        $user = Auth::guard($guard)->user();
        $sessionKey = 'user_session_' . $guard . '_' . $user->id;
        
        // Get current session fingerprint
        $currentFingerprint = $this->generateSessionFingerprint($request);
        
        // Check if session is already active elsewhere
        $storedFingerprint = Session::get($sessionKey);
        
        if ($storedFingerprint && $storedFingerprint !== $currentFingerprint) {
            // Session conflict detected - logout current user
            Log::warning('Session conflict detected', [
                'guard' => $guard,
                'user_id' => $user->id,
                'current_fingerprint' => $currentFingerprint,
                'stored_fingerprint' => $storedFingerprint,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            // Force logout
            Auth::guard($guard)->logout();
            Session::forget($sessionKey);
            Session::forget('password_hash_' . $guard);
            
            // Clear all session data for security
            Session::flush();
            
            // Regenerate session ID
            Session::regenerate(true);
            
            // Flash error message
            Session::flash('error', 'Your session has been terminated because you logged in from another browser or device. For security reasons, only one active session is allowed per account.');
            
            // Redirect to login page
            $loginRoute = $guard === 'admin' ? 'admin.login' : 
                         ($guard === 'teacher' ? 'teacher.login' : 'student.login');
            
            return redirect()->route($loginRoute);
        }
        
        // Store/update current session fingerprint
        Session::put($sessionKey, $currentFingerprint);
        
        // Update last activity timestamp
        Session::put('last_activity_' . $guard . '_' . $user->id, now());
        
        return $next($request);
    }
    
    /**
     * Generate a unique session fingerprint based on browser and system info
     */
    private function generateSessionFingerprint(Request $request): string
    {
        $data = [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'accept_language' => $request->header('Accept-Language'),
            'accept_encoding' => $request->header('Accept-Encoding'),
            'session_id' => Session::getId(),
        ];
        
        return hash('sha256', serialize($data));
    }
}
