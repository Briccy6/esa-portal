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
        Schema::create('to_whom_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('generated_by')->nullable(); // User who generated the letter
            $table->string('letter_type'); // enrollment, academic, financial, etc.
            $table->string('recipient_title'); // To Whom It May Concern, Dear Sir/Madam, etc.
            $table->string('purpose'); // Purpose of the letter
            $table->text('content')->nullable(); // Generated content
            $table->date('letter_date');
            $table->string('reference_number')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['student_id', 'letter_type']);
            $table->index(['reference_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('to_whom_letters');
    }
};
