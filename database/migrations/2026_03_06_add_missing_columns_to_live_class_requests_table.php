<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('live_class_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('live_class_requests', 'email')) {
                $table->string('email')->nullable()->after('user_type');
            }
            if (!Schema::hasColumn('live_class_requests', 'name')) {
                $table->string('name')->nullable()->after('email');
            }
            if (!Schema::hasColumn('live_class_requests', 'phone')) {
                $table->string('phone')->nullable()->after('name');
            }
            if (!Schema::hasColumn('live_class_requests', 'reason')) {
                $table->text('reason')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('live_class_requests', 'status')) {
                $table->string('status')->default('pending')->after('reason'); // pending, approved, rejected
            }
            if (!Schema::hasColumn('live_class_requests', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('status');
            }
            if (!Schema::hasColumn('live_class_requests', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('live_class_requests', 'rejected_reason')) {
                $table->text('rejected_reason')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('live_class_requests', 'processed_at')) {
                $table->timestamp('processed_at')->nullable()->after('rejected_reason');
            }
            
            // Only add index if it doesn't exist
            if (!collect(DB::select("SHOW INDEXES FROM live_class_requests WHERE Key_name = 'live_class_requests_live_class_id_status_index'"))->count()) {
                $table->index(['live_class_id', 'status']);
            }
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
