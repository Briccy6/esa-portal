<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('resources')) {
            $primaryKeys = DB::select("SHOW INDEX FROM resources WHERE Key_name = 'PRIMARY'");
            if (count($primaryKeys) === 0) {
                DB::statement('ALTER TABLE resources MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
            }
        }

        if (! Schema::hasTable('academic_year_course')) {
            Schema::create('academic_year_course', function (Blueprint $table) {
                $table->id();
                $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
                $table->foreignId('course_id')->constrained()->onDelete('cascade');
                $table->timestamps();
                $table->unique(['academic_year_id', 'course_id']);
            });
        }

        if (! Schema::hasTable('academic_year_teacher')) {
            Schema::create('academic_year_teacher', function (Blueprint $table) {
                $table->id();
                $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
                $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
                $table->timestamps();
                $table->unique(['academic_year_id', 'teacher_id']);
            });
        }

        if (! Schema::hasTable('academic_year_branch')) {
            Schema::create('academic_year_branch', function (Blueprint $table) {
                $table->id();
                $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
                $table->foreignId('branch_id')->constrained()->onDelete('cascade');
                $table->timestamps();
                $table->unique(['academic_year_id', 'branch_id']);
            });
        }

        if (! Schema::hasTable('academic_year_resource')) {
            Schema::create('academic_year_resource', function (Blueprint $table) {
                $table->id();
                $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
                $table->foreignId('resource_id')->constrained()->onDelete('cascade');
                $table->timestamps();
                $table->unique(['academic_year_id', 'resource_id']);
            });
        } else {
            $this->ensureAcademicYearResourceForeignKey();
        }

        $this->backfillAcademicYearPivots();
    }

    protected function backfillAcademicYearPivots(): void
    {
        $courses = DB::table('courses')
            ->whereNotNull('academic_year_id')
            ->get(['id', 'academic_year_id']);

        foreach ($courses as $course) {
            DB::table('academic_year_course')->insertOrIgnore([
                'academic_year_id' => $course->academic_year_id,
                'course_id' => $course->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (Schema::hasTable('teacher_course')) {
            $teacherCoursePairs = DB::table('teacher_course')
                ->join('courses', 'teacher_course.course_id', '=', 'courses.id')
                ->whereNotNull('courses.academic_year_id')
                ->select('courses.academic_year_id', 'teacher_course.teacher_id')
                ->distinct()
                ->get();

            foreach ($teacherCoursePairs as $pair) {
                DB::table('academic_year_teacher')->insertOrIgnore([
                    'academic_year_id' => $pair->academic_year_id,
                    'teacher_id' => $pair->teacher_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        if (Schema::hasTable('branch_course')) {
            $branchCoursePairs = DB::table('branch_course')
                ->join('courses', 'branch_course.course_id', '=', 'courses.id')
                ->whereNotNull('courses.academic_year_id')
                ->select('courses.academic_year_id', 'branch_course.branch_id')
                ->distinct()
                ->get();

            foreach ($branchCoursePairs as $pair) {
                DB::table('academic_year_branch')->insertOrIgnore([
                    'academic_year_id' => $pair->academic_year_id,
                    'branch_id' => $pair->branch_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $resources = DB::table('resources')
            ->join('courses', 'resources.course_id', '=', 'courses.id')
            ->whereNotNull('courses.academic_year_id')
            ->select('courses.academic_year_id', 'resources.id as resource_id')
            ->distinct()
            ->get();

        foreach ($resources as $resource) {
            DB::table('academic_year_resource')->insertOrIgnore([
                'academic_year_id' => $resource->academic_year_id,
                'resource_id' => $resource->resource_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function ensureAcademicYearResourceForeignKey(): void
    {
        if (! Schema::hasTable('academic_year_resource') || ! Schema::hasColumn('academic_year_resource', 'resource_id')) {
            return;
        }

        $createTable = DB::select("SHOW CREATE TABLE academic_year_resource");
        $ddl = $createTable[0]->{'Create Table'} ?? '';

        if (! str_contains($ddl, 'academic_year_resource_resource_id_foreign')) {
            Schema::table('academic_year_resource', function (Blueprint $table) {
                $table->foreign('resource_id')->references('id')->on('resources')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_year_resource');
        Schema::dropIfExists('academic_year_branch');
        Schema::dropIfExists('academic_year_teacher');
        Schema::dropIfExists('academic_year_course');
    }
};
