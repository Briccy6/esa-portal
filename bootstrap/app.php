<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'custom.post.size' => \App\Http\Middleware\CustomValidatePostSize::class,
            'admin.academic_year' => \App\Http\Middleware\SetAdminAcademicYear::class,
            'academic.year' => \App\Http\Middleware\SetAdminAcademicYear::class,
            'check.student.block' => \App\Http\Middleware\CheckStudentBlockStatus::class,
        ]);
        
        // Replace the default ValidatePostSize with our custom one
        $middleware->web(append: [
            \App\Http\Middleware\CustomValidatePostSize::class,
            \App\Http\Middleware\SetAdminAcademicYear::class,
            \App\Http\Middleware\RestrictIncompleteStudentProfile::class,
            \App\Http\Middleware\CheckStudentBlockStatus::class,
            \App\Http\Middleware\TrackUserActivity::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
