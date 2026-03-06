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
        if (!Schema::hasColumn('branches', 'branch_type')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->string('branch_type')->default('physical')->after('name'); // online, physical, hybrid
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('branches', 'branch_type')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->dropColumn('branch_type');
            });
        }
    }
};
