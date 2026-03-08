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
        Schema::create('client_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->string('payment_status')->default('pending');
            $table->string('payment_reference')->nullable();
            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(false);
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('RWF');
            $table->string('payment_gateway')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamps();
            
            $table->index(['client_id', 'payment_status']);
            $table->index(['subscription_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_subscriptions');
    }
};
