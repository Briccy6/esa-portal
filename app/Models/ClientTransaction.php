<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'subscription_id',
        'token_package_id',
        'tx_ref',
        'type',
        'amount',
        'currency',
        'status',
        'payment_method',
        'payment_reference',
        'gateway_response',
        'meta',
        'paid_at',
        'tokens',
        'bonus_tokens',
        'total_tokens',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'meta' => 'json',
        'gateway_response' => 'json',
        'paid_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'successful');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'successful' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Successful</span>',
            'pending' => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>',
            'failed' => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Failed</span>',
            default => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Unknown</span>',
        };
    }

    public function getTransactionTypeBadgeAttribute()
    {
        return match($this->type) {
            'subscription' => '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Subscription</span>',
            'token_purchase' => '<span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">Token Purchase</span>',
            default => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">' . ucfirst($this->type) . '</span>',
        };
    }
}
