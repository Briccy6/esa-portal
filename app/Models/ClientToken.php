<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'balance',
        'amount_added',
        'amount_deducted',
        'description',
        'type',
    ];

    protected $casts = [
        'balance' => 'integer',
        'amount_added' => 'integer',
        'amount_deducted' => 'integer',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function getFormattedBalanceAttribute()
    {
        return number_format($this->balance) . ' tokens';
    }

    public function getTransactionTypeBadgeAttribute()
    {
        return match($this->type) {
            'purchase' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Purchase</span>',
            'subscription' => '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Subscription</span>',
            'usage' => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Usage</span>',
            'bonus' => '<span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">Bonus</span>',
            default => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Other</span>',
        };
    }
}
