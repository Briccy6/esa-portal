<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id', 'student_id', 'attempt_number', 'started_at', 'finished_at', 
        'duration_minutes', 'total_score', 'percentage', 'status', 'ip_address', 
        'user_agent', 'violations'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'total_score' => 'decimal:2',
        'percentage' => 'decimal:2',
        'violations' => 'array',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function session()
    {
        return $this->hasOne(ExamSession::class, 'attempt_id');
    }

    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class, 'attempt_id');
    }

    public function antiCheatingLogs()
    {
        return $this->hasMany(AntiCheatingLog::class, 'exam_session_id');
    }

    public function isPassed()
    {
        $passingScore = $this->exam->settings->passing_score ?? 50;
        return $this->percentage >= $passingScore;
    }
}
