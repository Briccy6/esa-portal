<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToWhomLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'recipient_type',
        'recipient_name',
        'recipient_role',
        'generated_by',
        'letter_type',
        'recipient_title',
        'purpose',
        'content',
        'letter_date',
        'reference_number',
        'verification_token',
        'is_active',
    ];

    protected $casts = [
        'letter_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Letter types
    const TYPE_ENROLLMENT = 'enrollment';
    const TYPE_ACADEMIC_STANDING = 'academic_standing';
    const TYPE_FINANCIAL_STATUS = 'financial_status';
    const TYPE_COMPLETION = 'completion';
    const TYPE_GOOD_STANDING = 'good_standing';
    const TYPE_PROFESSIONAL_LETTER = 'professional_letter';
    const TYPE_CUSTOM = 'custom';
    const TYPE_PROOF_OF_REGISTRATION = 'proof_of_registration';
    const TYPE_STUDENT_CONFIRMATION = 'student_confirmation';
    const TYPE_TO_WHOM = 'to_whom';
    const TYPE_EMPLOYMENT_CONFIRMATION = 'employment_confirmation';

    // Recipient titles
    const RECIPIENT_TO_WHOM_IT_MAY_CONCERN = 'To Whom It May Concern';
    const RECIPIENT_DEAR_SIR_MADAM = 'Dear Sir/Madam';
    const RECIPIENT_HUMAN_RESOURCES = 'Human Resources Manager';
    const RECIPIENT_ADMISSIONS_OFFICE = 'Admissions Office';
    const RECIPIENT_EMBASSY = 'Embassy Officials';

    /**
     * Get the student that owns the letter.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the user who generated the letter.
     */
    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Generate unique reference number.
     */
    public static function generateReferenceNumber(): string
    {
        $year = date('Y');
        $sequence = static::whereYear('letter_date', $year)->count() + 1;
        
        return "ESA-LET-{$year}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get letter type options.
     */
    public static function getLetterTypes(): array
    {
        return [
            self::TYPE_PROOF_OF_REGISTRATION => 'Proof of Registration',
            self::TYPE_STUDENT_CONFIRMATION => 'Student Confirmation',
            self::TYPE_TO_WHOM => 'To Whom It May Concern',
            self::TYPE_EMPLOYMENT_CONFIRMATION => 'Employee Confirmation',
            self::TYPE_ENROLLMENT => 'Enrollment Verification',
            self::TYPE_ACADEMIC_STANDING => 'Academic Standing',
            self::TYPE_FINANCIAL_STATUS => 'Financial Status',
            self::TYPE_COMPLETION => 'Course Completion',
            self::TYPE_GOOD_STANDING => 'Good Standing',
            self::TYPE_PROFESSIONAL_LETTER => 'Professional Student Letter',
            self::TYPE_CUSTOM => 'Custom Letter',
        ];
    }

    /**
     * Get recipient title options.
     */
    public static function getRecipientTitles(): array
    {
        return [
            self::RECIPIENT_TO_WHOM_IT_MAY_CONCERN => 'To Whom It May Concern',
            self::RECIPIENT_DEAR_SIR_MADAM => 'Dear Sir/Madam',
            self::RECIPIENT_HUMAN_RESOURCES => 'Human Resources Manager',
            self::RECIPIENT_ADMISSIONS_OFFICE => 'Admissions Office',
            self::RECIPIENT_EMBASSY => 'Embassy Officials',
        ];
    }

    /**
     * Scope to get active letters.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get letters by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('letter_type', $type);
    }

    /**
     * Get formatted letter date.
     */
    public function getFormattedLetterDateAttribute(): string
    {
        return $this->letter_date->format('F j, Y');
    }

    public function getSubjectNameAttribute(): string
    {
        return $this->recipient_name ?: $this->student?->full_name ?: 'Unknown recipient';
    }
}
