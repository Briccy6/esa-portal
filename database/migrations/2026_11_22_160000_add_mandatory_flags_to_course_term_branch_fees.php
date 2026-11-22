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
        Schema::table('course_term_branch_fees', function (Blueprint $table) {
            $table->boolean('is_practical_mandatory')->default(false)->after('practical_fee');
            $table->boolean('is_trip_mandatory')->default(false)->after('trip_fee');
            $table->date('practical_due_date')->nullable()->after('is_practical_mandatory');
            $table->date('trip_due_date')->nullable()->after('is_trip_mandatory');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_term_branch_fees', function (Blueprint $table) {
            $table->dropColumn(['is_practical_mandatory', 'is_trip_mandatory', 'practical_due_date', 'trip_due_date']);
        });
    }
};
