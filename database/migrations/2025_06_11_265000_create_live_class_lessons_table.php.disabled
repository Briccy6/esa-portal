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
        Schema::create('live_class_lessons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('live_class_id');
            $table->unsignedBigInteger('lesson_id');
            $table->timestamps();
            $table->unique(['live_class_id', 'lesson_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_class_lessons');
    }
};
