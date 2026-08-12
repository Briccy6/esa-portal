<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('timetables') && Schema::hasColumn('timetables', 'image')) {
            // Make the image column nullable with a NULL default to avoid insert errors
            DB::statement("ALTER TABLE `timetables` MODIFY `image` VARCHAR(255) NULL DEFAULT NULL");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('timetables') && Schema::hasColumn('timetables', 'image')) {
            // Revert to NOT NULL with empty string default
            DB::statement("ALTER TABLE `timetables` MODIFY `image` VARCHAR(255) NOT NULL DEFAULT ''");
        }
    }
};
