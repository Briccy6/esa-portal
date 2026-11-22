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
        'start_time',
        'end_time',
    ];

    /**
     * Get the timetable session
     */
    public function timetableSession()
    {
        return $this->belongsTo(TimetableSession::class);
    }
}
