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
        Schema::table('live_class_requests', function (Blueprint $table) {
            $table->string('email')->nullable()->after('user_type');
            $table->string('name')->nullable()->after('email');
            $table->string('phone')->nullable()->after('name');
            $table->text('reason')->nullable()->after('phone');
            $table->string('status')->default('pending')->after('reason'); // pending, approved, rejected
            $table->unsignedBigInteger('approved_by')->nullable()->after('status');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('rejected_reason')->nullable()->after('approved_at');
            $table->timestamp('processed_at')->nullable()->after('rejected_reason');
            
            $table->index(['live_class_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('live_class_requests', function (Blueprint $table) {
            $table->dropColumn(['email', 'name', 'phone', 'reason', 'status', 'approved_by', 'approved_at', 'rejected_reason', 'processed_at']);
            $table->dropIndex(['live_class_id', 'status']);
        });
    }
};
