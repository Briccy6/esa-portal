<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
          if (!Schema::hasTable('students')) {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('id_number')->nullable();
                         $table->string('education_level')->nullable();
            $table->string('identification_type')->nullable();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->string('reg_number')->unique(); // Example: ESAPCB89762025-2026
            $table->string('password');
              $table->boolean('payed_registration_fees')->default(false);
               $table->decimal('discount', 8, 2)->nullable()->default(0.00)->comment('Discount amount for the student');

            // Document uploads
            $table->string('senior6_document')->nullable();
            $table->string('senior3_document')->nullable();
            $table->string('other_supporting_document')->nullable();
            $table->string('id_photo')->nullable()->change();
            $table->string('passport_photo')->nullable()->change();

            $table->timestamps();
        });
    }
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
