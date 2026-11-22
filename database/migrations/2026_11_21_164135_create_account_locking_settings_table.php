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
        Schema::create('account_locking_settings', function (Blueprint $table) {
            $table->id();
            $table->string('role')->index(); // accountant, student, dos, teacher, client
            $table->string('setting_key')->index(); // e.g., 'student_count_limit', 'payment_percentage_threshold'
            $table->text('setting_value'); // JSON or string value
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('effective_from')->nullable();
            $table->timestamp('effective_until')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['role', 'setting_key', 'branch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_locking_settings');
    }
};
