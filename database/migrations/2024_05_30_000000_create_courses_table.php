<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_code')->unique();       // e.g. "TOU001"
            $table->string('course_name');                 // e.g. "Tourism"
            $table->string('abbreviation');                // e.g. "TOU"
            $table->string('course_type');                 // e.g. "RTB Trade" or "REB Combination"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
}
