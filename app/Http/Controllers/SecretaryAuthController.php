<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Token;
use App\Models\TokenTransaction;
use App\Services\AcademicYearContext;

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
        $selectedAcademicYear = AcademicYearContext::selected();

        $totalStudents = AcademicYearContext::filterStudents(Student::query())->count();
        $totalTokens = Token::sum('balance');
        $pendingRegistrations = AcademicYearContext::filterStudents(
            Student::where('nesa_registered', false)
        )->count();
        $lowBalanceStudents = AcademicYearContext::filterStudents(
            Student::whereHas('token', fn ($query) => $query->where('balance', '<', 5))
        )->count();

        $recentStudents = AcademicYearContext::filterStudents(
            Student::with(['course', 'branch'])
        )->orderBy('created_at', 'desc')->take(5)->get();

        $recentTransactions = TokenTransaction::whereHas('student', function ($query) {
            if ($yearId = AcademicYearContext::selectedId()) {
                $query->where('academic_year_id', $yearId);
            }
        })
            ->with('student')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $studentsNeedingAttention = AcademicYearContext::filterStudents(
            Student::with(['token', 'course', 'branch'])
                ->where(function ($query) {
                    $query->where('nesa_registered', false)
                        ->orWhereHas('token', fn ($subQuery) => $subQuery->where('balance', '<', 5));
                })
        )->orderBy('created_at', 'desc')->take(10)->get();

        return view('secretary.dashboard', compact(
            'totalStudents',
            'totalTokens',
            'pendingRegistrations',
            'lowBalanceStudents',
            'recentStudents',
            'recentTransactions',
            'studentsNeedingAttention',
            'selectedAcademicYear'
        ));
    }
}
