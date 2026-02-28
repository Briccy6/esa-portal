<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // NESA Registration Fields
            $table->boolean('nesa_registered')->default(false)->comment('Student registered for NESA national examination');
            $table->string('nesa_sdms_code')->nullable()->comment('NESA SDMS code for examination registration');
            $table->string('nesa_reg_number')->nullable()->comment('NESA examination registration number');
            $table->string('nesa_document')->nullable()->comment('Document downloaded from NESA website');
            $table->date('nesa_registration_date')->nullable()->comment('Date of NESA registration');
            $table->text('nesa_remarks')->nullable()->comment('Additional remarks about NESA registration');
            
            // Indexes for better performance
            $table->index('nesa_registered');
            $table->index('nesa_sdms_code');
            $table->index('nesa_reg_number');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['nesa_registered']);
            $table->dropIndex(['nesa_sdms_code']);
            $table->dropIndex(['nesa_reg_number']);
            
            $table->dropColumn([
                'nesa_registered',
                'nesa_sdms_code', 
                'nesa_reg_number',
                'nesa_document',
                'nesa_registration_date',
                'nesa_remarks'
            ]);
        });
    }
};
