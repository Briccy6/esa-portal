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
        if (!Schema::hasTable('staff_branch')) {
            Schema::create('staff_branch', function (Blueprint $table) {
                $table->foreignId('staff_id')->constrained()->onDelete('cascade');
                $table->foreignId('branch_id')->constrained()->onDelete('cascade');
                $table->timestamps();
                
                $table->primary(['staff_id', 'branch_id']);
                $table->index(['branch_id', 'staff_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_branch');
    }
};
