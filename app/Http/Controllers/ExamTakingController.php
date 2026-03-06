<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamSession;
use App\Models\StudentAnswer;
use App\Models\AntiCheatingLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ExamTakingController extends Controller
{
    public function start(Exam $exam)
    {
        $student = Auth::user();
        
        // Check if exam is available
        if (!$exam->is_active || !$exam->settings->isCurrentlyActive()) {
            return back()->with('error', 'This exam is not currently available.');
        }

        // Check if student has reached max attempts
        $attempts = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->count();
            
        if ($attempts >= $exam->settings->max_attempts) {
            return back()->with('error', 'You have reached the maximum number of attempts for this exam.');
        }

        // Create new attempt
        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'attempt_number' => $attempts + 1,
            'started_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => 'in_progress',
        ]);

        // Create exam session
        $questionOrder = $this->generateQuestionOrder($exam);
        $answerOrders = $this->generateAnswerOrders($exam, $questionOrder);

        $session = ExamSession::create([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'attempt_id' => $attempt->id,
            'session_start' => now(),
            'last_activity' => now(),
            'time_remaining_seconds' => $exam->settings->duration_minutes * 60,
            'question_order' => $questionOrder,
            'answer_orders' => $answerOrders,
        ]);

        // Create exam settings record if it doesn't exist
        if (!$exam->settings) {
            $this->createDefaultSettings($exam);
        }

        return redirect()->route('exams.take-question', [$exam, $session, 1]);
    }

    public function takeQuestion(Exam $exam, ExamSession $session, $questionNumber = 1)
    {
        $this->authorize('take', $session);

        // Check if time expired
        if ($session->time_remaining_seconds <= 0) {
            return $this->autoSubmit($exam, $session);
        }

        // Check if too many violations
        if ($session->hasTooManyViolations()) {
            return $this->autoSubmit($exam, $session, 'auto_submitted_violations');
        }

        $questionOrder = $session->question_order;
        if ($questionNumber < 1 || $questionNumber > count($questionOrder)) {
            return back()->with('error', 'Invalid question number.');
        }

        $questionId = $questionOrder[$questionNumber - 1];
        $question = $exam->questions()->findOrFail($questionId);

        // Get existing answer if any
        $existingAnswer = StudentAnswer::where('attempt_id', $session->attempt_id)
            ->where('question_id', $questionId)
            ->first();

        // Prepare answer options with randomization
        $options = $this->prepareOptions($question, $session, $questionNumber);

        return view('exams.take-question', [
            'exam' => $exam,
            'session' => $session,
            'question' => $question,
            'questionNumber' => $questionNumber,
            'totalQuestions' => count($questionOrder),
            'existingAnswer' => $existingAnswer,
            'options' => $options,
        ]);
    }

    public function saveAnswer(Request $request, Exam $exam, ExamSession $session, $questionNumber)
    {
        $this->authorize('take', $session);

        $questionOrder = $session->question_order;
        $questionId = $questionOrder[$questionNumber - 1];
        $question = $exam->questions()->findOrFail($questionId);

        $answer = StudentAnswer::updateOrCreate(
            [
                'attempt_id' => $session->attempt_id,
                'question_id' => $questionId,
            ],
            [
                'answer' => $request->input('answer'),
                'selected_options' => $request->input('selected_options'),
                'answered_at' => now(),
                'needs_manual_grading' => in_array($question->question_type, ['essay', 'short_answer']),
            ]
        );

        // Auto-grade if possible
        if (!$answer->needs_manual_grading) {
            $answer->is_correct = $answer->isCorrect();
            $answer->score = $answer->is_correct ? $question->marks : 0;
            $answer->save();
        }

        // Update session activity
        $session->update([
            'last_activity' => now(),
        ]);

        // Navigate based on action
        if ($request->input('action') === 'next' && $questionNumber < count($questionOrder)) {
            return redirect()->route('exams.take-question', [$exam, $session, $questionNumber + 1]);
        } elseif ($request->input('action') === 'previous' && $questionNumber > 1) {
            return redirect()->route('exams.take-question', [$exam, $session, $questionNumber - 1]);
        } elseif ($request->input('action') === 'submit') {
            return $this->submitExam($exam, $session);
        } else {
            return back()->with('success', 'Answer saved!');
        }
    }

    public function submitExam(Exam $exam, ExamSession $session)
    {
        $this->authorize('take', $session);

        $attempt = $session->attempt;
        
        // Calculate total score
        $totalScore = 0;
        $totalPossible = 0;
        $studentAnswers = $attempt->studentAnswers;

        foreach ($exam->questions as $question) {
            $answer = $studentAnswers->where('question_id', $question->id)->first();
            if ($answer) {
                $totalScore += $answer->score ?? 0;
            }
            $totalPossible += $question->marks;
        }

        $percentage = $totalPossible > 0 ? ($totalScore / $totalPossible) * 100 : 0;

        // Update attempt
        $attempt->update([
            'finished_at' => now(),
            'duration_minutes' => $attempt->started_at->diffInMinutes(now()),
            'total_score' => $totalScore,
            'percentage' => $percentage,
            'status' => 'submitted',
        ]);

        // Update session
        $session->update([
            'last_activity' => now(),
        ]);

        return redirect()->route('exams.results', $exam)
            ->with('success', 'Exam submitted successfully!');
    }

    public function autoSubmit(Exam $exam, ExamSession $session, $reason = 'expired')
    {
        $attempt = $session->attempt;
        
        $attempt->update([
            'finished_at' => now(),
            'status' => $reason === 'expired' ? 'expired' : 'auto_submitted',
        ]);

        // Calculate score for submitted answers
        $this->calculateScore($attempt);

        return redirect()->route('exams.results', $exam)
            ->with('warning', 'Exam was automatically submitted due to ' . $reason . '.');
    }

    public function logViolation(Request $request, ExamSession $session)
    {
        $this->authorize('take', $session);

        $violationType = $request->input('violation_type');
        
        // Log the violation
        AntiCheatingLog::logViolation(
            $session->id,
            $session->student_id,
            $violationType,
            $request->input('description'),
            $request->input('metadata', [])
        );

        // Update session counters
        $updates = ['last_activity' => now()];
        
        if ($violationType === 'tab-switch') {
            $updates['tab_switches'] = $session->tab_switches + 1;
        } elseif ($violationType === 'fullscreen-exit') {
            $updates['fullscreen_violations'] = $session->fullscreen_violations + 1;
            $updates['is_fullscreen'] = false;
        }

        $session->update($updates);

        // Check if auto-submit should be triggered
        if ($session->hasTooManyViolations() && $session->exam->settings->auto_submit_on_violation) {
            return response()->json(['action' => 'auto_submit']);
        }

        return response()->json(['action' => 'continue']);
    }

    private function generateQuestionOrder(Exam $exam)
    {
        $questions = $exam->questions->pluck('id')->toArray();
        
        if ($exam->settings->randomize_questions) {
            shuffle($questions);
        }
        
        return $questions;
    }

    private function generateAnswerOrders(Exam $exam, $questionOrder)
    {
        $answerOrders = [];
        
        foreach ($questionOrder as $index => $questionId) {
            $question = $exam->questions()->findOrFail($questionId);
            
            if ($question->question_type === 'multiple_choice' && $exam->settings->randomize_answers) {
                $options = ['A', 'B', 'C', 'D'];
                shuffle($options);
                $answerOrders[$index + 1] = $options;
            }
        }
        
        return $answerOrders;
    }

    private function prepareOptions($question, $session, $questionNumber)
    {
        if ($question->question_type !== 'multiple_choice') {
            return null;
        }

        $options = [
            'A' => $question->option_a,
            'B' => $question->option_b,
            'C' => $question->option_c,
            'D' => $question->option_d,
        ];

        $answerOrders = $session->answer_orders;
        if (isset($answerOrders[$questionNumber])) {
            $orderedKeys = $answerOrders[$questionNumber];
            $orderedOptions = [];
            
            foreach ($orderedKeys as $key) {
                $orderedOptions[$key] = $options[$key];
            }
            
            return $orderedOptions;
        }

        return $options;
    }

    private function calculateScore(ExamAttempt $attempt)
    {
        $totalScore = 0;
        $totalPossible = 0;
        $studentAnswers = $attempt->studentAnswers;

        foreach ($attempt->exam->questions as $question) {
            $answer = $studentAnswers->where('question_id', $question->id)->first();
            if ($answer && !$answer->needs_manual_grading) {
                $answer->is_correct = $answer->isCorrect();
                $answer->score = $answer->is_correct ? $question->marks : 0;
                $answer->save();
                $totalScore += $answer->score;
            }
            $totalPossible += $question->marks;
        }

        $percentage = $totalPossible > 0 ? ($totalScore / $totalPossible) * 100 : 0;

        $attempt->update([
            'total_score' => $totalScore,
            'percentage' => $percentage,
        ]);
    }

    private function createDefaultSettings(Exam $exam)
    {
        \App\Models\ExamSettings::create([
            'exam_id' => $exam->id,
            'duration_minutes' => 60,
            'show_results_immediately' => true,
            'require_fullscreen' => true,
            'disable_right_click' => true,
            'disable_copy_paste' => true,
            'max_tab_switches' => 3,
            'auto_submit_on_violation' => true,
        ]);
    }
}
