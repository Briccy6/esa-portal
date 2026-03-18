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
        Schema::table('questions', function (Blueprint $table) {
            // Make exam_id nullable to allow questions in question banks without being tied to an exam
            $table->dropForeign(['exam_id']);
            $table->foreignId('exam_id')->nullable()->change();
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['exam_id']);
            $table->foreignId('exam_id')->nullable(false)->change();
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
        });
    }
};
