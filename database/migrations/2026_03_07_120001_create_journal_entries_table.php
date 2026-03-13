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
        if (!Schema::hasTable('journal_entries')) {
            Schema::create('journal_entries', function (Blueprint $table) {
                $table->id();
                $table->date('entry_date');
                $table->string('reference_number', 50)->unique();
                $table->text('description');
                $table->decimal('total_amount', 15, 2);
                $table->enum('status', ['Draft', 'Posted', 'Void'])->default('Draft');
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('posted_by')->nullable();
                $table->timestamp('posted_at')->nullable();
                $table->timestamps();

                $table->index(['entry_date']);
                $table->index(['status']);
                $table->index(['reference_number']);
                $table->index(['created_by']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
