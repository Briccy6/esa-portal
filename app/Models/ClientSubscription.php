<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'subscription_id',
        'payment_status',
        'payment_reference',
        'paid_amount',
        'starts_at',
        'ends_at',
        'is_active',
        'amount',
        'currency',
        'payment_gateway',
        'gateway_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'gateway_response' => 'json',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeExpired($query)
    {
        return $query->where('ends_at', '<', now());
    }

    public function isActive()
    {
        return $this->is_active && $this->ends_at && $this->ends_at > now();
    }

    public function markAsPaid($paymentData)
    {
        $this->update([
            'payment_status' => 'paid',
            'payment_reference' => $paymentData['tx_ref'] ?? null,
            'paid_amount' => $paymentData['amount'] ?? $this->amount,
            'starts_at' => now(),
            'ends_at' => now()->addDays($this->subscription->duration_days),
            'is_active' => true,
            'gateway_response' => $paymentData,
        ]);

        // Add tokens to client
        $this->client->addTokens($this->subscription->tokens_included, 'Subscription purchase');
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->payment_status) {
            'pending' => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>',
            'paid' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>',
            'failed' => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Failed</span>',
            default => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Unknown</span>',
        };
    }
}
