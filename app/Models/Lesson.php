<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Term; 
use App\Models\Course;
use App\Models\Teacher;
use App\Models\LessonUnit;
use App\Models\AcademicYear;

class Lesson extends Model
{
    protected $fillable = [
        'teacher_id',
        'course_id',          // Make sure this is fillable if you want to mass assign it
        'title',
        'lesson_weight',
        'max_test_marks',
        'max_exam_marks',
        'academic_year_id',
        'status',
        'ended_at',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function terms()
    {
        return $this->belongsToMany(Term::class); // expects pivot table lesson_term
    }
    public function units()
{
    return $this->hasMany(LessonUnit::class);
}
public function exams()
{
    return $this->belongsToMany(\App\Models\Exam::class, 'lesson_exam');
}

// Multi-branch relationships
public function branchCourses()
{
    return $this->hasMany(\App\Models\LessonBranchCourse::class);
}

public function branches()
{
    return $this->belongsToMany(\App\Models\Branch::class, 'lesson_branch_course')
        ->withPivot('course_id', 'teacher_id');
}

public function courses()
{
    return $this->belongsToMany(\App\Models\Course::class, 'lesson_branch_course')
        ->withPivot('branch_id', 'teacher_id');
}

public function teachers()
{
    return $this->belongsToMany(\App\Models\Teacher::class, 'lesson_branch_course')
        ->withPivot('branch_id', 'course_id');
}

}
