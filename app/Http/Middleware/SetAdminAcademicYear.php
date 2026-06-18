<?php

namespace App\Http\Middleware;

use App\Services\AcademicYearContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetAdminAcademicYear
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('admin')->check() || auth('staff')->check() || auth('teacher')->check()) {
            View::share('adminAcademicYears', AcademicYearContext::allOrdered());
            View::share('adminSelectedAcademicYear', AcademicYearContext::selected());
            View::share('adminCurrentAcademicYear', AcademicYearContext::current());
            View::share('adminViewingPastYear', AcademicYearContext::isViewingPastYear());
            View::share('selectedAcademicYear', AcademicYearContext::selected());
        }

        return $next($request);
    }
}
