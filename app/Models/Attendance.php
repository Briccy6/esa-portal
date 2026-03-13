<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'lesson_id',
  
        'date',
        'status',
        'marked_by',
        'marked_at',
        'approved',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function timetable()
    {
        return $this->belongsTo(Timetable::class);
    }
   
    
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
    
    // ...existing code...
}
