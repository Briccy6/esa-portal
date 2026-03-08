<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'branch_id',
        'lesson_id',
        'exam_id',
        'value',
        'grade_id',
        'status',
        'assessment_type',
        'marks_out_of'
    ];

    public function grade()
    {
        return $this->belongsTo(\App\Models\Grade::class);
    }

    public function student()
    {
        return $this->belongsTo(\App\Models\Student::class);
    }

    public function course()
    {
        return $this->belongsTo(\App\Models\Course::class);
    }

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class);
    }

    public function exam()
    {
        return $this->belongsTo(\App\Models\Exam::class);
    }

    public function lesson()
    {
        return $this->belongsTo(\App\Models\Lesson::class);
    }

    // Optional accessors
    public function getGradeNameAttribute()
    {
        return $this->grade?->name;
    }

    public function getValueAttribute($value)
    {
        return $value !== null ? (int)$value : null;
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = $value !== null ? (int)$value : null;
    }
}
