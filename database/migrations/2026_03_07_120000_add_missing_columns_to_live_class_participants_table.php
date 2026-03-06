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
        Schema::table('live_class_participants', function (Blueprint $table) {
            $table->string('email')->nullable()->after('user_type');
            $table->string('name')->nullable()->after('email');
            $table->timestamp('joined_at')->after('name');
            $table->timestamp('left_at')->nullable()->after('joined_at');
            $table->boolean('is_active')->default(true)->after('left_at');
            $table->json('device_info')->nullable()->after('is_active');
            $table->string('ip_address', 45)->nullable()->after('device_info');
            $table->json('browser_info')->nullable()->after('ip_address');
            
            // Add indexes
            $table->index(['live_class_id', 'user_type', 'is_active']);
            $table->index(['email', 'is_active']);
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
