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
        // Check if the id column exists and doesn't have auto_increment
        if (Schema::hasTable('marks') && Schema::hasColumn('marks', 'id')) {
            // Check if auto_increment is already set
            $columnInfo = \DB::select("SHOW COLUMNS FROM marks WHERE Field = 'id'");
            if ($columnInfo && !str_contains($columnInfo[0]->Extra, 'auto_increment')) {
                // Drop and recreate the id column with proper auto_increment
                \DB::statement('ALTER TABLE marks MODIFY id INT AUTO_INCREMENT PRIMARY KEY');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not easily reversible, but we can try
        if (Schema::hasTable('marks')) {
            \DB::statement('ALTER TABLE marks MODIFY id INT');
        }
    }
};
