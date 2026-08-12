<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AcademicYear;
use App\Models\Lesson;
use App\Models\Term;

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

    public function academicYears()
    {
        return $this->belongsToMany(AcademicYear::class, 'academic_year_course', 'course_id', 'academic_year_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
    public function exams()
    {
        return $this->belongsToMany(Exam::class);
    }
    public function marks()
    {
        return $this->hasMany(Mark::class);
    }
    public function resources()
    {
        return $this->hasMany(Resource::class);
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
    public function teachersManyToMany()
    {
        return $this->belongsToMany(\App\Models\Teacher::class, 'teacher_course');
    }

    public function courseTermFees()
    {
        return $this->hasMany(CourseTermFee::class);
    }

    public function courseTermBranchFees()
    {
        return $this->hasMany(\App\Models\CourseTermBranchFee::class);
    }

    public function activeTermFees()
    {
        return $this->hasMany(CourseTermFee::class)->where('is_active', true);
    }

    public function getFeesForTerm($termId)
    {
        return $this->courseTermFees()->where('term_id', $termId)->where('is_active', true)->first();
    }

    public function getFeesForTermAndBranch($termId, $branchId = null)
    {
        if ($branchId && $termId) {
            $branchFee = $this->courseTermBranchFees()
                ->where('branch_id', $branchId)
                ->where('term_id', $termId)
                ->where('is_active', true)
                ->first();

            if ($branchFee) {
                return $branchFee;
            }
        }

        return $this->getFeesForTerm($termId);
    }

    public function getCurrentTermFees()
    {
        $currentTerm = Term::where('start_date', '<=', now())
                           ->where('end_date', '>=', now())
                           ->first();
        
        if ($currentTerm) {
            return $this->getFeesForTerm($currentTerm->id);
        }
        
        // Fallback to legacy fee structure if no term fees found
        return $this->getLegacyFees();
    }

    public function getRegistrationFeeForAcademicYear($academicYearId = null)
    {
        if (floatval($this->registration_fee) > 0) {
            return floatval($this->registration_fee);
        }

        $feeQuery = $this->courseTermFees()->where('is_active', true);

        if ($academicYearId) {
            $feeQuery->whereHas('term', function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            });
        }

        $courseTermFee = $feeQuery->first();

        return $courseTermFee ? floatval($courseTermFee->registration_fee) : 0;
    }

    public function getActiveTermForAcademicYear($academicYearId)
    {
        if (!$academicYearId) {
            return null;
        }

        $term = Term::where('academic_year_id', $academicYearId)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if ($term) {
            return $term;
        }

        return Term::where('academic_year_id', $academicYearId)
            ->orderBy('start_date')
            ->first();
    }

    public function getRegistrationFeeForBranchAndAcademicYear($branchId = null, $academicYearId = null)
    {
        if ($branchId && $academicYearId) {
            $term = $this->getActiveTermForAcademicYear($academicYearId);

            if ($term) {
                $branchFee = $this->courseTermBranchFees()
                    ->where('branch_id', $branchId)
                    ->where('term_id', $term->id)
                    ->where('is_active', true)
                    ->first();

                if ($branchFee) {
                    return floatval($branchFee->registration_fee);
                }
            }
        }

        return $this->getRegistrationFeeForAcademicYear($academicYearId);
    }

    public function getLegacyFees()
    {
        return (object) [
            'registration_fee' => $this->registration_fee ?? 0,
            'tuition_fee' => $this->tuition_fee ?? 0,
            'practical_fee' => $this->practical_fee ?? 0,
            'trip_fee' => $this->trip_fee ?? 0,
            'student_card_fee' => 2000,
        ];
    }

    public function getPaymentSchedule()
    {
        return $this->activeTermFees()
                    ->with('term')
                    ->orderBy('term.start_date')
                    ->get();
    }

    public function getTotalYearFees()
    {
        return $this->activeTermFees()->sum(function($termFee) {
            return $termFee->total_fees;
        });
    }

    public function lessonBranchCourses()
    {
        return $this->hasMany(LessonBranchCourse::class);
    }
}
