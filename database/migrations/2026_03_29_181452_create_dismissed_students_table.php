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
        Schema::create('dismissed_students', function (Blueprint $table) {
            $table->id();
            
            // Student information (copied from students table)
            $table->string('full_name');
            $table->string('sex')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('course_id');
            $table->string('reg_number')->unique();
            $table->string('password');
            $table->boolean('payed_registration_fees')->default(0);
            $table->unsignedBigInteger('academic_year_id');
            $table->string('id_number')->nullable();
            $table->string('identification_type')->nullable();
            $table->string('id_photo')->nullable();
            $table->string('passport_photo')->nullable();
            $table->string('senior6_document')->nullable();
            $table->string('senior3_document')->nullable();
            $table->string('other_supporting_document')->nullable();
            $table->boolean('nesa_registered')->default(false);
            $table->string('nesa_sdms_code')->nullable();
            $table->string('nesa_reg_number')->nullable();
            $table->string('nesa_document')->nullable();
            $table->date('nesa_registration_date')->nullable();
            $table->text('nesa_remarks')->nullable();
            $table->decimal('discount', 5, 2)->nullable();
            $table->string('education_level')->nullable();
            
            // Dismissal specific fields
            $table->text('dismissal_reason');
            $table->text('dismissal_notes')->nullable();
            $table->unsignedBigInteger('dismissed_by');
            $table->date('dismissal_date');
            
            // Original student ID for reference
            $table->unsignedBigInteger('original_student_id')->unique();
            
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('course_id')->references('id')->on('courses');
            $table->foreign('academic_year_id')->references('id')->on('academic_years');
            $table->foreign('dismissed_by')->references('id')->on('admins');
            
            // Indexes
            $table->index('dismissal_date');
            $table->index('dismissed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dismissed_students');
    }
};
