<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'amount',
        'description',
        'is_public',
        'visibility',
        'academic_year',
        'term'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_public' => 'boolean',
    ];

    /**
     * Get the course that this fee belongs to
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Scope to get public fees
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true)
                     ->orWhere('visibility', 'public');
    }
}
