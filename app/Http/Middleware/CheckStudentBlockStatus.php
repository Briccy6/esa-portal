<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStudentBlockStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->guard('student')->check()) {
            $student = auth()->guard('student')->user();
            
            if ($student->is_blocked) {
                auth()->guard('student')->logout();
                return redirect()->route('student.login.form')
                    ->with('blocked', true)
                    ->with('block_reason', $student->block_reason);
            }
        }

        return $next($request);
    }
}
