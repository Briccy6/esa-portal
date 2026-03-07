<?php

namespace App\Http\Controllers;

use App\Models\Token;
use App\Models\Student;
use App\Models\TokenTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenController extends Controller
{
    public function index()
    {
        $student = Auth::user();
        $token = $student->token;
        $transactions = $token ? $token->transactions()->latest()->paginate(20) : collect();

        return view('tokens.index', compact('student', 'token', 'transactions'));
    }

    public function purchase(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:1|max:1000',
            'payment_reference' => 'nullable|string',
        ]);

        $student = Auth::user();
        $token = $student->initializeTokens();

        $transaction = $token->addTokens(
            $request->amount,
            'Token purchase',
            'purchase',
            $request->payment_reference
        );

        return redirect()->route('tokens.index')
            ->with('success', "Successfully purchased {$request->amount} tokens!");
    }

    public function secretaryIndex()
    {
        $students = Student::with('token')->paginate(20);
        
        // Determine the base routes for token management based on auth guard
        $isAdmin = auth()->guard('admin')->check();
        $tokenHistoryBaseRoute = $isAdmin ? route('admin.tokens.history') : route('secretary.tokens.history');
        $tokenHistoryRoute = $isAdmin ? route('admin.tokens.history', ':studentId') : route('secretary.tokens.history', ':studentId');
        $tokenAddRoute = $isAdmin ? route('admin.tokens.add', ':studentId') : route('secretary.tokens.add', ':studentId');
        $tokenDeductRoute = $isAdmin ? route('admin.tokens.deduct', ':studentId') : route('secretary.tokens.deduct', ':studentId');
        
        return view('tokens.secretary-index', compact('students', 'tokenHistoryBaseRoute', 'tokenHistoryRoute', 'tokenAddRoute', 'tokenDeductRoute'));
    }

    public function secretaryAddTokens(Request $request, Student $student)
    {
        $request->validate([
            'amount' => 'required|integer|min:1|max:1000',
            'description' => 'required|string|max:255',
            'source' => 'required|in:system,bonus,compensation,refund',
        ]);

        $token = $student->initializeTokens();

        $transaction = $token->addTokens(
            $request->amount,
            $request->description,
            $request->source
        );

        return redirect()->route('tokens.secretary-index')
            ->with('success', "Added {$request->amount} tokens to {$student->full_name}");
    }

    public function secretaryDeductTokens(Request $request, Student $student)
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
            'description' => 'required|string|max:255',
        ]);

        $token = $student->token;

        if (!$token || !$token->canAfford($request->amount)) {
            return redirect()->route('tokens.secretary-index')
                ->with('error', 'Student does not have enough tokens');
        }

        $transaction = $token->spendTokens(
            $request->amount,
            $request->description,
            'admin'
        );

        return redirect()->route('tokens.secretary-index')
            ->with('success', "Deducted {$request->amount} tokens from {$student->full_name}");
    }

    public function transactionHistory(Student $student = null)
    {
        if ($student) {
            $token = $student->token;
            $transactions = $token ? $token->transactions()->latest()->paginate(50) : collect();
            $title = "Token History - {$student->full_name}";
        } else {
            $transactions = TokenTransaction::with('token.student')->latest()->paginate(50);
            $title = 'All Token Transactions';
        }

        return view('transactions.history', compact('transactions', 'title', 'student'));
    }

    public function dashboard()
    {
        $student = Auth::user();
        $token = $student->token;
        
        $stats = [
            'total_tokens' => $token ? $token->balance : 0,
            'total_earned' => $token ? $token->total_earned : 0,
            'total_spent' => $token ? $token->total_spent : 0,
            'recent_transactions' => $token ? $token->transactions()->latest()->take(5)->get() : collect(),
        ];

        return view('tokens.dashboard', compact('student', 'stats'));
    }
}
