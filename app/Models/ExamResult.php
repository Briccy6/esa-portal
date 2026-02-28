<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'exam_id',
        'score',
        'total_marks',
        'percentage',
        'grade',
        'status',
        'started_at',
        'completed_at',
        'time_taken', // in minutes
        'answers'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'answers' => 'array'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    // Calculate grade based on percentage
    public function calculateGrade()
    {
        $percentage = $this->percentage;
        
        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        if ($percentage >= 50) return 'E';
        return 'F';
    }

    // Check if passed
    public function isPassed()
    {
        return $this->percentage >= ($this->exam->pass_marks ?? 50);
    }
}