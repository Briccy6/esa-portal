<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('students', 'enrollment_term_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->foreignId('enrollment_term_id')
                    ->nullable()
                    ->after('academic_year_id')
                    ->constrained('terms')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('students', 'enrollment_term_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropForeign(['enrollment_term_id']);
                $table->dropColumn('enrollment_term_id');
            });
        }
    }
};
