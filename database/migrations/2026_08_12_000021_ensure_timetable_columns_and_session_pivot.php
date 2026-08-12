<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('timetables')) {
            Schema::table('timetables', function (Blueprint $table) {
                if (! Schema::hasColumn('timetables', 'branch_id')) {
                    $table->foreignId('branch_id')->nullable()->constrained()->onDelete('cascade')->after('title');
                }
                if (! Schema::hasColumn('timetables', 'academic_year_id')) {
                    $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->onDelete('cascade')->after('branch_id');
                }
                if (! Schema::hasColumn('timetables', 'term_id')) {
                    $table->foreignId('term_id')->nullable()->constrained()->onDelete('cascade')->after('academic_year_id');
                }
                if (! Schema::hasColumn('timetables', 'status')) {
                    $table->enum('status', ['draft','published'])->default('draft')->after('term_id');
                }
            });
        }

        if (Schema::hasTable('timetable_sessions')) {
            Schema::table('timetable_sessions', function (Blueprint $table) {
                if (! Schema::hasColumn('timetable_sessions', 'mark_as_test')) {
                    $table->boolean('mark_as_test')->default(false)->after('notes');
                }
            });
        }

        if (! Schema::hasTable('timetable_session_course')) {
            Schema::create('timetable_session_course', function (Blueprint $table) {
                $table->id();
                $table->foreignId('timetable_session_id')->constrained('timetable_sessions')->onDelete('cascade');
                $table->foreignId('course_id')->constrained()->onDelete('cascade');
                $table->timestamps();
                $table->unique(['timetable_session_id','course_id'], 'timetable_session_course_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('timetable_session_course')) {
            Schema::dropIfExists('timetable_session_course');
        }

        if (Schema::hasTable('timetable_sessions')) {
            Schema::table('timetable_sessions', function (Blueprint $table) {
                if (Schema::hasColumn('timetable_sessions', 'mark_as_test')) {
                    $table->dropColumn('mark_as_test');
                }
            });
        }

        if (Schema::hasTable('timetables')) {
            Schema::table('timetables', function (Blueprint $table) {
                if (Schema::hasColumn('timetables', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('timetables', 'term_id')) {
                    $table->dropForeign(['term_id']);
                    $table->dropColumn('term_id');
                }
                if (Schema::hasColumn('timetables', 'academic_year_id')) {
                    $table->dropForeign(['academic_year_id']);
                    $table->dropColumn('academic_year_id');
                }
                if (Schema::hasColumn('timetables', 'branch_id')) {
                    $table->dropForeign(['branch_id']);
                    $table->dropColumn('branch_id');
                }
            });
        }
    }
};
