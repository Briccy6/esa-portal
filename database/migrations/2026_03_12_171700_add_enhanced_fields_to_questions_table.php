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
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('questions', 'question_bank_id')) {
                $table->foreignId('question_bank_id')->nullable()->constrained()->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('questions', 'question_type')) {
                $table->string('question_type')->default('multiple_choice');
            }
            
            if (!Schema::hasColumn('questions', 'correct_answers')) {
                $table->json('correct_answers')->nullable();
            }
            
            if (!Schema::hasColumn('questions', 'weight')) {
                $table->decimal('weight', 3, 2)->default(1.00);
            }
            
            if (!Schema::hasColumn('questions', 'difficulty_level')) {
                $table->integer('difficulty_level')->default(1);
            }
            
            if (!Schema::hasColumn('questions', 'explanation')) {
                $table->text('explanation')->nullable();
            }
            
            if (!Schema::hasColumn('questions', 'keywords')) {
                $table->text('keywords')->nullable();
            }
            
            // Enhanced fields for mathematics and biology
            if (!Schema::hasColumn('questions', 'subject_area')) {
                $table->string('subject_area')->default('general'); // mathematics, biology, general
            }
            
            if (!Schema::hasColumn('questions', 'biology_topic')) {
                $table->string('biology_topic')->nullable(); // cell_biology, genetics, etc.
            }
            
            if (!Schema::hasColumn('questions', 'diagram_labels')) {
                $table->text('diagram_labels')->nullable(); // Labels for diagrams
            }
            
            if (!Schema::hasColumn('questions', 'question_image')) {
                $table->string('question_image')->nullable(); // Image filename
            }
            
            // Update existing columns if needed
            if (Schema::hasColumn('questions', 'marks')) {
                $table->integer('marks')->default(1)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Drop the enhanced columns
            $columnsToDrop = [
                'subject_area',
                'biology_topic', 
                'diagram_labels',
                'question_image'
            ];
            
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('questions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
