<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    private const TRACKED_GUARDS = ['student', 'staff', 'admin', 'teacher'];

    public function handle(Request $request, Closure $next): Response
    {
        $identity = null;

        foreach (self::TRACKED_GUARDS as $guard) {
            if (Auth::guard($guard)->check()) {
                $identity = [
                    'guard' => $guard,
                    'user_id' => Auth::guard($guard)->id(),
                ];
                break;
            }
        }

        $response = $next($request);
        $now = now();

        if (
            $identity
            && $response->getStatusCode() < Response::HTTP_BAD_REQUEST
        ) {
            DB::table('user_activity_statuses')->upsert(
                [[
                    'guard' => $identity['guard'],
                    'user_id' => $identity['user_id'],
                    'last_active_at' => $now,
                ]],
                ['guard', 'user_id'],
                ['last_active_at']
            );
        }

        if (
            $identity
            && $request->isMethod('GET')
            && $response->isSuccessful()
            && str_starts_with((string) $response->headers->get('Content-Type'), 'text/html')
        ) {
            $route = $request->route();
            $routeName = $route?->getName() ?: $route?->uri() ?: $request->path();

            DB::table('user_activity_events')->upsert(
                [[
                    'guard' => $identity['guard'],
                    'user_id' => $identity['user_id'],
                    'route_name' => $routeName,
                    'activity_date' => $now->toDateString(),
                    'visits' => 1,
                    'last_visited_at' => $now,
                ]],
                ['guard', 'user_id', 'route_name', 'activity_date'],
                [
                    'visits' => DB::raw('visits + 1'),
                    'last_visited_at' => $now,
                ]
            );
        }

        return $response;
    }
}
