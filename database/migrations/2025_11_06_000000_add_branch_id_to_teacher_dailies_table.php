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
        Schema::table('teacher_dailies', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('unit_id')->constrained()->onDelete('cascade');
            $table->boolean('approved')->default(false)->after('date');
            $table->foreignId('approved_by')->nullable()->after('approved')->constrained('staff')->onDelete('set null');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('rejection_reason')->nullable()->after('approved_at');
            $table->foreignId('rejected_by')->nullable()->after('rejection_reason')->constrained('staff')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable()->after('rejected_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_dailies', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['approved', 'approved_by', 'approved_at', 'rejection_reason', 'rejected_by', 'rejected_at']);
        });
    }
};
