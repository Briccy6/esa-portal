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
        Schema::create('client_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->string('tx_ref')->unique();
            $table->string('type'); // subscription, token_purchase, etc.
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('RWF');
            $table->string('status')->default('pending');
            $table->string('payment_method')->nullable();
            $table->json('gateway_response')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            $table->index(['client_id', 'status']);
            $table->index('tx_ref');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_transactions');
    }
};
