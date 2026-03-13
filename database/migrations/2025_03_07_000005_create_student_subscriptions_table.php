<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('student_subscriptions')) {
            Schema::create('student_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained()->onDelete('cascade');
                $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
                $table->decimal('paid_amount', 8, 2);
                $table->string('payment_reference')->nullable();
                $table->string('payment_status')->default('pending')->comment('pending, paid, failed, refunded');
                $table->timestamp('starts_at')->useCurrent();
                $table->timestamp('ends_at')->useCurrent();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['student_id', 'is_active']);
                $table->index(['payment_status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('student_subscriptions');
    }
};
