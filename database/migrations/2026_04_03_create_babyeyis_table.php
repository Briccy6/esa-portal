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
        Schema::create('babyeyis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('term_id')->constrained('terms')->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('set null');
            $table->string('course_type')->nullable(); // 'single', 'multiple', 'by_type'
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('edited_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title')->nullable();
            $table->longText('content')->nullable(); // Rich text HTML content
            $table->string('trip_place')->nullable();
            $table->decimal('trip_fees', 10, 2)->nullable();
            $table->boolean('trip_known')->default(false);
            $table->json('fee_details')->nullable(); // Store fee breakdown
            $table->enum('status', ['draft', 'preview', 'published'])->default('draft');
            $table->string('reference_number')->unique();
            $table->timestamps();
            
            $table->index(['academic_year_id', 'term_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('babyeyis');
    }
};
