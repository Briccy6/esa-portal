<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Convert blog images to store directly in database
     */
    public function up(): void
    {
        Schema::table('blog_images', function (Blueprint $table) {
            // Add new columns for database storage
            $table->longBlob('image_data')->nullable()->after('blog_id');
            $table->string('mime_type')->default('image/jpeg')->after('image_data');
            $table->string('filename')->nullable()->after('mime_type');
            
            // Keep image_path for backward compatibility (can be dropped later)
            // but make it nullable for new records
            $table->string('image_path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        Schema::table('blog_images', function (Blueprint $table) {
            $table->dropColumn(['image_data', 'mime_type', 'filename']);
            $table->string('image_path')->nullable(false)->change();
        });
    }
};
