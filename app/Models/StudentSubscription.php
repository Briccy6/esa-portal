<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subscription_id',
        'paid_amount',
        'payment_reference',
        'payment_status',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'paid_amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('starts_at', '<=', now())
                    ->where('ends_at', '>=', now());
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function isActive()
    {
        return $this->is_active && 
               $this->payment_status === 'paid' && 
               $this->starts_at->isPast() && 
               $this->ends_at->isFuture();
    }

    public function markAsPaid($reference = null)
    {
        $this->update([
            'payment_status' => 'paid',
            'payment_reference' => $reference,
            'is_active' => true,
        ]);
    }

    public function getDaysRemainingAttribute()
    {
        if (!$this->ends_at) {
            return 0;
        }
        
        return max(0, now()->diffInDays($this->ends_at));
    }

    public function getFormattedPaidAmountAttribute()
    {
        return '$' . number_format($this->paid_amount, 2);
    }
}
