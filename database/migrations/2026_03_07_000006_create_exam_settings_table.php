<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            
            // Timing settings
            $table->integer('duration_minutes')->default(60);
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            
            // Question settings
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('randomize_answers')->default(false);
            $table->integer('questions_per_page')->default(1);
            
            // Results settings
            $table->boolean('show_results_immediately')->default(true);
            $table->boolean('show_correct_answers')->default(false);
            $table->boolean('show_explanations')->default(false);
            $table->timestamp('results_release_date')->nullable();
            
            // Retake settings
            $table->boolean('allow_retakes')->default(false);
            $table->integer('max_attempts')->default(1);
            $table->decimal('passing_score', 5, 2)->nullable();
            
            // Security settings
            $table->boolean('require_fullscreen')->default(true);
            $table->boolean('disable_right_click')->default(true);
            $table->boolean('disable_copy_paste')->default(true);
            $table->integer('max_tab_switches')->default(3);
            $table->boolean('auto_submit_on_violation')->default(true);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_settings');
    }
};
