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
        Schema::table('branch_teacher', function (Blueprint $table) {
            // Drop the existing id column and recreate it with auto_increment
            $table->dropColumn('id');
        });
        
        Schema::table('branch_teacher', function (Blueprint $table) {
            $table->id()->first();
            $table->unique(['branch_id', 'teacher_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branch_teacher', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropUnique(['branch_id', 'teacher_id']);
        });
        
        Schema::table('branch_teacher', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned()->first();
        });
    }
};
