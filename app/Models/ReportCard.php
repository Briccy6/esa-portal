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

    /**
     * Dynamically calculate position and total students based on current average marks.
     * This ensures the position always reflects the current ranking.
     * 
     * @return array{position: int|null, total: int}
     */
    public function calculateDynamicPosition(): array
    {
        // Get all students in the same cohort (course, academic year, term)
        $cohortCards = static::where('course_id', $this->course_id)
            ->where('academic_year_id', $this->academic_year_id)
            ->where('term_id', $this->term_id)
            ->whereNotNull('average_marks')
            ->orderByDesc('average_marks')
            ->orderBy('id') // Tiebreaker: use ID if marks are equal
            ->get();

        $totalStudents = static::where('course_id', $this->course_id)
            ->where('academic_year_id', $this->academic_year_id)
            ->where('term_id', $this->term_id)
            ->count();

        // Find this student's position in the cohort (1-indexed)
        $position = null;
        $currentRank = 1;
        $previousScore = null;

        foreach ($cohortCards as $index => $card) {
            // Handle ties: if marks are equal, keep same rank
            if ($previousScore !== null && $card->average_marks != $previousScore) {
                $currentRank = $index + 1;
            }
            $previousScore = $card->average_marks;

            if ((int) $card->id === (int) $this->id) {
                $position = $currentRank;
                break;
            }
        }

        return [
            'position' => $position,
            'total' => $totalStudents,
        ];
    }

    /**
     * Get calculated position and total students.
     * Formats as "position/total" string for display.
     */
    public function getCalculatedPositionDisplayAttribute(): string
    {
        $data = $this->calculateDynamicPosition();
        if ($data['position'] !== null && $data['total'] > 0) {
            return "{$data['position']}/{$data['total']}";
        }
        return 'N/A';
    }

    /**
     * Get calculated position number only.
     */
    public function getCalculatedPositionAttribute(): ?int
    {
        return $this->calculateDynamicPosition()['position'];
    }

    /**
     * Get total students in cohort.
     */
    public function getCalculatedTotalAttribute(): int
    {
        return $this->calculateDynamicPosition()['total'];
    }

    /**
     * Legacy method - kept for backwards compatibility.
     * Always uses dynamic calculation to ensure accuracy.
     * Database values are maintained for historical reference only.
     */
    public function getPositionDisplayAttribute()
    {
        // Always use dynamic calculation for current accuracy
        return $this->calculated_position_display;
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
        \Log::info('🔄 RECALCULATE POSITIONS START', [
            'course_id' => $courseId,
            'academic_year_id' => $academicYearId,
            'term_id' => $termId,
        ]);

        $cards = static::where('course_id', $courseId)
            ->where('academic_year_id', $academicYearId)
            ->where('term_id', $termId)
            ->orderByDesc('average_marks')
            ->orderBy('id')
            ->with('student')
            ->get();

        \Log::info('Found report cards:', [
            'count' => $cards->count(),
            'cards' => $cards->map(fn($c) => [
                'id' => $c->id,
                'student_name' => $c->student->full_name ?? 'Unknown',
                'average_marks' => $c->average_marks,
            ])->all(),
        ]);

        if ($cards->count() === 0) {
            \Log::warning('❌ No report cards found for this cohort');
            return;
        }

        $studentIdsInClass = Student::where('course_id', $courseId)
            ->where('academic_year_id', $academicYearId)
            ->pluck('id')
            ->all();

        \Log::info('Students in class:', [
            'total_count' => count($studentIdsInClass),
            'student_ids' => $studentIdsInClass,
        ]);

        $totalStudentsInClass = count($studentIdsInClass);
        if ($totalStudentsInClass === 0) {
            \Log::warning('⚠️ No students found in class');
            return;
        }

        // Compute cohort-wide ranking baseline from marks so generated cards
        // get positions against the full class, not only generated report cards.
        $averagesByStudent = DB::table('marks')
            ->selectRaw("
                student_id,
                AVG(
                    CASE
                        WHEN assessment_type IN ('test', 'assignment') THEN
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

        \Log::info('Averages from marks table:', [
            'count' => $averagesByStudent->count(),
            'data' => $averagesByStudent->all(),
        ]);

        $scoreBoard = collect($studentIdsInClass)->map(function ($studentId) use ($averagesByStudent) {
            return [
                'student_id' => (int) $studentId,
                // Students without marks are ranked at the end.
                'score' => $averagesByStudent->has($studentId)
                    ? (float) $averagesByStudent[$studentId]
                    : -1.0,
            ];
        })->sortByDesc('score')->values();

        \Log::info('Sorted scoreBoard:', [
            'count' => $scoreBoard->count(),
            'scores' => $scoreBoard->all(),
        ]);

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

        \Log::info('Rank assignments:', [
            'count' => count($rankByStudent),
            'ranks' => $rankByStudent,
        ]);

        $updateCount = 0;
        foreach ($cards as $card) {
            $newPosition = $rankByStudent[(int) $card->student_id] ?? null;
            $card->total_students = $totalStudentsInClass;
            $card->class_position = $newPosition;
            $card->saveQuietly();
            $updateCount++;
            
            \Log::info("Updated position for student {$card->student->full_name}", [
                'card_id' => $card->id,
                'student_id' => $card->student_id,
                'new_position' => $newPosition,
                'total_students' => $totalStudentsInClass,
            ]);
        }

        \Log::info("✅ RECALCULATE POSITIONS COMPLETE", [
            'updated_count' => $updateCount,
            'course_id' => $courseId,
            'academic_year_id' => $academicYearId,
            'term_id' => $termId,
        ]);
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
