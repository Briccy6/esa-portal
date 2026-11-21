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
        Schema::create('support_internal_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('support_conversations')->onDelete('cascade');
            $table->unsignedBigInteger('author_id');
            $table->string('author_type'); // admin, staff
            $table->text('note_content');
            $table->timestamps();

            $table->index('conversation_id', 'support_note_conv_idx');
            $table->index(['author_id', 'author_type'], 'support_note_author_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_internal_notes');
    }
};
