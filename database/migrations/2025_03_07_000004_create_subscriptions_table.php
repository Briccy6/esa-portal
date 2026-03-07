<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->integer('tokens_included');
                $table->decimal('price', 8, 2);
                $table->string('tier')->default('basic')->comment('basic, standard, premium');
                $table->integer('duration_days')->default(30);
                $table->boolean('is_active')->default(true);
                $table->boolean('is_recommended')->default(false);
                $table->json('features')->nullable();
                $table->timestamps();

                $table->index(['tier', 'is_active']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
