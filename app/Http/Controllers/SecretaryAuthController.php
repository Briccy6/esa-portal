<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Token;
use App\Models\TokenTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SecretaryAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('secretary.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (auth()->guard('staff')->attempt($credentials)) {
            return redirect()->route('secretary.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->withInput();
    }

    public function logout()
    {
        Auth::guard('staff')->logout();
        return redirect()->route('secretary.login.form');
    }

    public function dashboard()
    {
        // Total students
        $totalStudents = Student::count();

        // Total tokens in circulation
        $totalTokens = Token::sum('balance');

        // Pending registrations (students not NESA registered)
        $pendingRegistrations = Student::where('nesa_registered', false)->count();

        // Students with low token balance (< 5)
        $lowBalanceStudents = Student::whereHas('token', function($query) {
            $query->where('balance', '<', 5);
        })->count();

        // Recent students (last 10)
        $recentStudents = Student::with(['course', 'branch'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Recent token transactions
        $recentTransactions = TokenTransaction::with('student')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Students needing attention (low balance or pending NESA)
        $studentsNeedingAttention = Student::with(['token', 'course', 'branch'])
            ->where(function($query) {
                $query->where('nesa_registered', false)
                      ->orWhereHas('token', function($subQuery) {
                          $subQuery->where('balance', '<', 5);
                      });
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('secretary.dashboard', compact(
            'totalStudents',
            'totalTokens',
            'pendingRegistrations',
            'lowBalanceStudents',
            'recentStudents',
            'recentTransactions',
            'studentsNeedingAttention'
        ));
    }
}
