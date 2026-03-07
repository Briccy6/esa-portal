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
        if (!Schema::hasTable('timetable_sessions')) {
            Schema::create('timetable_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('timetable_id')->constrained()->onDelete('cascade');
                $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
                $table->tinyInteger('day_of_week'); // 0 = Sunday, 1 = Monday, etc.
                $table->time('start_time');
                $table->time('end_time');
                $table->string('classroom')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->index(['timetable_id', 'day_of_week', 'start_time']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetable_sessions');
    }
};
