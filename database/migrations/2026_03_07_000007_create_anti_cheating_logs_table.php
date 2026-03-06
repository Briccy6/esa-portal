<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anti_cheating_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->enum('violation_type', [
                'tab_switch',
                'fullscreen_exit', 
                'right_click',
                'copy_attempt',
                'paste_attempt',
                'multiple_windows',
                'suspicious_activity'
            ]);
            $table->text('description')->nullable();
            $table->timestamp('occurred_at');
            $table->json('metadata')->nullable(); // Additional context
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anti_cheating_logs');
    }
};
