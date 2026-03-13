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
    Schema::table('lesson_units', function (Blueprint $table) {
        $table->foreignId('term_id')->nullable()->constrained()->onDelete('set null');
        $table->string('course_level')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lesson_units', function (Blueprint $table) {
            //
        });
    }
};
