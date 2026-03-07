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
        if (!Schema::hasTable('grades')) {
            Schema::create('grades', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // e.g. "A", "B", "C", "D", "F"
                $table->integer('min_score');
                $table->integer('max_score');
                $table->text('description')->nullable();
                $table->string('color')->nullable(); // For UI display
                $table->decimal('gpa_points', 3, 2)->nullable(); // For GPA calculation
                $table->boolean('is_passing')->default(true);
                $table->timestamps();

                $table->unique(['name']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
