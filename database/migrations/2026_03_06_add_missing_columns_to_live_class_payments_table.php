<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('live_class_payments', function (Blueprint $table) {
            $table->string('name')->nullable()->after('email');
            $table->string('phone')->nullable()->after('name');
            $table->decimal('amount', 10, 2)->nullable()->after('phone');
            $table->string('currency', 3)->nullable()->after('amount');
            $table->string('payment_method')->nullable()->after('currency');
            $table->string('transaction_id')->nullable()->after('payment_method');
            $table->string('status')->default('pending')->after('transaction_id');
            $table->string('payment_gateway')->nullable()->after('status');
            $table->json('gateway_response')->nullable()->after('payment_gateway');
            $table->timestamp('paid_at')->nullable()->after('gateway_response');
            $table->timestamp('refunded_at')->nullable()->after('paid_at');
            $table->text('refund_reason')->nullable()->after('refunded_at');
        });
    }

    public function down()
    {
        Schema::table('live_class_payments', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'phone',
                'amount',
                'currency',
                'payment_method',
                'transaction_id',
                'status',
                'payment_gateway',
                'gateway_response',
                'paid_at',
                'refunded_at',
                'refund_reason'
            ]);
        });
    }
};
