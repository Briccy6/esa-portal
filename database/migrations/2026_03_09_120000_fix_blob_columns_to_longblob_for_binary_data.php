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
            // Change document_blob from longtext to binary to properly handle binary data
            $table->binary('document_blob')->change()->nullable();
            // Change video_blob from blob to binary to handle larger video files  
            $table->binary('video_blob')->change()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->longText('document_blob')->change();
            $table->binary('video_blob')->change();
        });
    }
};
