<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('branch_course')) {
            return;
        }

        // Remove duplicate pivot records before adding a unique constraint.
        $duplicates = DB::table('branch_course')
            ->select('branch_id', 'course_id', DB::raw('MIN(id) as keep_id'))
            ->groupBy('branch_id', 'course_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            DB::table('branch_course')
                ->where('branch_id', $duplicate->branch_id)
                ->where('course_id', $duplicate->course_id)
                ->where('id', '!=', $duplicate->keep_id)
                ->delete();
        }

        Schema::table('branch_course', function (Blueprint $table) {
            if (! $this->indexExists('branch_course', 'branch_course_branch_id_course_id_unique')) {
                $table->unique(['branch_id', 'course_id']);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('branch_course')) {
            return;
        }

        Schema::table('branch_course', function (Blueprint $table) {
            $table->dropUnique(['branch_id', 'course_id']);
        });
    }

    protected function indexExists(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        return ! empty($indexes);
    }
};
