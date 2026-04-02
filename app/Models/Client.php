<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Client extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'google_id',
        'is_active',
        'verification_status',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(ClientSubscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(ClientSubscription::class)
            ->where('is_active', true)
            ->where('ends_at', '>', now())
            ->latest();
    }

    public function transactions()
    {
        return $this->hasMany(ClientTransaction::class);
    }

    public function hasActiveSubscription()
    {
        return $this->activeSubscription()->exists();
    }

    public function purchaseSubscription($subscriptionId)
    {
        $subscription = Subscription::findOrFail($subscriptionId);
        
        return $this->subscriptions()->create([
            'subscription_id' => $subscription->id,
            'payment_status' => 'pending',
            'starts_at' => null,
            'ends_at' => null,
            'is_active' => false,
            'amount' => $subscription->price,
            'currency' => 'RWF',
        ]);
    }

    public function tokens()
    {
        return $this->hasMany(ClientToken::class);
    }

    public function getCurrentTokens()
    {
        $latestToken = $this->tokens()->latest()->first();
        return $latestToken ? $latestToken->balance : 0;
    }

    public function addTokens($amount, $description = '', $type = 'purchase')
    {
        $currentBalance = $this->getCurrentTokens();
        
        return $this->tokens()->create([
            'balance' => $currentBalance + $amount,
            'amount_added' => $amount,
            'description' => $description,
            'type' => $type,
        ]);
    }

    public function deductTokens($amount, $description = '', $type = 'usage')
    {
        $currentBalance = $this->getCurrentTokens();
        
        if ($currentBalance < $amount) {
            throw new \Exception('Insufficient tokens');
        }
        
        return $this->tokens()->create([
            'balance' => $currentBalance - $amount,
            'amount_deducted' => $amount,
            'description' => $description,
            'type' => $type,
        ]);
    }

    public function canAccessExam($exam)
    {
        // Free exams are accessible to everyone
        if ($exam->public_price == 0) {
            return true;
        }

        // Check if client has active subscription
        if ($this->hasActiveSubscription()) {
            return true;
        }

        // Check if client has enough tokens
        if ($this->getCurrentTokens() >= $exam->token_cost) {
            return true;
        }

        return false;
    }

    public function examSessions()
    {
        return $this->hasMany(ExamSession::class);
    }

    public function examAttempts()
    {
        return $this->examSessions()->whereNotNull('session_start');
    }

    public function examsTaken()
    {
        return $this->examAttempts()->count();
    }

    public function averageScore()
    {
        $completedSessions = $this->examSessions()
            ->whereNotNull('session_start')
            ->whereNotNull('session_end')
            ->whereNotNull('score')
            ->get();

        if ($completedSessions->isEmpty()) {
            return 0;
        }

        return $completedSessions->avg('score');
    }
}
