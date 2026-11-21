<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('support_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('support_conversations')->onDelete('cascade');
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->string('sender_type')->nullable(); // student, teacher, staff, admin
            $table->enum('sender_role', ['user', 'ai', 'agent'])->default('user');
            $table->text('message_content');
            $table->enum('message_type', ['text', 'system'])->default('text');
            $table->boolean('is_internal_note')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('conversation_id', 'support_msg_conv_idx');
            $table->index(['sender_id', 'sender_type'], 'support_msg_sender_idx');
            $table->index('sender_role', 'support_msg_role_idx');
            $table->index('created_at', 'support_msg_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_messages');
    }
};
