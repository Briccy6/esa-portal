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
          if (!Schema::hasTable('student_transactions')) {
               Schema::create('student_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('tx_ref')->unique(); // Flutterwave transaction reference
            $table->string('transaction_id')->nullable(); // Flutterwave transaction id
            $table->string('type'); // e.g. registration, tuition, topup
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('RWF');
            $table->string('status')->default('pending'); // pending, successful, failed
            $table->json('meta')->nullable(); // Any extra info (e.g. payment method)
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_transactions');
    }
};
