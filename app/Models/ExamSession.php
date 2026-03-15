<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id', 'student_id', 'client_id', 'attempt_id', 'session_start', 'last_activity',
        'time_remaining_seconds', 'is_fullscreen', 'tab_switches', 'fullscreen_violations',
        'question_order', 'answer_orders', 'data', 'started_at', 'completed_at', 'score'
    ];

    protected $casts = [
        'session_start' => 'datetime',
        'last_activity' => 'datetime',
        'question_order' => 'array',
        'answer_orders' => 'array',
        'data' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'score' => 'decimal:2',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    public function antiCheatingLogs()
    {
        return $this->hasMany(AntiCheatingLog::class);
    }

    public function isExpired()
    {
        return $this->time_remaining_seconds <= 0;
    }

    public function hasTooManyViolations()
    {
        $maxViolations = $this->exam->settings->max_tab_switches ?? 3;
        return $this->tab_switches >= $maxViolations;
    }
}
