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
         if (!Schema::hasTable('teacher_dailies')) {
            
        Schema::create('teacher_dailies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained('lesson_units')->onDelete('cascade');
            $table->string('activity');
            $table->float('hours_studied')->default(0);
            $table->integer('total_students');
            $table->text('absences')->nullable();
            $table->string('quiz_link')->nullable();
            $table->string('marking_guide')->nullable();
            $table->integer('total_marks')->nullable();
            $table->text('challenges')->nullable();
            $table->text('comment')->nullable();
            $table->date('date');
            $table->timestamps();
        });
    }
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_dailies');
    }
};
