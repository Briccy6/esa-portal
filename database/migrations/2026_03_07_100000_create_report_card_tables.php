<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_informations', function (Blueprint $table) {
            $table->id();
            $table->string('school_name');
            $table->text('address');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('motto')->nullable();
            $table->text('vision')->nullable();
            $table->text('mission')->nullable();
            $table->string('principal_name')->nullable();
            $table->string('principal_signature')->nullable();
            $table->string('stamp_signature')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('report_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained();
            $table->foreignId('term_id')->constrained();
            $table->foreignId('course_id')->constrained();
            $table->decimal('total_marks', 8, 2)->default(0);
            $table->decimal('average_marks', 8, 2)->default(0);
            $table->integer('class_position')->nullable();
            $table->integer('total_students')->default(0);
            $table->string('grade')->nullable();
            $table->text('remarks')->nullable();
            $table->string('status')->default('pending'); // pending, approved, printed
            $table->foreignId('approved_by')->nullable()->constrained('esa.admins');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'academic_year_id', 'term_id', 'course_id'], 'unique_report_card');
        });

        Schema::create('report_card_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_card_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained();
            $table->string('subject_name');
            $table->decimal('exam_mark', 8, 2)->default(0);
            $table->decimal('test_total', 8, 2)->default(0);
            $table->decimal('test_average', 8, 2)->default(0);
            $table->decimal('total_mark', 8, 2)->default(0);
            $table->string('grade')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->unique(['report_card_id', 'lesson_id'], 'unique_report_subject');
        });

        Schema::create('report_card_test_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_card_subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_id')->constrained();
            $table->string('test_name');
            $table->decimal('original_mark', 8, 2);
            $table->decimal('max_mark', 8, 2);
            $table->decimal('normalized_mark', 8, 2); // Converted to 100%
            $table->decimal('weight', 5, 2)->default(1.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_card_test_marks');
        Schema::dropIfExists('report_card_subjects');
        Schema::dropIfExists('report_cards');
        Schema::dropIfExists('school_informations');
    }
};
