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
        Schema::table('staff', function (Blueprint $table) {
            $table->string('google_id')->nullable()->unique()->after('email');
            $table->text('google_token')->nullable()->after('google_id');
            $table->text('google_refresh_token')->nullable()->after('google_token');
            $table->string('google_avatar')->nullable()->after('google_refresh_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'google_token', 'google_refresh_token', 'google_avatar']);
        });
    }
};
