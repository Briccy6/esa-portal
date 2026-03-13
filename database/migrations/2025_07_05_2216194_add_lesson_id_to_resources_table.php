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
        if (!Schema::hasColumn('resources', 'lesson_id')) {
            Schema::table('resources', function (Blueprint $table) {
                $table->unsignedBigInteger('lesson_id')->after('branch_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
public function down(): void
{
    \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    Schema::dropIfExists('resources');
    \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
}

};
