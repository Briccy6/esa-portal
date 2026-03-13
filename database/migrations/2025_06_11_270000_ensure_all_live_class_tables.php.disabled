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
        // Ensure all required tables exist
        $tables = [
            'live_class_branches' => ['live_class_id', 'branch_id'],
            'live_class_courses' => ['live_class_id', 'course_id'],
            'live_class_lessons' => ['live_class_id', 'lesson_id'],
            'live_class_participants' => ['live_class_id', 'user_id', 'user_type'],
            'live_class_requests' => ['live_class_id', 'user_id', 'user_type'],
            'live_class_payments' => ['live_class_id', 'user_type', 'email']
        ];

        foreach ($tables as $tableName => $columns) {
            if (!Schema::hasTable($tableName)) {
                Schema::create($tableName, function (Blueprint $table) use ($columns, $tableName) {
                    $table->id();
                    foreach ($columns as $column) {
                        $table->unsignedBigInteger($column);
                    }
                    $table->timestamps();
                    
                    // Add unique constraint for pivot tables
                    if (in_array($tableName, ['live_class_branches', 'live_class_courses', 'live_class_lessons'])) {
                        $table->unique([$columns[0], $columns[1]]);
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop anything in down method for safety
    }
};
