<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('attempt_id')->constrained('exam_attempts')->onDelete('cascade');
            $table->timestamp('session_start')->nullable();
            $table->timestamp('last_activity')->nullable();
            $table->integer('time_remaining_seconds');
            $table->boolean('is_fullscreen')->default(false);
            $table->integer('tab_switches')->default(0);
            $table->integer('fullscreen_violations')->default(0);
            $table->json('question_order')->nullable(); // Randomized question order
            $table->json('answer_orders')->nullable(); // Randomized answer choices
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_sessions');
    }
};
