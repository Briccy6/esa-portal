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
        Schema::create('student_optional_fee_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_term_branch_fee_id')->constrained()->onDelete('cascade');
            $table->enum('fee_type', ['practical', 'trip']);
            $table->enum('response', ['accepted', 'declined', 'pending'])->default('pending');
            $table->timestamp('responded_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'course_term_branch_fee_id', 'fee_type'], 'student_fee_unique');
            $table->index(['student_id', 'response'], 'student_response_idx');
            $table->index(['course_term_branch_fee_id', 'fee_type', 'response'], 'fee_response_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_optional_fee_responses');
    }
};
