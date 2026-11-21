<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $table = 'admins'; // custom table name

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'google_id',
        'google_token',
        'google_refresh_token',
        'google_avatar',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Check if admin has linked Google account
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
     * Link Google account to admin
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
     * Unlink Google account from admin
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
