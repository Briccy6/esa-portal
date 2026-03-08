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
        Schema::create('client_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->integer('balance')->default(0);
            $table->integer('amount_added')->nullable();
            $table->integer('amount_deducted')->nullable();
            $table->text('description')->nullable();
            $table->string('type')->default('purchase'); // purchase, subscription, usage, bonus
            $table->timestamps();
            
            $table->index(['client_id', 'type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_tokens');
    }
};
