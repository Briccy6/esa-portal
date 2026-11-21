<?php

use App\Models\SupportConversation;
use App\Models\SupportInternalNote;
use App\Models\SupportMessage;
use Tests\TestCase;

uses(TestCase::class);

test('support relationships use the conversation_id database column', function () {
    $conversation = new SupportConversation();
    $message = new SupportMessage();
    $note = new SupportInternalNote();

    expect($conversation->messages()->getForeignKeyName())->toBe('conversation_id')
        ->and($conversation->internalNotes()->getForeignKeyName())->toBe('conversation_id')
        ->and($conversation->latestMessage()->getForeignKeyName())->toBe('conversation_id')
        ->and($message->conversation()->getForeignKeyName())->toBe('conversation_id')
        ->and($note->conversation()->getForeignKeyName())->toBe('conversation_id');
});
