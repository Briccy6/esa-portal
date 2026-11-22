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
        if (!Schema::hasTable('student_announcement_reads')) {
            Schema::create('student_announcement_reads', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained()->onDelete('cascade');
                $table->foreignId('announcement_id')->constrained()->onDelete('cascade');
                $table->timestamp('read_at')->useCurrent();
                $table->unique(['student_id', 'announcement_id']);
                $table->index(['student_id', 'read_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_announcement_reads');
    }
};
