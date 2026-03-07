<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalEntry extends Model
{
    protected $fillable = [
        'entry_date',
        'reference_number',
        'description',
        'total_amount',
        'status',
        'notes',
        'created_by',
        'posted_by',
        'posted_at'
    ];

    protected $casts = [
        'entry_date' => 'date',
        'total_amount' => 'decimal:2',
        'posted_at' => 'datetime',
    ];

    /**
     * Get the journal items for this entry.
     */
    public function journalItems(): HasMany
    {
        return $this->hasMany(JournalItem::class);
    }

    /**
     * Get the user who created this entry.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who posted this entry.
     */
    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    /**
     * Check if the entry is balanced.
     */
    public function isBalanced(): bool
    {
        $totalDebits = $this->journalItems->sum('debit_amount');
        $totalCredits = $this->journalItems->sum('credit_amount');
        
        return abs($totalDebits - $totalCredits) < 0.01;
    }

    /**
     * Get total debits for this entry.
     */
    public function getTotalDebitsAttribute(): float
    {
        return $this->journalItems->sum('debit_amount');
    }

    /**
     * Get total credits for this entry.
     */
    public function getTotalCreditsAttribute(): float
    {
        return $this->journalItems->sum('credit_amount');
    }

    /**
     * Post the journal entry.
     */
    public function post($postedBy = null): bool
    {
        if ($this->status !== 'Draft') {
            return false;
        }

        if (!$this->isBalanced()) {
            throw new \Exception('Cannot post unbalanced journal entry');
        }

        $this->status = 'Posted';
        $this->posted_by = $postedBy ?? auth()->id();
        $this->posted_at = now();
        
        return $this->save();
    }

    /**
     * Void the journal entry.
     */
    public function void($voidedBy = null): bool
    {
        if ($this->status === 'Void') {
            return false;
        }

        $this->status = 'Void';
        return $this->save();
    }

    /**
     * Scope to get posted entries.
     */
    public function scopePosted($query)
    {
        return $query->where('status', 'Posted');
    }

    /**
     * Scope to get draft entries.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'Draft');
    }

    /**
     * Generate unique reference number.
     */
    public static function generateReferenceNumber(): string
    {
        $prefix = 'JE';
        $date = now()->format('Ymd');
        $sequence = static::whereDate('created_at', today())->count() + 1;
        
        return "{$prefix}{$date}" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
