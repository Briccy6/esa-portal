<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Add new columns for enhanced question types
            $table->string('question_type')->default('multiple_choice')->after('exam_id');
            $table->text('explanation')->nullable()->after('correct_answer');
            $table->foreignId('question_bank_id')->nullable()->constrained()->onDelete('set null')->after('exam_id');
            $table->json('correct_answers')->nullable()->after('correct_answer'); // For multiple select
            $table->text('keywords')->nullable()->after('explanation'); // For short answer matching
            $table->decimal('weight', 5, 2)->default(1.00)->after('marks'); // Weighted questions
            $table->integer('difficulty_level')->default(1)->after('weight'); // 1-5 difficulty
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn([
                'question_type',
                'explanation', 
                'question_bank_id',
                'correct_answers',
                'keywords',
                'weight',
                'difficulty_level'
            ]);
        });
    }
};
