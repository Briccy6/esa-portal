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
        Schema::table('resources', function (Blueprint $table) {
            // Fix video_blob column to be nullable
            if (Schema::hasColumn('resources', 'video_blob')) {
                $table->binary('video_blob')->nullable()->change();
            }
            // Also fix related video columns if needed
            if (Schema::hasColumn('resources', 'video_name')) {
                $table->string('video_name')->nullable()->change();
            }
            if (Schema::hasColumn('resources', 'video_mime')) {
                $table->string('video_mime')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            // This migration is safe to rollback - just leave as is
            // The columns should remain nullable
        });
    }
};
