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
        Schema::table('student_transactions', function (Blueprint $table) {
            $table->foreignId('term_id')->nullable()->after('student_id')->constrained()->onDelete('set null');
            $table->index(['term_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_transactions', function (Blueprint $table) {
            $table->dropForeign(['term_id']);
            $table->dropIndex(['term_id']);
            $table->dropColumn('term_id');
        });
    }
};
