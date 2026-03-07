<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Make exam_id nullable to allow questions in question banks
            $table->unsignedBigInteger('exam_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedBigInteger('exam_id')->nullable(false)->change();
        });
    }
};
