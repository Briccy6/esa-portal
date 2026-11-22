<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherDailyPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_daily_id',
        'teacher_id',
        'calculated_amount',
        'amount_paid',
        'status',
        'work_date',
        'notes',
        'approved_at',
        'approved_by',
        'paid_at',
        'paid_by',
    ];

    protected $casts = [
        'calculated_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'work_date' => 'date',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the teacher daily record
     */
    public function teacherDaily()
    {
        return $this->belongsTo(TeacherDaily::class);
    }

    /**
     * Get the teacher
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get the staff who approved
     */
    public function approver()
    {
        return $this->belongsTo(Staff::class, 'approved_by');
    }

    /**
     * Get the staff who paid
     */
    public function payer()
    {
        return $this->belongsTo(Staff::class, 'paid_by');
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved payments
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for paid payments
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope for partial payments
     */
    public function scopePartial($query)
    {
        return $query->where('status', 'partial');
    }

    /**
     * Get remaining amount to pay
     */
    public function getRemainingAmountAttribute()
    {
        return $this->calculated_amount - $this->amount_paid;
    }
}
