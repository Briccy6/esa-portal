<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Lesson;

class Course extends Model
{
    use HasFactory;

  protected $fillable = [
    'course_name',
    'abbreviation',
    'course_type',
    'academic_year_id',
    'course_code',
    'registration_fee',
    'tuition_fee',
    'practical_fee',
    'trip_fee',
    'discount_fee',
];
     public function lessons()
    {
        return $this->hasMany(Lesson::class, 'course_id');
    }
    public function branches()
    {
        return $this->belongsToMany(\App\Models\Branch::class, 'branch_course');
    }
public function academicYear()
{
    return $this->belongsTo(AcademicYear::class);
}
 public function students()
    {
        return $this->hasMany(Student::class);
    }
    public function transcripts()
    {
        return $this->hasMany(Transcript::class);
    }
    public function exams()
    {
        return $this->belongsToMany(Exam::class);
    }
    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    // Accessor for total cost (excluding registration fee, applying discount as %)
    public function getTotalCostAttribute()
    {
        $subtotal = floatval($this->tuition_fee) + floatval($this->practical_fee) + floatval($this->trip_fee);
        $discount = floatval($this->discount_fee);

        if ($discount > 0 && $discount <= 100) {
            return round($subtotal - ($subtotal * ($discount / 100)), 2);
        }
        return round($subtotal, 2);
    }
    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class);
    }
    public function teachers()
    {
        return $this->hasManyThrough(
            \App\Models\Teacher::class,    // Final model
            \App\Models\Lesson::class,     // Intermediate model
            'course_id',                   // Foreign key on lessons table
            'id',                          // Foreign key on teachers table (assuming lessons has teacher_id)
            'id',                          // Local key on courses table
            'teacher_id'                   // Local key on lessons table
        );
    }
}
