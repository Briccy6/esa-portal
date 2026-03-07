<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('token_transactions')) {
            Schema::create('token_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('token_id')->constrained()->onDelete('cascade');
                $table->integer('amount');
                $table->enum('type', ['credit', 'debit']);
                $table->string('description')->nullable();
                $table->string('source')->default('system')->comment('system, exam, purchase, refund');
                $table->integer('balance_after')->comment('Token balance after this transaction');
                $table->nullableMorphs('referenceable');
                // Related model like exam, subscription
                $table->timestamps();

                $table->index(['token_id', 'type']);
                $table->index(['source']);
                $table->index(['created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('token_transactions');
    }
};
