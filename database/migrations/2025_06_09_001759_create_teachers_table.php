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
          if (!Schema::hasTable('teachers')) {
 Schema::create('teachers', function (Blueprint $table) {
    $table->id();
    
    // Personal Information
    $table->string('teacher_name');
    $table->string('teacher_id_number')->unique();
    $table->string('id_number')->unique(); // National ID
    $table->string('telephone');
    $table->string('email')->unique();
    $table->string('photo')->nullable(); // Store photo file path
    
    // Professional Information
    $table->enum('department', [
        'All', 
        'REB Combinations', 
        'RTB Trades', 
        'Training', 
        'Internship'
    ]);
    $table->enum('employment_type', ['Permanent', 'Short Term', 'Casual']);
    $table->string('level_of_education');
    
    // Financial Information
    $table->decimal('salary', 10, 2); // 10 digits total, 2 decimal places
    
    $table->timestamps();
});
          }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    Schema::dropIfExists('teachers');
    \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
}

};
