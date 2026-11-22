<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentOptionalFeeResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_term_branch_fee_id',
        'fee_type',
        'response',
        'responded_at',
        'notes',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    /**
     * Get the student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the course term branch fee
     */
    public function courseTermBranchFee()
    {
        return $this->belongsTo(CourseTermBranchFee::class);
    }

    /**
     * Scope for accepted responses
     */
    public function scopeAccepted($query)
    {
        return $query->where('response', 'accepted');
    }

    /**
     * Scope for declined responses
     */
    public function scopeDeclined($query)
    {
        return $query->where('response', 'declined');
    }

    /**
     * Scope for pending responses
     */
    public function scopePending($query)
    {
        return $query->where('response', 'pending');
    }

    /**
     * Scope for practical fees
     */
    public function scopePractical($query)
    {
        return $query->where('fee_type', 'practical');
    }

    /**
     * Scope for trip fees
     */
    public function scopeTrip($query)
    {
        return $query->where('fee_type', 'trip');
    }
}
