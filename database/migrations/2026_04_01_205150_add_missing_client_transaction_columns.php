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
        Schema::table('client_transactions', function (Blueprint $table) {
            // Only add columns that don't already exist
            if (!Schema::hasColumn('client_transactions', 'tokens')) {
                $table->integer('tokens')->nullable()->default(0);
            }
            if (!Schema::hasColumn('client_transactions', 'bonus_tokens')) {
                $table->integer('bonus_tokens')->nullable()->default(0);
            }
            if (!Schema::hasColumn('client_transactions', 'total_tokens')) {
                $table->integer('total_tokens')->nullable()->default(0);
            }
            if (!Schema::hasColumn('client_transactions', 'description')) {
                $table->text('description')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'tokens',
                'bonus_tokens',
                'total_tokens',
                'description'
            ]);
        });
    }
};
