<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalItem extends Model
{
    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'debit_amount',
        'credit_amount',
        'description',
        'reference_type',
        'reference_id'
    ];

    protected $casts = [
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
    ];

    /**
     * Get the journal entry for this item.
     */
    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    /**
     * Get the account for this item.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the amount based on whether it's debit or credit.
     */
    public function getAmountAttribute(): float
    {
        return max($this->debit_amount, $this->credit_amount);
    }

    /**
     * Check if this is a debit item.
     */
    public function isDebit(): bool
    {
        return $this->debit_amount > 0;
    }

    /**
     * Check if this is a credit item.
     */
    public function isCredit(): bool
    {
        return $this->credit_amount > 0;
    }

    /**
     * Scope to get debit items.
     */
    public function scopeDebits($query)
    {
        return $query->where('debit_amount', '>', 0);
    }

    /**
     * Scope to get credit items.
     */
    public function scopeCredits($query)
    {
        return $query->where('credit_amount', '>', 0);
    }

    /**
     * Scope to get items for a specific reference.
     */
    public function scopeForReference($query, $referenceType, $referenceId)
    {
        return $query->where('reference_type', $referenceType)
                    ->where('reference_id', $referenceId);
    }

    /**
     * Create a debit item.
     */
    public static function createDebit($journalEntryId, $accountId, $amount, $description = null, $referenceType = null, $referenceId = null)
    {
        return static::create([
            'journal_entry_id' => $journalEntryId,
            'account_id' => $accountId,
            'debit_amount' => $amount,
            'credit_amount' => 0,
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId
        ]);
    }

    /**
     * Create a credit item.
     */
    public static function createCredit($journalEntryId, $accountId, $amount, $description = null, $referenceType = null, $referenceId = null)
    {
        return static::create([
            'journal_entry_id' => $journalEntryId,
            'account_id' => $accountId,
            'debit_amount' => 0,
            'credit_amount' => $amount,
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId
        ]);
    }
}
