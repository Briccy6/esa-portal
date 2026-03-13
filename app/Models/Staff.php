<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // important
use Illuminate\Notifications\Notifiable;

class Staff extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'role', 'photo', 'qr_code', 'staff_card', 'email', 'phone', 'password'
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
}
