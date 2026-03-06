<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Mark;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'exam_type', 'class_date', 'exam_weight', 'total_marks', 
        'academic_year_id', 'teacher_id', 'term_id', 'notes', 'exam_password', 'is_active',
    ];

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
        return $this->hasMany(Question::class);
    }

    public function settings()
    {
        return $this->hasOne(ExamSettings::class);
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function sessions()
    {
        return $this->hasMany(ExamSession::class);
    }

    public function analytics()
    {
        return $this->hasMany(ExamAnalytics::class);
    }
}
