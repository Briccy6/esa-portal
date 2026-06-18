<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Convert blog images to store directly in database
     */
    public function up(): void
    {
        if (! Schema::hasTable('blog_images')) {
            return;
        }

        if (Schema::hasColumn('blog_images', 'alt_text') && ! Schema::hasColumn('blog_images', 'image_alt')) {
            DB::statement('ALTER TABLE blog_images CHANGE alt_text image_alt VARCHAR(255) NULL');
        }

        if (Schema::hasColumn('blog_images', 'caption') && ! Schema::hasColumn('blog_images', 'image_caption')) {
            DB::statement('ALTER TABLE blog_images CHANGE caption image_caption TEXT NULL');
        }

        DB::statement('ALTER TABLE blog_images MODIFY COLUMN image_path LONGBLOB NULL');
        DB::statement("ALTER TABLE blog_images MODIFY COLUMN image_alt VARCHAR(255) DEFAULT 'image/jpeg'");
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        if (! Schema::hasTable('blog_images')) {
            return;
        }

        DB::statement('ALTER TABLE blog_images MODIFY COLUMN image_path VARCHAR(255) NOT NULL');

        if (Schema::hasColumn('blog_images', 'image_alt')) {
            DB::statement('ALTER TABLE blog_images MODIFY COLUMN image_alt VARCHAR(255) NULL');
        }
    }
};
