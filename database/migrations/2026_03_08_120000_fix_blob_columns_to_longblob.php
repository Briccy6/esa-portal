<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to change blob columns to longblob
        DB::statement('ALTER TABLE resources MODIFY COLUMN document_blob LONGBLOB NULL');
        DB::statement('ALTER TABLE resources MODIFY COLUMN video_blob LONGBLOB NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to blob
        DB::statement('ALTER TABLE resources MODIFY COLUMN document_blob BLOB NULL');
        DB::statement('ALTER TABLE resources MODIFY COLUMN video_blob BLOB NULL');
    }
};
