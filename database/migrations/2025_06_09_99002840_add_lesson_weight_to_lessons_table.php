<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('lessons', function (Blueprint $table) {
        $table->integer('lesson_weight')->nullable();
    });
}

public function down(): void
{
    \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    Schema::dropIfExists('lessons');
    \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
}

};
