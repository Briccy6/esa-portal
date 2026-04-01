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
        Schema::table('student_subscriptions', function (Blueprint $table) {
            $table->decimal('amount', 8, 2)->default(0)->after('subscription_id');
            $table->integer('tokens_included')->default(0)->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['amount', 'tokens_included']);
        });
    }
};
