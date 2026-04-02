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
        Schema::table('course_term_fees', function (Blueprint $table) {
            // Add is_active column if it doesn't exist
            if (!Schema::hasColumn('course_term_fees', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('student_card_fee');
            }
            
            // Add notes column if it doesn't exist
            if (!Schema::hasColumn('course_term_fees', 'notes')) {
                $table->text('notes')->nullable()->after('is_active');
            }
            
            // Add index if it doesn't exist
            if (!Schema::hasIndex('course_term_fees', ['course_id', 'term_id', 'is_active'])) {
                $table->index(['course_id', 'term_id', 'is_active']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_term_fees', function (Blueprint $table) {
            $table->dropIndex(['course_id', 'term_id', 'is_active']);
            $table->dropColumn(['is_active', 'notes']);
        });
    }
};
