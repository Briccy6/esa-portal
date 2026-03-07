<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tokens')) {
            Schema::create('tokens', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained()->onDelete('cascade');
                $table->integer('balance')->default(0)->comment('Current token balance');
                $table->integer('total_earned')->default(0)->comment('Total tokens earned');
                $table->integer('total_spent')->default(0)->comment('Total tokens spent');
                $table->timestamp('last_earned_at')->nullable();
                $table->timestamp('last_spent_at')->nullable();
                $table->timestamps();

                $table->index(['student_id', 'balance']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tokens');
    }
};
