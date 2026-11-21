<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // important
use Illuminate\Notifications\Notifiable;

class Staff extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'role', 'photo', 'qr_code', 'staff_card', 'email', 'phone', 'password', 'address',
        'google_id', 'google_token', 'google_refresh_token', 'google_avatar'
    ];

    protected $hidden = [
        'password',
        'remember_token', // add if you use remember me
    ];
    
    // Single branch relationship (for backward compatibility)
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    
    // Many-to-many relationship with branches
    public function branches()
    {
        return $this->belongsToMany(\App\Models\Branch::class, 'staff_branch');
    }

    public function isStudent()
    {
        return false;
    }
    
    public function isStaff()
    {
        return true;
    }

    /**
     * Check if staff has linked Google account
     */
    public function hasGoogleAccount()
    {
        return !empty($this->google_id);
    }

    /**
     * Get Google avatar URL
     */
    public function getGoogleAvatarUrlAttribute()
    {
        return $this->google_avatar;
    }

    /**
     * Link Google account to staff
     */
    public function linkGoogleAccount($googleId, $token, $refreshToken = null, $avatar = null)
    {
        $this->update([
            'google_id' => $googleId,
            'google_token' => $token,
            'google_refresh_token' => $refreshToken,
            'google_avatar' => $avatar,
        ]);
    }

    /**
     * Unlink Google account from staff
     */
    public function unlinkGoogleAccount()
    {
        $this->update([
            'google_id' => null,
            'google_token' => null,
            'google_refresh_token' => null,
            'google_avatar' => null,
        ]);
    }

    /**
     * Support chat relationships
     */
    public function supportConversations()
    {
        return $this->morphMany(\App\Models\SupportConversation::class, 'user');
    }

    public function supportMessages()
    {
        return $this->morphMany(\App\Models\SupportMessage::class, 'sender');
    }

    public function supportInternalNotes()
    {
        return $this->morphMany(\App\Models\SupportInternalNote::class, 'author');
    }
}
