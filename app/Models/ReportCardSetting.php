<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ReportCardSetting extends Model
{
    protected $fillable = [
        'academic_year',
        'term',
        'report_card_available_at',
        'description',
        'is_active'
    ];

    protected $casts = [
        'report_card_available_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    /**
     * Get the active report card setting for the current academic year and term
     */
    public static function getActive()
    {
        return self::where('academic_year', config('app.academic_year', '2025-2026'))
                    ->where('term', config('app.current_term', 'Term 1'))
                    ->where('is_active', true)
                    ->first();
    }

    /**
     * Check if report cards are currently available
     */
    public function isAvailable()
    {
        return $this->is_active && $this->report_card_available_at->isPast();
    }

    /**
     * Get time remaining until report cards are available
     */
    public function getTimeRemaining()
    {
        if ($this->isAvailable()) {
            return null; // Already available
        }

        return $this->report_card_available_at->diffForHumans(now(), true);
    }

    /**
     * Get countdown data for JavaScript
     */
    public function getCountdownData()
    {
        if ($this->isAvailable()) {
            return null;
        }

        $now = Carbon::now();
        $target = $this->report_card_available_at;
        
        if ($target->isPast()) {
            return null;
        }

        return [
            'months' => $target->diffInMonths($now),
            'weeks' => $target->diffInWeeks($now) % 4,
            'days' => $target->diffInDays($now) % 7,
            'hours' => $target->diffInHours($now) % 24,
            'minutes' => $target->diffInMinutes($now) % 60,
            'seconds' => $target->diffInSeconds($now) % 60,
            'total_seconds' => $target->diffInSeconds($now),
            'target_date' => $target->toISOString()
        ];
    }
}
