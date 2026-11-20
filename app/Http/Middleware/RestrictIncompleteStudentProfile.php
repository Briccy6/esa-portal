<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Student;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictIncompleteStudentProfile
{
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();
        $usesStudentGuard = false;

        if ($route) {
            foreach ($route->gatherMiddleware() as $middleware) {
                if (!is_string($middleware) || !str_starts_with($middleware, 'auth:')) {
                    continue;
                }

                if (in_array('student', explode(',', substr($middleware, 5)), true)) {
                    $usesStudentGuard = true;
                    break;
                }
            }
        }

        if (!$usesStudentGuard) {
            return $next($request);
        }

        $student = auth('student')->user();
        $allowedRoutes = [
            'student.dashboard',
            'students.profile',
            'students.profile.update',
            'student.logout',
        ];

        if (
            $student
            && !$student->hasCompletedProfile()
            && now('Africa/Kigali')->greaterThanOrEqualTo(Student::profileCompletionDeadline())
            && !$request->routeIs(...$allowedRoutes)
        ) {
            return redirect()->route('student.dashboard');
        }

        return $next($request);
    }
}
