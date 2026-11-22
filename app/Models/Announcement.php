<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'audience',
        'content',
        'attachment',
        'sender',
    ];

    /**
     * Get the reads for this announcement
     */
    public function reads()
    {
        return $this->hasMany(StudentAnnouncementRead::class);
    }
}
