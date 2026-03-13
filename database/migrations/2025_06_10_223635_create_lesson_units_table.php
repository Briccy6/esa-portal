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
         if (!Schema::hasTable('lesson_units')) {
      Schema::create('lesson_units', function (Blueprint $table) {
    $table->id();
    $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
    $table->string('unit_name');
    $table->integer('hours');
    $table->timestamps();
});

    }
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_units');
    }
};
