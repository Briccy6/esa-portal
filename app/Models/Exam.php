<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subject',
        'description',
        'class_date',
        'exam_weight',
        'total_marks',
        'academic_year_id',
        'teacher_id',
        'term_id',
        'notes',
        'exam_password',
        'is_active',
    ];

    // Replace the course() method with courses()
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_exam');
    }

    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_exam');
    }

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    public function questions()
    {
        return $this->hasMany(\App\Models\Question::class);
    }
}
