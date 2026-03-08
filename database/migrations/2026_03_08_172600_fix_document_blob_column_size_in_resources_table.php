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
            // Ensure document_blob is LONGTEXT to handle large files
            if (Schema::hasColumn('resources', 'document_blob')) {
                $table->longText('document_blob')->change();
            } else {
                $table->longText('document_blob')->nullable();
            }
            
            // Ensure video_blob is LONGTEXT to handle large files
            if (Schema::hasColumn('resources', 'video_blob')) {
                $table->longText('video_blob')->change();
            } else {
                $table->longText('video_blob')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            // Revert back to regular blob if needed
            if (Schema::hasColumn('resources', 'document_blob')) {
                $table->binary('document_blob')->change();
            }
            if (Schema::hasColumn('resources', 'video_blob')) {
                $table->binary('video_blob')->change();
            }
        });
    }
};
