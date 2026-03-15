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
        Schema::table('report_card_test_marks', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['exam_id']);
            // Make the column nullable
            $table->unsignedBigInteger('exam_id')->nullable()->change();
            // Re-add the foreign key constraint with nullable
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_card_test_marks', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['exam_id']);
            // Make the column not nullable again
            $table->unsignedBigInteger('exam_id')->nullable(false)->change();
            // Re-add the original foreign key constraint
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
        });
    }
};
