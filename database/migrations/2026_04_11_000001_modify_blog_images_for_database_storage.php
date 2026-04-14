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
            // Modify image_path to store as longBlob (binary image data)
            $table->longBlob('image_path')->nullable()->change();
            
            // Use image_alt to store mime_type
            $table->string('image_alt')->default('image/jpeg')->change();
            
            // Use image_caption to store filename
            // image_caption already exists and is TEXT, so it can handle it
        });
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        Schema::table('blog_images', function (Blueprint $table) {
            $table->string('image_path')->nullable(false)->change();
            $table->string('image_alt')->nullable()->change();
        });
    }
};
