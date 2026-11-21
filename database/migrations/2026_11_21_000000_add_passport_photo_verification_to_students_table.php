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
        Schema::table('students', function (Blueprint $table) {
            $table->enum('passport_photo_verification_status', ['pending', 'approved', 'rejected', 'manual_review'])
                  ->default('pending')
                  ->after('passport_photo');
            $table->timestamp('passport_photo_verified_at')->nullable()->after('passport_photo_verification_status');
            $table->json('passport_photo_verification_result')->nullable()->after('passport_photo_verified_at');
            $table->string('passport_photo_rejection_reason')->nullable()->after('passport_photo_verification_result');
            $table->decimal('passport_photo_confidence_score', 3, 2)->nullable()->after('passport_photo_rejection_reason');
            $table->boolean('passport_photo_requires_manual_review')->default(false)->after('passport_photo_confidence_score');
            $table->unsignedBigInteger('passport_photo_verified_by')->nullable()->after('passport_photo_requires_manual_review');
            // Note: Foreign key constraint removed as it may cause issues if admins table structure differs
            // $table->foreign('passport_photo_verified_by')->references('id')->on('admins')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'passport_photo_verification_status',
                'passport_photo_verified_at',
                'passport_photo_verification_result',
                'passport_photo_rejection_reason',
                'passport_photo_confidence_score',
                'passport_photo_requires_manual_review',
                'passport_photo_verified_by',
            ]);
        });
    }
};
