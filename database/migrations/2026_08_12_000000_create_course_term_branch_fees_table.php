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
        Schema::create('course_term_branch_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('term_id');
            $table->unsignedBigInteger('branch_id');
            $table->decimal('registration_fee', 12, 2)->default(0);
            $table->decimal('tuition_fee', 12, 2)->default(0);
            $table->decimal('practical_fee', 12, 2)->default(0);
            $table->decimal('trip_fee', 12, 2)->default(0);
            $table->decimal('student_card_fee', 12, 2)->default(2000);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('term_id')->references('id')->on('terms')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unique(['course_id','term_id','branch_id'],'ctbf_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_term_branch_fees');
    }
};
