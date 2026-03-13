<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id', 'question_id', 'total_attempts', 'correct_attempts', 'success_rate',
        'average_time_seconds', 'difficulty_score', 'option_a_count', 'option_b_count',
        'option_c_count', 'option_d_count'
    ];

    protected $casts = [
        'success_rate' => 'decimal:2',
        'average_time_seconds' => 'decimal:2',
        'difficulty_score' => 'decimal:2',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function updateAnalytics()
    {
        $totalAttempts = StudentAnswer::where('question_id', $this->question_id)->count();
        $correctAttempts = StudentAnswer::where('question_id', $this->question_id)
            ->where('is_correct', true)->count();
        
        $this->total_attempts = $totalAttempts;
        $this->correct_attempts = $correctAttempts;
        $this->success_rate = $totalAttempts > 0 ? ($correctAttempts / $totalAttempts) * 100 : 0;
        
        // Calculate average time spent on this question
        $avgTime = StudentAnswer::where('question_id', $this->question_id)
            ->whereNotNull('answered_at')
            ->avg('answered_at');
        
        $this->average_time_seconds = $avgTime ?? 0;
        
        // Update answer distribution for MCQ
        if ($this->question->question_type === 'multiple_choice') {
            $this->updateAnswerDistribution();
        }
        
        $this->save();
    }

    private function updateAnswerDistribution()
    {
        $this->option_a_count = StudentAnswer::where('question_id', $this->question_id)
            ->where('answer', 'A')->count();
        $this->option_b_count = StudentAnswer::where('question_id', $this->question_id)
            ->where('answer', 'B')->count();
        $this->option_c_count = StudentAnswer::where('question_id', $this->question_id)
            ->where('answer', 'C')->count();
        $this->option_d_count = StudentAnswer::where('question_id', $this->question_id)
            ->where('answer', 'D')->count();
    }
}
