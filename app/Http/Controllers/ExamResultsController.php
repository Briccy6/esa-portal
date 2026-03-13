<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\StudentAnswer;
use App\Models\ExamAnalytics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExamResultsExport;

class ExamResultsController extends Controller
{
    public function index(Exam $exam)
    {
        $this->authorize('viewResults', $exam);

        $exam->load(['attempts.student', 'questions']);

        // Calculate statistics
        $totalAttempts = $exam->attempts->count();
        $completedAttempts = $exam->attempts->where('status', 'submitted')->count();
        $averageScore = $completedAttempts > 0 ? $exam->attempts->where('status', 'submitted')->avg('percentage') : 0;
        $passRate = $this->calculatePassRate($exam);

        return view('exam-results.index', compact('exam', 'totalAttempts', 'completedAttempts', 'averageScore', 'passRate'));
    }

    public function studentResults(Exam $exam, ExamAttempt $attempt)
    {
        $this->authorize('viewStudentResults', [$exam, $attempt]);

        $attempt->load(['student', 'studentAnswers.question', 'exam.settings']);

        return view('exam-results.student', compact('attempt'));
    }

    public function gradeEssay(Request $request, Exam $exam, ExamAttempt $attempt, $questionId)
    {
        $this->authorize('grade', $exam);

        $validator = Validator::make($request->all(), [
            'score' => 'required|numeric|min:0|max:' . $request->input('max_marks'),
            'feedback' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $studentAnswer = StudentAnswer::where('attempt_id', $attempt->id)
            ->where('question_id', $questionId)
            ->firstOrFail();

        $studentAnswer->update([
            'score' => $request->score,
            'feedback' => $request->feedback,
            'needs_manual_grading' => false,
        ]);

        // Recalculate total score
        $this->recalculateScore($attempt);

        return back()->with('success', 'Essay graded successfully!');
    }

    public function bulkGrade(Request $request, Exam $exam)
    {
        $this->authorize('grade', $exam);

        $scores = $request->input('scores', []);
        $feedbacks = $request->input('feedbacks', []);

        foreach ($scores as $answerId => $score) {
            $studentAnswer = StudentAnswer::findOrFail($answerId);
            
            if ($studentAnswer->needs_manual_grading) {
                $studentAnswer->update([
                    'score' => $score,
                    'feedback' => $feedbacks[$answerId] ?? null,
                    'needs_manual_grading' => false,
                ]);
            }
        }

        // Recalculate scores for all affected attempts
        $attemptIds = StudentAnswer::whereIn('id', array_keys($scores))
            ->pluck('attempt_id')
            ->unique();

        foreach ($attemptIds as $attemptId) {
            $attempt = ExamAttempt::findOrFail($attemptId);
            $this->recalculateScore($attempt);
        }

        return back()->with('success', 'Essays graded successfully!');
    }

    public function analytics(Exam $exam)
    {
        $this->authorize('viewAnalytics', $exam);

        $exam->load(['questions', 'attempts', 'analytics.question']);

        // Overall statistics
        $stats = [
            'total_attempts' => $exam->attempts->count(),
            'completed_attempts' => $exam->attempts->where('status', 'submitted')->count(),
            'average_score' => $exam->attempts->where('status', 'submitted')->avg('percentage') ?? 0,
            'highest_score' => $exam->attempts->where('status', 'submitted')->max('percentage') ?? 0,
            'lowest_score' => $exam->attempts->where('status', 'submitted')->min('percentage') ?? 0,
            'average_duration' => $exam->attempts->where('status', 'submitted')->avg('duration_minutes') ?? 0,
        ];

        // Question performance
        $questionPerformance = $this->getQuestionPerformance($exam);

        // Score distribution
        $scoreDistribution = $this->getScoreDistribution($exam);

        // Time analysis
        $timeAnalysis = $this->getTimeAnalysis($exam);

        return view('exam-results.analytics', compact('exam', 'stats', 'questionPerformance', 'scoreDistribution', 'timeAnalysis'));
    }

    public function exportResults(Exam $exam)
    {
        $this->authorize('export', $exam);

        return Excel::download(new ExamResultsExport($exam), "exam_{$exam->id}_results.xlsx");
    }

    public function releaseResults(Exam $exam)
    {
        $this->authorize('manage', $exam);

        $exam->settings->update([
            'show_results_immediately' => true,
            'results_release_date' => now(),
        ]);

        return back()->with('success', 'Results have been released to students!');
    }

    public function generateReport(Exam $exam)
    {
        $this->authorize('viewAnalytics', $exam);

        $exam->load(['attempts.student', 'questions.studentAnswers']);

        $report = [
            'exam_info' => [
                'title' => $exam->title,
                'subject' => $exam->subject,
                'total_questions' => $exam->questions->count(),
                'total_marks' => $exam->questions->sum('marks'),
                'duration_minutes' => $exam->settings->duration_minutes,
                'passing_score' => $exam->settings->passing_score,
            ],
            'participation' => [
                'total_students' => $exam->courses->flatMap(function($course) {
                    return $course->users;
                })->unique()->count(),
                'total_attempts' => $exam->attempts->count(),
                'completion_rate' => $this->calculateCompletionRate($exam),
            ],
            'performance' => [
                'average_score' => $exam->attempts->where('status', 'submitted')->avg('percentage') ?? 0,
                'pass_rate' => $this->calculatePassRate($exam),
                'grade_distribution' => $this->getGradeDistribution($exam),
            ],
            'question_analysis' => $this->getQuestionPerformance($exam),
            'recommendations' => $this->generateRecommendations($exam),
        ];

        return view('exam-results.report', compact('exam', 'report'));
    }

    private function calculatePassRate($exam)
    {
        $passingScore = $exam->settings->passing_score ?? 50;
        $completedAttempts = $exam->attempts->where('status', 'submitted');
        
        if ($completedAttempts->isEmpty()) {
            return 0;
        }

        $passedCount = $completedAttempts->where('percentage', '>=', $passingScore)->count();
        return ($passedCount / $completedAttempts->count()) * 100;
    }

    private function calculateCompletionRate($exam)
    {
        $totalStudents = $exam->courses->flatMap(function($course) {
            return $course->users;
        })->unique()->count();

        if ($totalStudents === 0) {
            return 0;
        }

        $completedAttempts = $exam->attempts->where('status', 'submitted')->count();
        return ($completedAttempts / $totalStudents) * 100;
    }

    private function getQuestionPerformance($exam)
    {
        return $exam->questions->map(function($question) {
            $answers = $question->studentAnswers;
            $totalAttempts = $answers->count();
            $correctAttempts = $answers->where('is_correct', true)->count();
            
            return [
                'id' => $question->id,
                'text' => substr($question->text, 0, 100) . '...',
                'type' => $question->question_type,
                'total_attempts' => $totalAttempts,
                'correct_attempts' => $correctAttempts,
                'success_rate' => $totalAttempts > 0 ? ($correctAttempts / $totalAttempts) * 100 : 0,
                'average_time' => $answers->avg('answered_at') ?? 0,
                'difficulty_level' => $question->difficulty_level,
            ];
        });
    }

    private function getScoreDistribution($exam)
    {
        $scores = $exam->attempts->where('status', 'submitted')->pluck('percentage');
        
        $ranges = [
            '0-20' => 0, '21-40' => 0, '41-60' => 0, '61-80' => 0, '81-100' => 0
        ];

        foreach ($scores as $score) {
            if ($score <= 20) $ranges['0-20']++;
            elseif ($score <= 40) $ranges['21-40']++;
            elseif ($score <= 60) $ranges['41-60']++;
            elseif ($score <= 80) $ranges['61-80']++;
            else $ranges['81-100']++;
        }

        return $ranges;
    }

    private function getTimeAnalysis($exam)
    {
        $durations = $exam->attempts->where('status', 'submitted')->pluck('duration_minutes');
        
        return [
            'average' => $durations->avg() ?? 0,
            'minimum' => $durations->min() ?? 0,
            'maximum' => $durations->max() ?? 0,
            'allowed_duration' => $exam->settings->duration_minutes,
        ];
    }

    private function getGradeDistribution($exam)
    {
        $scores = $exam->attempts->where('status', 'submitted')->pluck('percentage');
        
        $grades = [
            'A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0
        ];

        foreach ($scores as $score) {
            if ($score >= 80) $grades['A']++;
            elseif ($score >= 70) $grades['B']++;
            elseif ($score >= 60) $grades['C']++;
            elseif ($score >= 50) $grades['D']++;
            else $grades['F']++;
        }

        return $grades;
    }

    private function recalculateScore($attempt)
    {
        $totalScore = $attempt->studentAnswers->sum('score');
        $totalPossible = $attempt->exam->questions->sum('marks');
        $percentage = $totalPossible > 0 ? ($totalScore / $totalPossible) * 100 : 0;

        $attempt->update([
            'total_score' => $totalScore,
            'percentage' => $percentage,
        ]);
    }

    private function generateRecommendations($exam)
    {
        $recommendations = [];
        
        // Analyze question difficulty
        $hardQuestions = $exam->questions->filter(function($question) {
            $successRate = $question->studentAnswers->count() > 0 
                ? ($question->studentAnswers->where('is_correct', true)->count() / $question->studentAnswers->count()) * 100 
                : 0;
            return $successRate < 30;
        });

        if ($hardQuestions->count() > 0) {
            $recommendations[] = "Consider reviewing or simplifying {$hardQuestions->count()} difficult questions that had success rates below 30%.";
        }

        // Analyze time management
        $avgDuration = $exam->attempts->where('status', 'submitted')->avg('duration_minutes') ?? 0;
        if ($avgDuration > $exam->settings->duration_minutes * 0.9) {
            $recommendations[] = "Students are taking close to the full time. Consider extending the duration or reducing question count.";
        }

        // Analyze participation
        $completionRate = $this->calculateCompletionRate($exam);
        if ($completionRate < 70) {
            $recommendations[] = "Low completion rate ({$completionRate}%). Consider sending reminders or extending the exam window.";
        }

        return $recommendations;
    }
}
