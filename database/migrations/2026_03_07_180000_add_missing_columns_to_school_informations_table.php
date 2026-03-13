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
        Schema::table('school_informations', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('school_informations', 'logo')) {
                $table->binary('logo')->nullable()->after('logo_path');
            }
            if (!Schema::hasColumn('school_informations', 'principal_signature_data')) {
                $table->binary('principal_signature_data')->nullable()->after('principal_signature');
            }
            if (!Schema::hasColumn('school_informations', 'stamp_signature_data')) {
                $table->binary('stamp_signature_data')->nullable()->after('stamp_signature');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_informations', function (Blueprint $table) {
            $table->dropColumn(['logo', 'principal_signature_data', 'stamp_signature_data']);
        });
    }
};
