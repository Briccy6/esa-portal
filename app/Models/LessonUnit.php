<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LessonUnit extends Model
{
    use HasFactory;

  protected $fillable = [
    'lesson_id',
    'term_id',
    'unit_name',
    'hours',
    'course_level',
    'file_path',
];


    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
    public function term()
{
    return $this->belongsTo(Term::class);
}

}
