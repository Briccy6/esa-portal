<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('dismissed_students')) {
            return;
        }

        Schema::table('dismissed_students', function (Blueprint $table) {
            if (!Schema::hasColumn('dismissed_students', 'payed_registration_fees')) {
                $table->boolean('payed_registration_fees')->default(0)->after('password');
            }
        });

        // Nullable sex/phone to match `students` (MariaDB / MySQL)
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE dismissed_students MODIFY sex VARCHAR(20) NULL');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE dismissed_students MODIFY phone VARCHAR(255) NULL');
    }

    public function down(): void
    {
        if (!Schema::hasTable('dismissed_students')) {
            return;
        }

        if (Schema::hasColumn('dismissed_students', 'payed_registration_fees')) {
            Schema::table('dismissed_students', function (Blueprint $table) {
                $table->dropColumn('payed_registration_fees');
            });
        }
    }
};
