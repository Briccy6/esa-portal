<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    /**
     * Competition-style ranks for one cohort (same course, year, term).
     * Call once after bulk generation so every card exists before ranking.
     * Ties share the same rank; next rank skips (e.g. 1, 1, 3).
     */
    public static function recalculatePositionsForCohort(int $courseId, int $academicYearId, int $termId): void
    {
        $cards = static::where('course_id', $courseId)
            ->where('academic_year_id', $academicYearId)
            ->where('term_id', $termId)
            ->orderByDesc('average_marks')
            ->orderBy('id')
            ->get();

        if ($cards->count() === 0) {
            return;
        }

        $studentIdsInClass = Student::where('course_id', $courseId)
            ->where('academic_year_id', $academicYearId)
            ->pluck('id')
            ->all();

        $totalStudentsInClass = count($studentIdsInClass);
        if ($totalStudentsInClass === 0) {
            return;
        }

        // Compute cohort-wide ranking baseline from marks so generated cards
        // get positions against the full class, not only generated report cards.
        $averagesByStudent = DB::table('marks')
            ->selectRaw("
                student_id,
                AVG(
                    CASE
                        WHEN assessment_type = 'test' THEN
                            CASE
                                WHEN marks_out_of IS NOT NULL AND marks_out_of > 0
                                    THEN (value / marks_out_of) * 100
                                ELSE value
                            END
                        WHEN assessment_type = 'exam' THEN
                            CASE
                                WHEN marks_out_of IS NOT NULL AND marks_out_of > 0
                                    THEN (value / marks_out_of) * 100
                                ELSE value
                            END
                        ELSE value
                    END
                ) AS cohort_avg
            ")
            ->where('course_id', $courseId)
            ->where('term_id', $termId)
            ->whereIn('student_id', $studentIdsInClass)
            ->groupBy('student_id')
            ->pluck('cohort_avg', 'student_id');

        $scoreBoard = collect($studentIdsInClass)->map(function ($studentId) use ($averagesByStudent) {
            return [
                'student_id' => (int) $studentId,
                // Students without marks are ranked at the end.
                'score' => $averagesByStudent->has($studentId)
                    ? (float) $averagesByStudent[$studentId]
                    : -1.0,
            ];
        })->sortByDesc('score')->values();

        $rankByStudent = [];
        $rank = 1;
        foreach ($scoreBoard as $i => $entry) {
            if ($i > 0) {
                $prev = $scoreBoard[$i - 1];
                if ((float) $entry['score'] < (float) $prev['score']) {
                    $rank = $i + 1;
                }
            }
            $rankByStudent[(int) $entry['student_id']] = $rank;
        }

        foreach ($cards as $card) {
            $card->total_students = $totalStudentsInClass;
            $card->class_position = $rankByStudent[(int) $card->student_id] ?? null;
            $card->saveQuietly();
        }
    }

    public function calculateClassPosition(): void
    {
        static::recalculatePositionsForCohort(
            (int) $this->course_id,
            (int) $this->academic_year_id,
            (int) $this->term_id
        );
    }
}
