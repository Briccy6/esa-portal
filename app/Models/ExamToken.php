<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'student_id',
        'tokens_used',
        'attempted_at',
        'payment_status',
        'payment_amount',
        'payment_reference',
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
        'tokens_used' => 'integer',
        'payment_amount' => 'decimal:2',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeFree($query)
    {
        return $query->where('payment_status', 'free');
    }

    public function markAsPaid($amount, $reference = null)
    {
        $this->update([
            'payment_status' => 'paid',
            'payment_amount' => $amount,
            'payment_reference' => $reference,
        ]);
    }

    public function markAsFree()
    {
        $this->update([
            'payment_status' => 'free',
            'payment_amount' => 0,
        ]);
    }
}
