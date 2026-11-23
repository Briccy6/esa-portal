<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonAssignment extends Model
{
    protected $fillable = [
        'lesson_id',
        'branch_id',
        'teacher_id',
        'term_id',
        'title',
        'description',
        'question',
        'submission_method',
        'allowed_file_types',
        'attachment_path',
        'attachment_original_name',
        'due_at',
        'marks_out_of',
    ];

    protected $casts = [
        'allowed_file_types' => 'array',
        'due_at' => 'datetime',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function marks()
    {
        return $this->hasMany(Mark::class, 'assignment_id');
    }
}
