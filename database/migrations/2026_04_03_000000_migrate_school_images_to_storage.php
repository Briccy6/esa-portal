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
        Schema::table('school_informations', function (Blueprint $table) {
            // Add new columns to store file paths instead of BLOB data
            $table->string('logo_file_path')->nullable()->comment('Path to logo file in storage');
            $table->string('principal_signature_file_path')->nullable()->comment('Path to principal signature file in storage');
            $table->string('stamp_signature_file_path')->nullable()->comment('Path to stamp signature file in storage');
            
            // Add tracking for migration status
            $table->boolean('images_migrated_to_storage')->default(false)->comment('Tracks if BLOB images have been migrated to storage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_informations', function (Blueprint $table) {
            $table->dropColumn([
                'logo_file_path',
                'principal_signature_file_path',
                'stamp_signature_file_path',
                'images_migrated_to_storage'
            ]);
        });
    }
};
