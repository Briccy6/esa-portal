<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Exam;
use App\Models\QuestionBank;
use App\Models\StudentAnswer;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id', 'question_bank_id', 'text', 'question_type', 'option_a', 'option_b', 'option_c', 'option_d', 
        'correct_answer', 'correct_answers', 'marks', 'weight', 'difficulty_level', 'explanation', 'keywords',
        'subject_area', 'biology_topic', 'diagram_labels', 'question_image'
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function questionBank()
    {
        return $this->belongsTo(QuestionBank::class);
    }

    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class);
    }

    public function analytics()
    {
        return $this->hasMany(ExamAnalytics::class);
    }
       
    protected $casts = [
        'exam_id' => 'integer',
        'question_bank_id' => 'integer',
        'marks' => 'integer',
        'weight' => 'decimal:2',
        'difficulty_level' => 'integer',
        'correct_answers' => 'array',
        'options' => 'array',
    ];
    
    public function getImageUrlAttribute()
    {
        if ($this->question_image) {
            return asset('images/question_images/' . $this->question_image);
        }
        return null;
    }
    
    public function getFormattedTextAttribute()
    {
        // Return text as-is - MathJax will handle LaTeX rendering
        return $this->text;
    }
    
    public function getOptionsAttribute()
    {
        return [
            $this->option_a,
            $this->option_b,
            $this->option_c,
            $this->option_d,
        ];
    }
}
