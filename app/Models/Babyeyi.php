<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Babyeyi extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'term_id',
        'course_id',
        'course_type',
        'created_by',
        'edited_by',
        'title',
        'content',
        'trip_place',
        'trip_fees',
        'trip_known',
        'fee_details',
        'status',
        'reference_number',
    ];

    protected $casts = [
        'fee_details' => 'array',
        'trip_fees' => 'decimal:2',
        'trip_known' => 'boolean',
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_PREVIEW = 'preview';
    const STATUS_PUBLISHED = 'published';

    /**
     * Get the academic year that owns the babyeyi.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the term that owns the babyeyi.
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    /**
     * Get the course that owns the babyeyi (if single course).
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the user who created the babyeyi (can be Staff or Admin).
     * Note: Due to the multi-guard authentication system, this relationship
     * cannot use a direct foreign key. Use getCreatorInfo() instead to get the actual user.
     * 
     * @deprecated Use getCreatorInfo() instead
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who edited the babyeyi (can be Staff or Admin).
     * Note: Due to the multi-guard authentication system, this relationship
     * cannot use a direct foreign key. Use getEditorInfo() instead to get the actual user.
     * 
     * @deprecated Use getEditorInfo() instead
     */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    /**
     * Get the creator user info - tries to find in both Staff and Admin tables.
     */
    public function getCreatorInfo()
    {
        if (!$this->created_by) {
            return null;
        }

        // Try to find in Staff table first
        $staff = Staff::find($this->created_by);
        if ($staff) {
            return [
                'type' => 'staff',
                'user' => $staff,
                'name' => $staff->name,
            ];
        }

        // Try to find in Admin table
        $admin = Admin::find($this->created_by);
        if ($admin) {
            return [
                'type' => 'admin',
                'user' => $admin,
                'name' => $admin->first_name . ' ' . $admin->last_name,
            ];
        }

        return null;
    }

    /**
     * Get the editor user info - tries to find in both Staff and Admin tables.
     */
    public function getEditorInfo()
    {
        if (!$this->edited_by) {
            return null;
        }

        // Try to find in Staff table first
        $staff = Staff::find($this->edited_by);
        if ($staff) {
            return [
                'type' => 'staff',
                'user' => $staff,
                'name' => $staff->name,
            ];
        }

        // Try to find in Admin table
        $admin = Admin::find($this->edited_by);
        if ($admin) {
            return [
                'type' => 'admin',
                'user' => $admin,
                'name' => $admin->first_name . ' ' . $admin->last_name,
            ];
        }

        return null;
    }

    /**
     * Generate unique reference number.
     */
    public static function generateReferenceNumber(): string
    {
        $year = date('Y');
        $sequence = static::whereYear('created_at', $year)->count() + 1;
        
        return "BYI-{$year}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get status options.
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PREVIEW => 'Preview',
            self::STATUS_PUBLISHED => 'Published',
        ];
    }

    /**
     * Scope to get published documents.
     */
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * Scope to get draft documents.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Get formatted reference number with date.
     */
    public function getFormattedReferenceAttribute(): string
    {
        return $this->reference_number . ' (' . $this->created_at->format('F j, Y') . ')';
    }
}
