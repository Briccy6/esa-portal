<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Student;
use App\Models\ExamToken;
use Illuminate\Support\Facades\Auth;

class CheckExamAccess
{
    public function handle(Request $request, Closure $next)
    {
        $examId = $request->route('exam') ?? $request->input('exam_id');
        
        if (!$examId) {
            return redirect()->back()->with('error', 'Exam not specified');
        }

        $exam = Exam::findOrFail($examId);
        $student = Auth::user();

        // Check if exam is accessible
        if (!$exam->isAccessible()) {
            return redirect()->back()->with('error', 'This exam is not currently accessible');
        }

        // Check access permissions
        $access = $exam->canBeAttemptedByStudent($student);

        if (!$access['allowed']) {
            $message = $access['reason'];
            
            switch ($access['action']) {
                case 'pay':
                    return redirect()->route('exam.payment', $exam->id)
                        ->with('info', $message . '. Amount: ' . $exam->formatted_public_price);
                
                case 'buy_tokens':
                    return redirect()->route('tokens.purchase')
                        ->with('info', $message . ". Required: {$access['required']} tokens, You have: {$access['has']} tokens");
                
                case 'register':
                    return redirect()->route('students.create')
                        ->with('info', $message . '. Please register to attempt this exam.');
                
                case 'wait':
                    return redirect()->back()->with('info', $message);
                
                default:
                    return redirect()->back()->with('error', $message);
            }
        }

        // For registered students, check and deduct tokens if required
        if ($student && $exam->requiresTokens() && !$student->activeSubscription) {
            $token = $student->token;
            
            if (!$token || !$token->canAfford($exam->token_cost)) {
                return redirect()->route('tokens.purchase')
                    ->with('error', "You need {$exam->token_cost} tokens to attempt this exam");
            }

            // Deduct tokens for exam attempt
            $token->spendTokens($exam->token_cost, "Attempted exam: {$exam->title}", 'exam');
        }

        // Create exam token record
        if ($student) {
            ExamToken::updateOrCreate(
                [
                    'exam_id' => $exam->id,
                    'student_id' => $student->id,
                ],
                [
                    'tokens_used' => $exam->token_cost,
                    'attempted_at' => now(),
                    'payment_status' => $exam->requiresTokens() ? 'paid' : 'free',
                    'payment_amount' => 0,
                ]
            );
        }

        return $next($request);
    }
}
