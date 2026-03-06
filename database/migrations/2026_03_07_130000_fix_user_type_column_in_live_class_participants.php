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
        Schema::table('live_class_participants', function (Blueprint $table) {
            // Check if user_type column exists and modify it to string if needed
            if (Schema::hasColumn('live_class_participants', 'user_type')) {
                $table->string('user_type')->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse as this is fixing a data type issue
    }
};
