<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAnnouncementRead extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'announcement_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Get the student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the announcement
     */
    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }
}
