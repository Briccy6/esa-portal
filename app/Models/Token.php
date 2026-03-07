<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'balance',
        'total_earned',
        'total_spent',
        'last_earned_at',
        'last_spent_at',
    ];

    protected $casts = [
        'last_earned_at' => 'datetime',
        'last_spent_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function transactions()
    {
        return $this->hasMany(TokenTransaction::class);
    }

    public function addTokens($amount, $description = null, $source = 'system')
    {
        $this->increment('balance', $amount);
        $this->increment('total_earned', $amount);
        $this->update(['last_earned_at' => now()]);

        return $this->transactions()->create([
            'amount' => $amount,
            'type' => 'credit',
            'description' => $description,
            'source' => $source,
            'balance_after' => $this->balance,
        ]);
    }

    public function spendTokens($amount, $description = null, $source = 'exam')
    {
        if ($this->balance < $amount) {
            return false;
        }

        $this->decrement('balance', $amount);
        $this->increment('total_spent', $amount);
        $this->update(['last_spent_at' => now()]);

        return $this->transactions()->create([
            'amount' => $amount,
            'type' => 'debit',
            'description' => $description,
            'source' => $source,
            'balance_after' => $this->balance,
        ]);
    }

    public function canAfford($amount)
    {
        return $this->balance >= $amount;
    }

    public function getFormattedBalanceAttribute()
    {
        return number_format($this->balance) . ' tokens';
    }
}
