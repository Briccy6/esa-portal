<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'term_id',
        'course_id',
        'total_marks',
        'average_marks',
        'class_position',
        'total_students',
        'grade',
        'remarks',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'total_marks' => 'decimal:2',
        'average_marks' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function subjects()
    {
        return $this->hasMany(ReportCardSubject::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'approved_by');
    }

    public function getGradeAttribute($value)
    {
        if ($value) {
            return $value;
        }

        // Auto-calculate grade based on average
        $average = $this->average_marks;
        if ($average >= 90) return 'A+';
        if ($average >= 80) return 'A';
        if ($average >= 70) return 'B+';
        if ($average >= 60) return 'B';
        if ($average >= 50) return 'C';
        if ($average >= 40) return 'D';
        return 'F';
    }

    public function getPositionDisplayAttribute()
    {
        if ($this->class_position && $this->total_students) {
            return "{$this->class_position}/{$this->total_students}";
        }
        return 'N/A';
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    public function scopeForTerm($query, $termId)
    {
        return $query->where('term_id', $termId);
    }

    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function calculateClassPosition()
    {
        // Get all students in the same course, academic year, and term
        // Include all report cards regardless of status to get accurate positioning
        $classmates = static::where('academic_year_id', $this->academic_year_id)
            ->where('term_id', $this->term_id)
            ->where('course_id', $this->course_id)
            ->orderByDesc('average_marks')
            ->get();

        $this->total_students = $classmates->count();
        
        $position = 1;
        foreach ($classmates as $classmate) {
            if ($classmate->average_marks > $this->average_marks) {
                $position++;
            } elseif ($classmate->id === $this->id) {
                break;
            }
        }
        
        $this->class_position = $position;
        $this->save();
    }
}
