<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'timetable_session_id',
        'shift',
    ];

    /**
     * Get the timetable session
     */
    public function timetableSession()
    {
        return $this->belongsTo(TimetableSession::class);
    }
}
