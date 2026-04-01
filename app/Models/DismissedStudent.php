<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;

class DismissedStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        // Student information
        'full_name',
        'sex',
        'email',
        'phone',
        'branch_id',
        'course_id',
        'reg_number',
        'password',
        'payed_registration_fees',
        'academic_year_id',
        'id_number',
        'identification_type',
        'id_photo',
        'passport_photo',
        'senior6_document',
        'senior3_document',
        'other_supporting_document',
        'nesa_registered',
        'nesa_sdms_code',
        'nesa_reg_number',
        'nesa_document',
        'nesa_registration_date',
        'nesa_remarks',
        'discount',
        'education_level',
        
        // Dismissal specific fields
        'dismissal_reason',
        'dismissal_notes',
        'dismissed_by',
        'dismissal_date',
        'original_student_id',
    ];

    protected $casts = [
        'nesa_registered' => 'boolean',
        'payed_registration_fees' => 'boolean',
        'nesa_registration_date' => 'date',
        'dismissal_date' => 'date',
        'discount' => 'decimal:2',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Relationships
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function dismissedBy()
    {
        return $this->belongsTo(Admin::class, 'dismissed_by');
    }

    /**
     * Scopes
     */
    public function scopeByDismissalDate($query, $startDate, $endDate = null)
    {
        if ($endDate) {
            return $query->whereBetween('dismissal_date', [$startDate, $endDate]);
        }
        return $query->whereDate('dismissal_date', $startDate);
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Accessors
     */
    public function getFormattedDismissalDateAttribute()
    {
        return $this->dismissal_date->format('d M Y');
    }

    public function getDismissalReasonSummaryAttribute()
    {
        return strlen($this->dismissal_reason) > 50 
            ? substr($this->dismissal_reason, 0, 50) . '...' 
            : $this->dismissal_reason;
    }

    /**
     * Static method to create dismissed student from existing student
     */
    public static function isRegNumberDismissed(string $regNumber): bool
    {
        return static::where('reg_number', $regNumber)->exists();
    }

    public static function dismissStudent(Student $student, $dismissalReason, $dismissalNotes = null, $dismissedById = null)
    {
        $dismissedStudent = new self();

        $table = (new self())->getTable();
        $columns = Schema::hasTable($table)
            ? array_flip(Schema::getColumnListing($table))
            : [];

        $skip = array_flip(['id', 'created_at', 'updated_at', 'dismissal_reason', 'dismissal_notes', 'dismissed_by', 'dismissal_date', 'original_student_id']);

        foreach ($student->getAttributes() as $key => $value) {
            if (isset($skip[$key])) {
                continue;
            }
            if ($columns !== [] && !isset($columns[$key])) {
                continue;
            }
            $dismissedStudent->$key = $value;
        }

        $dismissedStudent->dismissal_reason = $dismissalReason;
        $dismissedStudent->dismissal_notes = $dismissalNotes;
        $dismissedStudent->dismissed_by = $dismissedById ?? auth('admin')->id();
        $dismissedStudent->dismissal_date = now();
        $dismissedStudent->original_student_id = $student->id;

        return $dismissedStudent;
    }
}
