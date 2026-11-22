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
            $table->boolean('notify_students')->default(false)->after('trip_due_date');
            $table->date('valid_from')->nullable()->after('notify_students');
            $table->date('valid_until')->nullable()->after('valid_from');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_term_branch_fees', function (Blueprint $table) {
            $table->dropColumn(['notify_students', 'valid_from', 'valid_until']);
        });
    }
};
