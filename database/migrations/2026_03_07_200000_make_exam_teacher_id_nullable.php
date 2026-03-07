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
        // Use raw SQL to make teacher_id nullable
        \DB::statement('ALTER TABLE exams MODIFY teacher_id BIGINT UNSIGNED NULL');
        
        // Drop and recreate foreign key with set null
        try {
            \DB::statement('ALTER TABLE exams DROP FOREIGN KEY exams_teacher_id_foreign');
        } catch (\Exception $e) {
            // Foreign key might not exist or have different name
        }
        
        \DB::statement('ALTER TABLE exams ADD CONSTRAINT exams_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
        });
    }
};
