<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonBranchCourse extends Model
{
    protected $fillable = [
        'lesson_id',
        'branch_id',
        'course_id',
        'teacher_id',
    ];

    protected $table = 'lesson_branch_course';

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
