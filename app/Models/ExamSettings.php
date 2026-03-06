<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id', 'duration_minutes', 'start_time', 'end_time', 'randomize_questions',
        'randomize_answers', 'questions_per_page', 'show_results_immediately',
        'show_correct_answers', 'show_explanations', 'results_release_date',
        'allow_retakes', 'max_attempts', 'passing_score', 'require_fullscreen',
        'disable_right_click', 'disable_copy_paste', 'max_tab_switches',
        'auto_submit_on_violation'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'results_release_date' => 'datetime',
        'passing_score' => 'decimal:2',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function isCurrentlyActive()
    {
        $now = now();
        return (!$this->start_time || $now >= $this->start_time) && 
               (!$this->end_time || $now <= $this->end_time);
    }

    public function canShowResults()
    {
        if ($this->show_results_immediately) {
            return true;
        }
        
        return $this->results_release_date && now() >= $this->results_release_date;
    }
}
