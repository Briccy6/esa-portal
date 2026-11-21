<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('timetable_sessions')) {
            return;
        }

        Schema::table('timetable_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('timetable_sessions', 'session_type')) {
                $table->string('session_type')->default('class');
            }
            if (! Schema::hasColumn('timetable_sessions', 'minimum_paid_percentage')) {
                $table->decimal('minimum_paid_percentage', 5, 2)->default(0);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('timetable_sessions')) {
            return;
        }

        Schema::table('timetable_sessions', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('timetable_sessions', 'session_type')) {
                $columns[] = 'session_type';
            }
            if (Schema::hasColumn('timetable_sessions', 'minimum_paid_percentage')) {
                $columns[] = 'minimum_paid_percentage';
            }
            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
