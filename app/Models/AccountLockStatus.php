<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountLockStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_type',
        'user_id',
        'lock_reason',
        'lock_details',
        'is_locked',
        'locked_at',
        'unlock_at',
        'unlocked_at',
        'unlocked_by',
        'unlock_notes',
    ];

    protected $casts = [
        'lock_details' => 'array',
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
        'unlock_at' => 'datetime',
        'unlocked_at' => 'datetime',
    ];

    /**
     * Get the staff who unlocked the account
     */
    public function unlockedBy()
    {
        return $this->belongsTo(Staff::class, 'unlocked_by');
    }

    /**
     * Get the user that owns the lock status
     */
    public function getUser()
    {
        if ($this->user_type === 'student') {
            return Student::find($this->user_id);
        } elseif ($this->user_type === 'staff') {
            return Staff::find($this->user_id);
        } elseif ($this->user_type === 'admin') {
            return \App\Models\Admin::find($this->user_id);
        }
        return null;
    }

    /**
     * Scope for currently locked accounts
     */
    public function scopeLocked($query)
    {
        return $query->where('is_locked', true)
            ->where(function($q) {
                $q->whereNull('unlock_at')
                    ->orWhere('unlock_at', '>', now());
            });
    }

    /**
     * Check if a user is locked
     */
    public static function isLocked($userType, $userId)
    {
        return self::where('user_type', $userType)
            ->where('user_id', $userId)
            ->locked()
            ->exists();
    }

    /**
     * Lock a user account
     */
    public static function lockUser($userType, $userId, $reason, $details = null, $unlockAt = null)
    {
        return self::updateOrCreate(
            [
                'user_type' => $userType,
                'user_id' => $userId,
            ],
            [
                'lock_reason' => $reason,
                'lock_details' => $details,
                'is_locked' => true,
                'locked_at' => now(),
                'unlock_at' => $unlockAt,
                'unlocked_at' => null,
                'unlocked_by' => null,
                'unlock_notes' => null,
            ]
        );
    }

    /**
     * Unlock a user account
     */
    public static function unlockUser($userType, $userId, $unlockedBy = null, $notes = null)
    {
        $lock = self::where('user_type', $userType)
            ->where('user_id', $userId)
            ->locked()
            ->first();

        if ($lock) {
            $lock->update([
                'is_locked' => false,
                'unlocked_at' => now(),
                'unlocked_by' => $unlockedBy,
                'unlock_notes' => $notes,
            ]);
        }

        return $lock;
    }

    /**
     * Get lock details for a user
     */
    public static function getLockDetails($userType, $userId)
    {
        return self::where('user_type', $userType)
            ->where('user_id', $userId)
            ->locked()
            ->first();
    }
}
