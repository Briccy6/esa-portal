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
        'assignment_id',
        'term_id',
        'exam_id',
        'value',
        'grade_id',
        'status',
        'assessment_type',
        'marks_out_of',
        'test_date',
        'exam_date',
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

    public function assignment()
    {
        return $this->belongsTo(\App\Models\LessonAssignment::class, 'assignment_id');
    }

    public function term()
    {
        return $this->belongsTo(\App\Models\Term::class);
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

    // Accessor for normalized marks (always out of 100)
    public function getNormalizedValueAttribute()
    {
        if (in_array($this->assessment_type, ['test', 'assignment'], true) && $this->marks_out_of > 0) {
            return round(($this->value / $this->marks_out_of) * 100, 2);
        }
        return $this->value; // Exams are already out of 100
    }

    // Accessor for normalized percentage with % symbol
    public function getNormalizedPercentageAttribute()
    {
        return $this->normalized_value . '%';
    }
}
