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
        // Create live_class_courses table
        Schema::create('live_class_courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('live_class_id');
            $table->unsignedBigInteger('course_id');
            $table->timestamps();

            $table->foreign('live_class_id')->references('id')->on('live_classes')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->unique(['live_class_id', 'course_id']);
        });

        // Create live_class_lessons table
        Schema::create('live_class_lessons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('live_class_id');
            $table->unsignedBigInteger('lesson_id');
            $table->timestamps();

            $table->foreign('live_class_id')->references('id')->on('live_classes')->onDelete('cascade');
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            $table->unique(['live_class_id', 'lesson_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_class_lessons');
        Schema::dropIfExists('live_class_courses');
    }
};
