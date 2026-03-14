<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    public function handle($request, Closure $next)
    {
        \Log::info('=== AdminAuth middleware ===');
        \Log::info('Request: ' . $request->method() . ' ' . $request->url());
        \Log::info('Admin auth check: ' . (Auth::guard('admin')->check() ? 'YES' : 'NO'));
        \Log::info('Admin ID: ' . (Auth::guard('admin')->id() ?? 'NULL'));
        \Log::info('Session ID: ' . session()->getId());

        if (!Auth::guard('admin')->check()) {
            \Log::error('AdminAuth FAILED - redirecting to login');
            \Log::info('Session data keys: ' . json_encode(array_keys(session()->all())));
            return redirect()->route('admin.login.form');
        }

        return $next($request);
    }
}
