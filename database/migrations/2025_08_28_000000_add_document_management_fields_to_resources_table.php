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
            if (!Schema::hasColumn('resources', 'price')) {
                $table->decimal('price', 8, 2)->default(0.00);
            }
            if (!Schema::hasColumn('resources', 'is_public')) {
                $table->boolean('is_public')->default(false);
            }
            if (!Schema::hasColumn('resources', 'is_free')) {
                $table->boolean('is_free')->default(true);
            }
            if (!Schema::hasColumn('resources', 'watermark_path')) {
                $table->string('watermark_path')->nullable();
            }
            if (!Schema::hasColumn('resources', 'watermark_applied')) {
                $table->boolean('watermark_applied')->default(false);
            }
            if (!Schema::hasColumn('resources', 'admin_notes')) {
                $table->text('admin_notes')->nullable();
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
