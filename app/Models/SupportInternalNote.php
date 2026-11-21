<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SupportInternalNote extends Model
{
    protected $fillable = [
        'conversation_id',
        'author_id',
        'author_type',
        'note_content',
    ];

    /**
     * Get the conversation this note belongs to
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(SupportConversation::class);
    }

    /**
     * Get the author of this note
     */
    public function author(): MorphTo
    {
        return $this->morphTo();
    }
}
