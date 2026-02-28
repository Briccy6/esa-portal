<?php
// app/Models/TimetableSession.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'timetable_id',
        'lesson_id',
        'day_of_week',
        'start_time',
        'end_time',
        'classroom',
        'notes'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i'
    ];

    public function timetable()
    {
        return $this->belongsTo(Timetable::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    // Get duration in hours
    public function getDurationHours()
    {
        $start = \Carbon\Carbon::createFromFormat('H:i', $this->start_time);
        $end = \Carbon\Carbon::createFromFormat('H:i', $this->end_time);
        return $end->diffInHours($start);
    }

    // Check if session conflicts with another
    public function conflictsWith(TimetableSession $other)
    {
        if ($this->day_of_week !== $other->day_of_week) {
            return false;
        }

        $thisStart = \Carbon\Carbon::createFromFormat('H:i', $this->start_time);
        $thisEnd = \Carbon\Carbon::createFromFormat('H:i', $this->end_time);
        $otherStart = \Carbon\Carbon::createFromFormat('H:i', $other->start_time);
        $otherEnd = \Carbon\Carbon::createFromFormat('H:i', $other->end_time);

        return $thisStart->lt($otherEnd) && $thisEnd->gt($otherStart);
    }
}