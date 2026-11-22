<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    protected $fillable = [
        'lesson_assignment_id',
        'student_id',
        'file_path',
        'original_file_name',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function assignment()
    {
        return $this->belongsTo(LessonAssignment::class, 'lesson_assignment_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
