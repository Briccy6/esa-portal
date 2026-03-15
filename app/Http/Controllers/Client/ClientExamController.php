<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientExamController extends Controller
{
    public function index()
    {
        $exams = Exam::where('is_public', true)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('client.exams.index', compact('exams'));
    }

    public function show(Exam $exam)
    {
        if (!$exam->is_public || !$exam->is_active) {
            abort(404);
        }

        return view('client.exams.show', compact('exam'));
    }

    public function take(Exam $exam)
    {
        if (!$exam->is_public || !$exam->is_active) {
            abort(404);
        }

        $client = Auth::guard('client')->user();
        
        // Check if client has access (tokens or subscription)
        if (!$client->canAccessExam($exam)) {
            return back()->with('error', 'You need tokens or an active subscription to access this exam.');
        }

        // Create exam session
        $session = $client->examSessions()->create([
            'exam_id' => $exam->id,
            'started_at' => now(),
        ]);

        return redirect()->route('client.exams.take.question', [$exam->id, $session->id, 1]);
    }

    public function takeQuestion(Exam $exam, $session, $questionNumber = 1)
    {
        if (!$exam->is_public || !$exam->is_active) {
            abort(404);
        }

        $client = Auth::guard('client')->user();
        
        // Verify session belongs to client
        $examSession = $client->examSessions()
            ->where('exam_id', $exam->id)
            ->where('id', $session)
            ->firstOrFail();

        // Get questions with relationships
        $questions = $exam->questions()
            ->with('options')
            ->orderBy('id')
            ->get();

        if ($questionNumber > $questions->count()) {
            return redirect()->route('client.exams.take.question', [$exam->id, $session, $questions->count()]);
        }

        $currentQuestion = $questions->get($questionNumber - 1);
        
        // Get session data
        $sessionData = $examSession->data ?? [];
        $answers = $sessionData['answers'] ?? [];
        $flagged = $sessionData['flagged'] ?? [];

        return view('client.exams.take', compact(
            'exam',
            'session',
            'questionNumber',
            'questions',
            'currentQuestion',
            'answers',
            'flagged'
        ));
    }

    public function saveAnswer(Request $request, Exam $exam, $session, $questionNumber)
    {
        $request->validate([
            'answer' => 'required|string|max:1',
        ]);

        $client = Auth::guard('client')->user();
        
        // Verify session belongs to client
        $examSession = $client->examSessions()
            ->where('exam_id', $exam->id)
            ->where('id', $session)
            ->firstOrFail();

        // Get current session data
        $sessionData = $examSession->data ?? [];
        
        // Save answer
        $sessionData['answers'][$questionNumber] = $request->answer;
        
        // Update session data
        $examSession->update(['data' => $sessionData]);

        return response()->json(['success' => true]);
    }

    public function submitExam(Request $request, Exam $exam, $session)
    {
        $client = Auth::guard('client')->user();
        
        // Verify session belongs to client
        $examSession = $client->examSessions()
            ->where('exam_id', $exam->id)
            ->where('id', $session)
            ->firstOrFail();

        // Get session data
        $sessionData = $examSession->data ?? [];
        $answers = $sessionData['answers'] ?? [];

        // Calculate score
        $questions = $exam->questions()->with('options')->get();
        $correctAnswers = 0;
        $totalQuestions = $questions->count();

        foreach ($questions as $index => $question) {
            $questionNumber = $index + 1;
            if (isset($answers[$questionNumber]) && $answers[$questionNumber] === $question->correct_answer) {
                $correctAnswers++;
            }
        }

        $score = ($correctAnswers / $totalQuestions) * 100;

        // Mark session as completed
        $examSession->update([
            'completed_at' => now(),
            'score' => $score,
            'data' => $sessionData,
        ]);

        // Deduct token if used
        if ($client->getCurrentTokens() >= $exam->token_cost && !$client->hasActiveSubscription()) {
            $client->deductTokens($exam->token_cost);
        }

        return redirect()->route('client.exams.show', $exam->id)
            ->with('success', "Exam completed! Your score: {$correctAnswers}/{$totalQuestions} ({$score}%)");
    }
}
