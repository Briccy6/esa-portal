<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Mail\AdminLoginAlertMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            // Send login alert email
            $admin = Auth::guard('admin')->user();
            $ip = $request->ip();
            $time = Carbon::now()->toDateTimeString();
            $userAgent = $request->header('User-Agent');
            $logoutUrl = route('admin.force-logout');

            Mail::to($admin->email)->send(
                new AdminLoginAlertMail($ip, $time, $userAgent, $logoutUrl)
            );

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    public function forceLogout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login')->with('status', 'You have been logged out for security reasons.');
    }
}