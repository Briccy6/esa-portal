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
            // Add missing document blob columns
            if (!Schema::hasColumn('resources', 'document_blob')) {
                $table->binary('document_blob')->nullable();
            }
            if (!Schema::hasColumn('resources', 'document_name')) {
                $table->string('document_name')->nullable();
            }
            if (!Schema::hasColumn('resources', 'document_mime')) {
                $table->string('document_mime')->nullable();
            }
            
            // Add missing video blob columns
            if (!Schema::hasColumn('resources', 'video_blob')) {
                $table->binary('video_blob')->nullable();
            }
            if (!Schema::hasColumn('resources', 'video_name')) {
                $table->string('video_name')->nullable();
            }
            if (!Schema::hasColumn('resources', 'video_mime')) {
                $table->string('video_mime')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn([
                'document_blob', 
                'document_name', 
                'document_mime',
                'video_blob',
                'video_name', 
                'video_mime'
            ]);
        });
    }
};
