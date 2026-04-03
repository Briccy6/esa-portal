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
        // Drop the existing foreign key constraint on created_by
        Schema::table('babyeyis', function (Blueprint $table) {
            // Drop the constraint - the exact name depends on the database
            try {
                $table->dropForeign(['created_by']);
            } catch (\Exception $e) {
                // Constraint might not exist or have a different name
                \Log::warning('Could not drop created_by foreign key: '.$e->getMessage());
            }
            
            // Also drop the constraint on edited_by if it exists
            try {
                $table->dropForeign(['edited_by']);
            } catch (\Exception $e) {
                \Log::warning('Could not drop edited_by foreign key: '.$e->getMessage());
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a destructive rollback - we won't recreate the foreign keys
        // since the issue persists with the current structure
    }
};
