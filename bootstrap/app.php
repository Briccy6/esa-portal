<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

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
        ]);
        
        // Replace the default ValidatePostSize with our custom one
        $middleware->remove(\Illuminate\Http\Middleware\ValidatePostSize::class);
        $middleware->web(append: \App\Http\Middleware\CustomValidatePostSize::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle 404 errors for missing models
        $exceptions->render(function (Illuminate\Database\Eloquent\ModelNotFoundException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Resource not found',
                    'status' => 'error'
                ], 404);
            }
            
            return response()->view('errors.404', [], 404);
        });

        // Handle method not allowed exceptions
        $exceptions->render(function (Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Method not allowed',
                    'status' => 'error'
                ], 405);
            }
            
            return response()->view('errors.405', [], 405);
        });

        // Handle CSRF token mismatch
        $exceptions->render(function (Illuminate\Session\TokenMismatchException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'CSRF token mismatch',
                    'status' => 'error'
                ], 419);
            }
            
            return response()->view('errors.419', [], 419);
        });

        // Handle validation exceptions
        $exceptions->render(function (Illuminate\Validation\ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                    'status' => 'error'
                ], 422);
            }
            
            return response()->view('errors.422', [
                'errors' => $e->errors()
            ], 422);
        });

        // Handle authentication exceptions
        $exceptions->render(function (Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated',
                    'status' => 'error'
                ], 401);
            }
            
            return response()->view('errors.401', [], 401);
        });

        // Handle authorization exceptions
        $exceptions->render(function (Illuminate\Auth\Access\AuthorizationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Access denied',
                    'status' => 'error'
                ], 403);
            }
            
            return response()->view('errors.403', [], 403);
        });

        // Handle too many requests
        $exceptions->render(function (Illuminate\Http\Exceptions\ThrottleRequestsException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Too many requests',
                    'status' => 'error'
                ], 429);
            }
            
            return response()->view('errors.429', [], 429);
        });

        // Handle all other exceptions in production
        $exceptions->render(function (Throwable $e, Request $request) {
            // Log the error
            \Log::error($e->getMessage(), [
                'exception' => $e,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Server error',
                    'status' => 'error'
                ], 500);
            }
            
            // In production, show generic error page
            if (app()->environment('production')) {
                return response()->view('errors.500', [], 500);
            }
            
            // In local development, let Laravel handle it normally
            throw $e;
        });

        // Log all exceptions
        $exceptions->reportable(function (Throwable $e) {
            \Log::error('Exception caught:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'ip' => request()->ip(),
                'user_id' => auth()->id(),
                'user_agent' => request()->userAgent()
            ]);
        });
    })->create();
