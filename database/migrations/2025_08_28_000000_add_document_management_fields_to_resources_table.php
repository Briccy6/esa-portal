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
            $table->decimal('price', 8, 2)->default(0.00)->after('document_mime');
            $table->boolean('is_public')->default(false)->after('price');
            $table->boolean('is_free')->default(true)->after('is_public');
            $table->string('watermark_path')->nullable()->after('is_free');
            $table->boolean('watermark_applied')->default(false)->after('watermark_path');
            $table->text('admin_notes')->nullable()->after('watermark_applied');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn([
                'price', 
                'is_public', 
                'is_free', 
                'watermark_path', 
                'watermark_applied', 
                'admin_notes'
            ]);
        });
    }
};
