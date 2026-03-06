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
        Schema::table('live_class_requests', function (Blueprint $table) {
            $table->string('user_type')->change(); // Change from integer to string
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('live_class_requests', function (Blueprint $table) {
            $table->bigInteger('user_type')->unsigned()->change(); // Revert to integer
        });
    }
};
