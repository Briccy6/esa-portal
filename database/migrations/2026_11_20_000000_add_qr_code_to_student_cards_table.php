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
        Schema::table('student_cards', function (Blueprint $table) {
            if (!Schema::hasColumn('student_cards', 'passport_photo')) {
                $table->longText('passport_photo')->nullable()->after('branch');
            }

            if (!Schema::hasColumn('student_cards', 'academic_year')) {
                $table->string('academic_year')->nullable()->after('passport_photo');
            }

            if (!Schema::hasColumn('student_cards', 'qr_code')) {
                $table->longText('qr_code')->nullable()->after('academic_year');
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_cards', function (Blueprint $table) {
            if (Schema::hasColumn('student_cards', 'qr_code')) {
                $table->dropColumn('qr_code');
            }
        });
    }
};
