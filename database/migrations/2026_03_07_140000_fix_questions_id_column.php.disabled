<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Drop the existing id column and recreate it as proper auto-increment primary key
            $table->dropColumn('id');
            $table->id()->first();
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->bigInteger('id')->unsigned()->first();
        });
    }
};
