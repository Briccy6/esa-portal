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
        if (!Schema::hasTable('journal_items')) {
            Schema::create('journal_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('journal_entry_id')->constrained()->onDelete('cascade');
                $table->foreignId('account_id')->constrained()->onDelete('restrict');
                $table->decimal('debit_amount', 15, 2)->default(0);
                $table->decimal('credit_amount', 15, 2)->default(0);
                $table->text('description')->nullable();
                $table->string('reference_type')->nullable(); // e.g., 'student_transaction', 'expense'
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->timestamps();

                $table->index(['journal_entry_id']);
                $table->index(['account_id']);
                $table->index(['reference_type', 'reference_id']);
                $table->index(['debit_amount']);
                $table->index(['credit_amount']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_items');
    }
};
