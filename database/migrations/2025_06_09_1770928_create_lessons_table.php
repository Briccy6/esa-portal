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
          if (!Schema::hasTable('lessons')) {
Schema::create('lessons', function (Blueprint $table) {
    $table->id();
    $table->foreignId('teacher_id')->nullable()->constrained()->onDelete('set null');
    $table->string('title');
    $table->integer('max_test_marks');
    $table->integer('max_exam_marks');
    $table->timestamps();
});
          }



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    Schema::dropIfExists('lessons');
    \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
}

};
