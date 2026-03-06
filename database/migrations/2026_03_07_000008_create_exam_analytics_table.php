<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            
            // Question performance metrics
            $table->integer('total_attempts')->default(0);
            $table->integer('correct_attempts')->default(0);
            $table->decimal('success_rate', 5, 2)->default(0);
            $table->decimal('average_time_seconds', 8, 2)->default(0);
            $table->decimal('difficulty_score', 5, 2)->default(0);
            
            // Answer distribution for MCQ
            $table->integer('option_a_count')->default(0);
            $table->integer('option_b_count')->default(0);
            $table->integer('option_c_count')->default(0);
            $table->integer('option_d_count')->default(0);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_analytics');
    }
};
