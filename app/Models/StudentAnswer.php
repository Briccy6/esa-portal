<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id', 'question_id', 'answer', 'selected_options', 'score', 
        'is_correct', 'feedback', 'needs_manual_grading', 'answered_at'
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'selected_options' => 'array',
        'answered_at' => 'datetime',
    ];

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function isCorrect()
    {
        if ($this->question->question_type === 'multiple_choice') {
            return $this->answer === $this->question->correct_answer;
        } elseif ($this->question->question_type === 'true_false') {
            return $this->answer === $this->question->correct_answer;
        } elseif ($this->question->question_type === 'multiple_select') {
            $correctAnswers = $this->question->correct_answers ?? [];
            $selectedOptions = $this->selected_options ?? [];
            sort($correctAnswers);
            sort($selectedOptions);
            return $correctAnswers === $selectedOptions;
        } elseif ($this->question->question_type === 'short_answer') {
            return $this->checkShortAnswer();
        }
        
        return false; // Essay questions need manual grading
    }

    private function checkShortAnswer()
    {
        $keywords = $this->question->keywords ?? '';
        $studentAnswer = strtolower($this->answer);
        $keywordList = array_map('trim', explode(',', strtolower($keywords)));
        
        foreach ($keywordList as $keyword) {
            if (strpos($studentAnswer, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }
}
