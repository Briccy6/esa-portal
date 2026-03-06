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
            // Only add columns that don't exist
            if (!Schema::hasColumn('exams', 'exam_type')) {
                $table->string('exam_type')->after('description');
            }
            if (!Schema::hasColumn('exams', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('teacher_id')->constrained()->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            if (Schema::hasColumn('exams', 'exam_type')) {
                $table->dropColumn('exam_type');
            }
            if (Schema::hasColumn('exams', 'branch_id')) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            }
        });
    }
};
