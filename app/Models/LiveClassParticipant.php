<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveClassParticipant extends Model
{
    protected $fillable = [
        'live_class_id',
        'user_id',
        'user_type', // 'student', 'teacher', 'admin', 'staff', 'public'
        'email', // For public users
        'name', // For public users
        'joined_at',
        'left_at',
        'is_active',
        'device_info',
        'ip_address',
        'browser_info'
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
        'is_active' => 'boolean',
        'device_info' => 'json',
        'browser_info' => 'json'
    ];

    public function liveClass()
    {
        return $this->belongsTo(LiveClass::class);
    }

    public function user()
    {
        if ($this->user_type === 'student') {
            return $this->belongsTo(Student::class, 'user_id');
        } elseif ($this->user_type === 'teacher') {
            return $this->belongsTo(Teacher::class, 'user_id');
        } elseif ($this->user_type === 'admin') {
            return $this->belongsTo(Admin::class, 'user_id');
        } elseif ($this->user_type === 'staff') {
            return $this->belongsTo(Staff::class, 'user_id');
        }
        return null;
    }

    public function getDisplayName()
    {
        if ($this->user_type === 'public') {
            return $this->name ?: $this->email;
        }
        
        $user = $this->user;
        if ($user) {
            return $user->name ?? $user->full_name ?? 'Unknown';
        }
        
        return 'Unknown User';
    }

    public function getDuration()
    {
        if ($this->left_at && $this->joined_at) {
            return $this->joined_at->diffInSeconds($this->left_at);
        }
        
        if ($this->is_active && $this->joined_at) {
            return $this->joined_at->diffInSeconds(now());
        }
        
        return 0;
    }
}
