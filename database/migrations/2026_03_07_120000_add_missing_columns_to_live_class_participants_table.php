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
        Schema::table('live_class_participants', function (Blueprint $table) {
            if (!Schema::hasColumn('live_class_participants', 'email')) {
                $table->string('email')->nullable()->after('user_type');
            }
            if (!Schema::hasColumn('live_class_participants', 'name')) {
                $table->string('name')->nullable()->after('email');
            }
            if (!Schema::hasColumn('live_class_participants', 'joined_at')) {
                $table->timestamp('joined_at')->after('name');
            }
            if (!Schema::hasColumn('live_class_participants', 'left_at')) {
                $table->timestamp('left_at')->nullable()->after('joined_at');
            }
            if (!Schema::hasColumn('live_class_participants', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('left_at');
            }
            if (!Schema::hasColumn('live_class_participants', 'device_info')) {
                $table->json('device_info')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('live_class_participants', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('device_info');
            }
            if (!Schema::hasColumn('live_class_participants', 'browser_info')) {
                $table->json('browser_info')->nullable()->after('ip_address');
            }
            
            // Add indexes only if they don't exist
            if (!collect(DB::select("SHOW INDEXES FROM live_class_participants WHERE Key_name = 'live_class_participants_live_class_id_user_type_is_active_index'"))->count()) {
                $table->index(['live_class_id', 'user_type', 'is_active']);
            }
            if (!collect(DB::select("SHOW INDEXES FROM live_class_participants WHERE Key_name = 'live_class_participants_email_is_active_index'"))->count()) {
                $table->index(['email', 'is_active']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('live_class_participants', function (Blueprint $table) {
            $table->dropIndex(['live_class_id', 'user_type', 'is_active']);
            $table->dropIndex(['email', 'is_active']);
            $table->dropColumn([
                'email',
                'name', 
                'joined_at',
                'left_at',
                'is_active',
                'device_info',
                'ip_address',
                'browser_info'
            ]);
        });
    }
};
