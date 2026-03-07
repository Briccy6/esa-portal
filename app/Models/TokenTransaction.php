<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'token_id',
        'amount',
        'type',
        'description',
        'source',
        'balance_after',
        'referenceable_id',
        'referenceable_type',
    ];

    protected $casts = [
        'amount' => 'integer',
        'balance_after' => 'integer',
    ];

    public function token()
    {
        return $this->belongsTo(Token::class);
    }

    public function referenceable()
    {
        return $this->morphTo();
    }

    public function scopeCredits($query)
    {
        return $query->where('type', 'credit');
    }

    public function scopeDebits($query)
    {
        return $query->where('type', 'debit');
    }

    public function scopeFromSource($query, $source)
    {
        return $query->where('source', $source);
    }

    public function getFormattedAmountAttribute()
    {
        $prefix = $this->type === 'credit' ? '+' : '-';
        return $prefix . $this->amount . ' tokens';
    }
}
