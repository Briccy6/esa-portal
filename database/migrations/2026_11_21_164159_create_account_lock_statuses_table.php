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
        Schema::create('account_lock_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('user_type'); // staff, student, admin
            $table->unsignedBigInteger('user_id');
            $table->string('lock_reason'); // e.g., 'student_count_exceeded', 'expense_not_recorded', 'payment_percentage_not_met'
            $table->text('lock_details')->nullable(); // JSON with additional details
            $table->boolean('is_locked')->default(true);
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('unlock_at')->nullable(); // For automatic unlocking
            $table->timestamp('unlocked_at')->nullable();
            $table->foreignId('unlocked_by')->nullable()->constrained('staff')->onDelete('set null');
            $table->text('unlock_notes')->nullable();
            $table->timestamps();

            $table->index(['user_type', 'user_id']);
            $table->index(['is_locked', 'unlock_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_lock_statuses');
    }
};
