<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Check if an index exists on a table.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $result = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        return count($result) > 0;
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes to support_conversations table
        Schema::table('support_conversations', function (Blueprint $table) {
            if (!$this->indexExists('support_conversations', 'support_conv_status_last_msg_idx')) {
                $table->index(['status', 'last_message_at'], 'support_conv_status_last_msg_idx');
            }
            
            if (!$this->indexExists('support_conversations', 'support_conv_created_idx')) {
                $table->index('created_at', 'support_conv_created_idx');
            }
            
            if (!$this->indexExists('support_conversations', 'support_conv_user_idx')) {
                $table->index('user_id', 'support_conv_user_idx');
            }
            
            if (!$this->indexExists('support_conversations', 'support_conv_agent_idx')) {
                $table->index('assigned_agent_id', 'support_conv_agent_idx');
            }
            
            if (!$this->indexExists('support_conversations', 'support_conv_user_status_idx')) {
                $table->index(['user_id', 'status'], 'support_conv_user_status_idx');
            }
            
            if (!$this->indexExists('support_conversations', 'support_conv_agent_status_idx')) {
                $table->index(['assigned_agent_id', 'status'], 'support_conv_agent_status_idx');
            }
        });

        // Add indexes to support_messages table
        Schema::table('support_messages', function (Blueprint $table) {
            if (!$this->indexExists('support_messages', 'support_msg_conv_created_idx')) {
                $table->index(['conversation_id', 'created_at'], 'support_msg_conv_created_idx');
            }
            
            if (!$this->indexExists('support_messages', 'support_msg_internal_idx')) {
                $table->index('is_internal_note', 'support_msg_internal_idx');
            }
            
            if (!$this->indexExists('support_messages', 'support_msg_role_created_idx')) {
                $table->index(['sender_role', 'created_at'], 'support_msg_role_created_idx');
            }
            
            if (!$this->indexExists('support_messages', 'support_msg_type_idx')) {
                $table->index('message_type', 'support_msg_type_idx');
            }
        });

        // Add indexes to support_internal_notes table
        Schema::table('support_internal_notes', function (Blueprint $table) {
            if (!$this->indexExists('support_internal_notes', 'support_note_conv_created_idx')) {
                $table->index(['conversation_id', 'created_at'], 'support_note_conv_created_idx');
            }
            
            if (!$this->indexExists('support_internal_notes', 'support_note_author_single_idx')) {
                $table->index('author_id', 'support_note_author_single_idx');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes from support_conversations table
        Schema::table('support_conversations', function (Blueprint $table) {
            if ($this->indexExists('support_conversations', 'support_conv_status_last_msg_idx')) {
                $table->dropIndex('support_conv_status_last_msg_idx');
            }
            if ($this->indexExists('support_conversations', 'support_conv_created_idx')) {
                $table->dropIndex('support_conv_created_idx');
            }
            if ($this->indexExists('support_conversations', 'support_conv_user_idx')) {
                $table->dropIndex('support_conv_user_idx');
            }
            if ($this->indexExists('support_conversations', 'support_conv_agent_idx')) {
                $table->dropIndex('support_conv_agent_idx');
            }
            if ($this->indexExists('support_conversations', 'support_conv_user_status_idx')) {
                $table->dropIndex('support_conv_user_status_idx');
            }
            if ($this->indexExists('support_conversations', 'support_conv_agent_status_idx')) {
                $table->dropIndex('support_conv_agent_status_idx');
            }
        });

        // Remove indexes from support_messages table
        Schema::table('support_messages', function (Blueprint $table) {
            if ($this->indexExists('support_messages', 'support_msg_conv_created_idx')) {
                $table->dropIndex('support_msg_conv_created_idx');
            }
            if ($this->indexExists('support_messages', 'support_msg_internal_idx')) {
                $table->dropIndex('support_msg_internal_idx');
            }
            if ($this->indexExists('support_messages', 'support_msg_role_created_idx')) {
                $table->dropIndex('support_msg_role_created_idx');
            }
            if ($this->indexExists('support_messages', 'support_msg_type_idx')) {
                $table->dropIndex('support_msg_type_idx');
            }
        });

        // Remove indexes from support_internal_notes table
        Schema::table('support_internal_notes', function (Blueprint $table) {
            if ($this->indexExists('support_internal_notes', 'support_note_conv_created_idx')) {
                $table->dropIndex('support_note_conv_created_idx');
            }
            if ($this->indexExists('support_internal_notes', 'support_note_author_single_idx')) {
                $table->dropIndex('support_note_author_single_idx');
            }
        });
    }
};
