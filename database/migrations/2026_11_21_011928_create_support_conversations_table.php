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
        Schema::create('support_conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('user_type'); // student, teacher, staff, admin
            $table->string('title')->nullable();
            $table->enum('status', ['open', 'pending', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->unsignedBigInteger('assigned_agent_id')->nullable();
            $table->string('assigned_agent_type')->nullable(); // admin, staff
            $table->enum('handling_mode', ['ai', 'human'])->default('ai');
            $table->string('language', 10)->default('en');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'user_type'], 'support_user_idx');
            $table->index(['assigned_agent_id', 'assigned_agent_type'], 'support_agent_idx');
            $table->index('status', 'support_status_idx');
            $table->index('handling_mode', 'support_mode_idx');
            $table->index('last_message_at', 'support_last_msg_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_conversations');
    }
};
