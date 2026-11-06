<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherDaily extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'course_id',
        'lesson_id',
        'unit_id',
        'branch_id',
        'activity',
        'hours_studied',
        'total_students',
        'absences',
        'quiz_link',
        'marking_guide',
        'total_marks',
        'challenges',
        'comment',
        'date',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function unit()
    {
        return $this->belongsTo(LessonUnit::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
