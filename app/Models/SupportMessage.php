<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SupportMessage extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'sender_type',
        'sender_role',
        'message_content',
        'message_type',
        'is_internal_note',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_internal_note' => 'boolean',
    ];

    /**
     * Get the conversation this message belongs to
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(SupportConversation::class);
    }

    /**
     * Get the sender of this message
     */
    public function sender(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include user messages
     */
    public function scopeUserMessages($query)
    {
        return $query->where('sender_role', 'user');
    }

    /**
     * Scope a query to only include AI messages
     */
    public function scopeAiMessages($query)
    {
        return $query->where('sender_role', 'ai');
    }

    /**
     * Scope a query to only include agent messages
     */
    public function scopeAgentMessages($query)
    {
        return $query->where('sender_role', 'agent');
    }

    /**
     * Scope a query to only include internal notes
     */
    public function scopeInternalNotes($query)
    {
        return $query->where('is_internal_note', true);
    }

    /**
     * Scope a query to exclude internal notes
     */
    public function scopePublicMessages($query)
    {
        return $query->where('is_internal_note', false);
    }
}
