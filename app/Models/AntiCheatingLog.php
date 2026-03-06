<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AntiCheatingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_session_id', 'student_id', 'violation_type', 'description', 'occurred_at', 'metadata'
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function examSession()
    {
        return $this->belongsTo(ExamSession::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public static function logViolation($examSessionId, $studentId, $violationType, $description = null, $metadata = [])
    {
        return self::create([
            'exam_session_id' => $examSessionId,
            'student_id' => $studentId,
            'violation_type' => $violationType,
            'description' => $description,
            'occurred_at' => now(),
            'metadata' => $metadata
        ]);
    }
}
