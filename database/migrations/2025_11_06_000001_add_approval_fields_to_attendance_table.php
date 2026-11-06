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
        Schema::table('attendance', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('lesson_id')->constrained()->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->after('approved')->constrained('staff')->onDelete('set null');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('rejection_reason')->nullable()->after('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['branch_id', 'approved_by', 'approved_at', 'rejection_reason']);
        });
    }
};
