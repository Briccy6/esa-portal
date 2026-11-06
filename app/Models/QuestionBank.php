<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\Question;

class QuestionBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'teacher_id', 'course_id', 'lesson_id', 'academic_year_id', 'is_active'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function lesson()
    {
        return $this->belongsTo(\App\Models\Lesson::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
