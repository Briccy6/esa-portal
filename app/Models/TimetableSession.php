<?php
// app/Models/TimetableSession.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TimetableSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'timetable_id',
        'lesson_id',
        'day_of_week',
        'shift',
        'start_time',
        'end_time',
        'classroom',
        'notes',
        'reserve_text',
        'mark_as_test',
        'session_type',
        'activity_type',
        'minimum_paid_percentage',
    ];

    protected $casts = [
        'mark_as_test' => 'boolean',
        'minimum_paid_percentage' => 'decimal:2',
    ];

    public function timetable()
    {
        return $this->belongsTo(Timetable::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'timetable_session_course')
            ->withTimestamps();
    }

    protected function parseTime($value)
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        if (is_string($value)) {
            $formats = ['H:i', 'H:i:s'];
            foreach ($formats as $format) {
                try {
                    $parsed = Carbon::createFromFormat($format, $value);
                    if ($parsed && $parsed->format($format) === substr($value, 0, strlen($format))) {
                        return $parsed;
                    }
                } catch (\Exception $e) {
                    // continue
                }
            }
            try {
                return Carbon::parse($value);
            } catch (\Exception $e) {
                return Carbon::createFromFormat('H:i', '00:00');
            }
        }

        return Carbon::parse($value);
    }

    public function getDurationHours()
    {
        $start = $this->parseTime($this->start_time);
        $end = $this->parseTime($this->end_time);
        return max(0, $end->floatDiffInHours($start));
    }

    public function conflictsWith(TimetableSession $other)
    {
        if ($this->day_of_week !== $other->day_of_week) {
            return false;
        }

        $thisStart = $this->parseTime($this->start_time);
        $thisEnd = $this->parseTime($this->end_time);
        $otherStart = $this->parseTime($other->start_time);
        $otherEnd = $this->parseTime($other->end_time);

        return $thisStart->lt($otherEnd) && $thisEnd->gt($otherStart);
    }
}
