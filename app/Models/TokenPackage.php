<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'tokens',
        'bonus_tokens',
        'price',
        'is_active',
        'is_popular',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
    ];

    public function getFormattedTokensAttribute()
    {
        $totalTokens = $this->tokens + $this->bonus_tokens;
        return number_format($totalTokens) . ' tokens';
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2) . ' RWF';
    }

    public function transactions()
    {
        return $this->hasMany(TokenTransaction::class);
    }
}
