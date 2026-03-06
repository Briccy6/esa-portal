<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LiveClassPayment extends Model
{
    protected $fillable = [
        'live_class_id',
        'user_type', // 'public'
        'email',
        'name',
        'phone',
        'amount',
        'currency',
        'payment_method', // 'card', 'mobile_money', 'bank_transfer'
        'transaction_id',
        'status', // 'pending', 'completed', 'failed', 'refunded'
        'payment_gateway', // 'stripe', 'paypal', 'flutterwave', etc.
        'gateway_response',
        'paid_at',
        'refunded_at',
        'refund_reason'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
        'gateway_response' => 'json'
    ];

    public function liveClass()
    {
        return $this->belongsTo(LiveClass::class);
    }

    public static function generateTransactionId()
    {
        return 'LC_' . Str::random(12) . '_' . time();
    }

    public function markAsCompleted($gatewayResponse = null)
    {
        $this->update([
            'status' => 'completed',
            'paid_at' => now(),
            'gateway_response' => $gatewayResponse
        ]);
    }

    public function markAsFailed($gatewayResponse = null)
    {
        $this->update([
            'status' => 'failed',
            'gateway_response' => $gatewayResponse
        ]);
    }

    public function refund($reason = '')
    {
        $this->update([
            'status' => 'refunded',
            'refunded_at' => now(),
            'refund_reason' => $reason
        ]);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function getFormattedAmount()
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }
}
