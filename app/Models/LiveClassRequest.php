<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveClassRequest extends Model
{
    protected $fillable = [
        'live_class_id',
        'user_id',
        'user_type', // 'student', 'public'
        'email', // For public users
        'name', // For public users
        'phone', // For public users
        'reason', // Request reason/message
        'status', // 'pending', 'approved', 'rejected'
        'approved_by', // Admin/Staff who approved
        'approved_at',
        'rejected_reason',
        'processed_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'processed_at' => 'datetime'
    ];

    public function liveClass()
    {
        return $this->belongsTo(LiveClass::class);
    }

    public function user()
    {
        if ($this->user_type === 'student') {
            return $this->belongsTo(Student::class, 'user_id');
        }
        return null;
    }

    public function approvedByUser()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    public function approve($approvedBy)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
            'processed_at' => now()
        ]);
    }

    public function reject($reason, $rejectedBy)
    {
        $this->update([
            'status' => 'rejected',
            'rejected_reason' => $reason,
            'approved_by' => $rejectedBy,
            'processed_at' => now()
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
