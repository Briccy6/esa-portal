<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_activity_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('guard', 20);
            $table->unsignedBigInteger('user_id');
            $table->timestamp('last_active_at');

            $table->unique(['guard', 'user_id'], 'user_activity_status_identity_unique');
            $table->index(['guard', 'last_active_at']);
        });

        Schema::create('user_activity_events', function (Blueprint $table) {
            $table->id();
            $table->string('guard', 20);
            $table->unsignedBigInteger('user_id');
            $table->string('route_name', 255);
            $table->date('activity_date');
            $table->unsignedInteger('visits')->default(0);
            $table->timestamp('last_visited_at');
            $table->timestamps();

            $table->unique(
                ['guard', 'user_id', 'route_name', 'activity_date'],
                'user_activity_route_daily_unique'
            );
            $table->index(['guard', 'last_visited_at']);
            $table->index(['activity_date', 'guard']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_activity_events');
        Schema::dropIfExists('user_activity_statuses');
    }
};
