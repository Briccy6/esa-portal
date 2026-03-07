<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'tokens_included',
        'price',
        'tier',
        'duration_days',
        'is_active',
        'is_recommended',
        'features',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
        'is_recommended' => 'boolean',
    ];

    public function studentSubscriptions()
    {
        return $this->hasMany(StudentSubscription::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByTier($query, $tier)
    {
        return $query->where('tier', $tier);
    }

    public function scopeRecommended($query)
    {
        return $query->where('is_recommended', true);
    }

    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    public function getFormattedTokensAttribute()
    {
        return number_format($this->tokens_included) . ' tokens';
    }

    public function getMonthlyEquivalentAttribute()
    {
        $monthlyPrice = $this->duration_days > 0 ? ($this->price / $this->duration_days) * 30 : $this->price;
        return '$' . number_format($monthlyPrice, 2) . '/month';
    }
}
