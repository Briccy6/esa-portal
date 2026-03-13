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
        Schema::table('exams', function (Blueprint $table) {
            // Add missing columns
            $table->string('exam_type')->after('description');
            $table->integer('exam_weight')->after('class_date');
            $table->integer('total_marks')->after('exam_weight');
            $table->foreignId('branch_id')->nullable()->after('teacher_id')->constrained()->onDelete('cascade');
            
            // Drop old single foreign keys if they exist (since we're using many-to-many)
            if (Schema::hasColumn('exams', 'course_id')) {
                $table->dropForeign(['course_id']);
                $table->dropColumn('course_id');
            }
            if (Schema::hasColumn('exams', 'lesson_id')) {
                $table->dropForeign(['lesson_id']);
                $table->dropColumn('lesson_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['exam_type', 'exam_weight', 'total_marks', 'branch_id']);
            
            // Restore old columns if needed
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->nullable()->constrained()->onDelete('cascade');
        });
    }
};
