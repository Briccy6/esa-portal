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
        if (!Schema::hasTable('teacher_daily_payments')) {
            Schema::create('teacher_daily_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('teacher_daily_id')->constrained()->onDelete('cascade');
                $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
                $table->decimal('calculated_amount', 15, 2)->default(0);
                $table->decimal('amount_paid', 15, 2)->default(0);
                $table->enum('status', ['pending', 'approved', 'paid', 'partial'])->default('pending');
                $table->date('work_date');
                $table->text('notes')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('staff')->onDelete('set null');
                $table->timestamp('paid_at')->nullable();
                $table->foreignId('paid_by')->nullable()->constrained('staff')->onDelete('set null');
                $table->timestamps();

                $table->index(['teacher_id', 'status']);
                $table->index(['status', 'approved_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_daily_payments');
    }
};
