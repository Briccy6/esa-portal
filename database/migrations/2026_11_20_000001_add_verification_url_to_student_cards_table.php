<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_cards', function (Blueprint $table) {
            if (!Schema::hasColumn('student_cards', 'verification_url')) {
                $table->text('verification_url')->nullable()->after('qr_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_cards', function (Blueprint $table) {
            if (Schema::hasColumn('student_cards', 'verification_url')) {
                $table->dropColumn('verification_url');
            }
        });
    }
};
