<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('submission_method');
            $table->json('allowed_file_types')->nullable();
            $table->dateTime('due_at');
            $table->unsignedInteger('marks_out_of');
            $table->timestamps();

            $table->index(['lesson_id', 'branch_id', 'term_id']);
        });

        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_assignment_id')->constrained('lesson_assignments')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_file_name');
            $table->dateTime('submitted_at');
            $table->timestamps();

            $table->unique(['lesson_assignment_id', 'student_id']);
        });

        Schema::table('marks', function (Blueprint $table) {
            $table->foreignId('assignment_id')
                ->nullable()
                ->after('lesson_id')
                ->constrained('lesson_assignments')
                ->nullOnDelete();
            $table->unique(['assignment_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::table('marks', function (Blueprint $table) {
            $table->dropUnique(['assignment_id', 'student_id']);
            $table->dropConstrainedForeignId('assignment_id');
        });

        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('lesson_assignments');
    }
};
