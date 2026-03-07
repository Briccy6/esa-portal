<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('live_class_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('live_class_payments', 'name')) {
                $table->string('name')->nullable()->after('email');
            }
            if (!Schema::hasColumn('live_class_payments', 'phone')) {
                $table->string('phone')->nullable()->after('name');
            }
            if (!Schema::hasColumn('live_class_payments', 'amount')) {
                $table->decimal('amount', 10, 2)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('live_class_payments', 'currency')) {
                $table->string('currency', 3)->nullable()->after('amount');
            }
            if (!Schema::hasColumn('live_class_payments', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('currency');
            }
            if (!Schema::hasColumn('live_class_payments', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('live_class_payments', 'status')) {
                $table->string('status')->default('pending')->after('transaction_id');
            }
            if (!Schema::hasColumn('live_class_payments', 'payment_gateway')) {
                $table->string('payment_gateway')->nullable()->after('status');
            }
            if (!Schema::hasColumn('live_class_payments', 'gateway_response')) {
                $table->json('gateway_response')->nullable()->after('payment_gateway');
            }
            if (!Schema::hasColumn('live_class_payments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('gateway_response');
            }
            if (!Schema::hasColumn('live_class_payments', 'refunded_at')) {
                $table->timestamp('refunded_at')->nullable()->after('paid_at');
            }
            if (!Schema::hasColumn('live_class_payments', 'refund_reason')) {
                $table->text('refund_reason')->nullable()->after('refunded_at');
            }
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
