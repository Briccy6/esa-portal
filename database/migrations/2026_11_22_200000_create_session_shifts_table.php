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
        Schema::create('session_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timetable_session_id')->constrained('timetable_sessions')->onDelete('cascade');
            $table->enum('shift', ['day', 'evening', 'weekend']);
            $table->timestamps();
            
            $table->unique(['timetable_session_id', 'shift']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_shifts');
    }
};
