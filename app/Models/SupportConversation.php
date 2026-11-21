<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportConversation extends Model
{
    protected $fillable = [
        'user_id',
        'user_type',
        'title',
        'status',
        'assigned_agent_id',
        'assigned_agent_type',
        'handling_mode',
        'language',
        'last_message_at',
        'resolved_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the user who owns this conversation
     */
    public function user(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the assigned agent
     */
    public function assignedAgent(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get all messages for this conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(SupportMessage::class)->orderBy('created_at');
    }

    /**
     * Get internal notes for this conversation
     */
    public function internalNotes(): HasMany
    {
        return $this->hasMany(SupportInternalNote::class)->orderBy('created_at');
    }

    /**
     * Get the latest message
     */
    public function latestMessage()
    {
        return $this->hasOne(SupportMessage::class)->latest();
    }

    /**
     * Scope a query to only include conversations with a specific status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include AI-handled conversations
     */
    public function scopeAiHandled($query)
    {
        return $query->where('handling_mode', 'ai');
    }

    /**
     * Scope a query to only include human-handled conversations
     */
    public function scopeHumanHandled($query)
    {
        return $query->where('handling_mode', 'human');
    }

    /**
     * Scope a query to only include conversations for a specific user
     */
    public function scopeForUser($query, $userId, $userType)
    {
        return $query->where('user_id', $userId)->where('user_type', $userType);
    }

    /**
     * Mark conversation as resolved
     */
    public function markAsResolved()
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);
    }

    /**
     * Assign conversation to an agent
     */
    public function assignToAgent($agentId, $agentType)
    {
        $this->update([
            'assigned_agent_id' => $agentId,
            'assigned_agent_type' => $agentType,
            'handling_mode' => 'human',
            'status' => 'in_progress',
        ]);
    }

    /**
     * Return conversation to AI handling
     */
    public function returnToAi()
    {
        $this->update([
            'assigned_agent_id' => null,
            'assigned_agent_type' => null,
            'handling_mode' => 'ai',
        ]);
    }

    /**
     * Update last message timestamp
     */
    public function updateLastMessageAt()
    {
        $this->update(['last_message_at' => now()]);
    }
}
