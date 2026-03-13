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
        if (!Schema::hasTable('accounts')) {
            Schema::create('accounts', function (Blueprint $table) {
                $table->id();
                $table->string('account_code', 20)->unique();
                $table->string('account_name', 200);
                $table->enum('account_type', ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense']);
                $table->enum('normal_balance', ['Debit', 'Credit']);
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_cash_account')->default(false);
                $table->enum('cash_flow_category', ['Operating', 'Investing', 'Financing', 'Non-cash'])->nullable();
                $table->integer('parent_account_id')->nullable();
                $table->integer('level')->default(1);
                $table->timestamps();

                $table->index(['account_type']);
                $table->index(['is_active']);
                $table->index(['is_cash_account']);
                $table->index(['cash_flow_category']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
