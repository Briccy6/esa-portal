<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $fillable = [
        'account_code',
        'account_name',
        'account_type',
        'normal_balance',
        'description',
        'is_active',
        'is_cash_account',
        'cash_flow_category',
        'parent_account_id',
        'level'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_cash_account' => 'boolean',
    ];

    /**
     * Get the journal items for this account.
     */
    public function journalItems(): HasMany
    {
        return $this->hasMany(JournalItem::class);
    }

    /**
     * Get the current balance for this account.
     */
    public function getCurrentBalance($asOfDate = null): float
    {
        $query = $this->journalItems()
            ->whereHas('journalEntry', function ($q) use ($asOfDate) {
                $q->where('status', 'Posted');
                if ($asOfDate) {
                    $q->whereDate('entry_date', '<=', $asOfDate);
                }
            });

        $totalDebits = $query->sum('debit_amount');
        $totalCredits = $query->sum('credit_amount');

        if ($this->normal_balance === 'Debit') {
            return $totalDebits - $totalCredits;
        } else {
            return $totalCredits - $totalDebits;
        }
    }

    /**
     * Get the debit balance for this account.
     */
    public function getDebitBalance($asOfDate = null): float
    {
        $balance = $this->getCurrentBalance($asOfDate);
        return $this->normal_balance === 'Debit' && $balance > 0 ? $balance : 0;
    }

    /**
     * Get the credit balance for this account.
     */
    public function getCreditBalance($asOfDate = null): float
    {
        $balance = $this->getCurrentBalance($asOfDate);
        return $this->normal_balance === 'Credit' && $balance > 0 ? $balance : 0;
    }

    /**
     * Scope to get only active accounts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get cash accounts.
     */
    public function scopeCashAccounts($query)
    {
        return $query->where('is_cash_account', true);
    }

    /**
     * Scope to get accounts by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('account_type', $type);
    }

    /**
     * Get formatted account name with code.
     */
    public function getFormattedNameAttribute(): string
    {
        return "{$this->account_code} - {$this->account_name}";
    }
}
