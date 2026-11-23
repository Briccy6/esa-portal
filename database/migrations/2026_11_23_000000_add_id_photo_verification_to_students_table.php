<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->enum('id_photo_verification_status', ['pending', 'approved'])
                ->default('pending')
                ->after('id_photo');
            $table->timestamp('id_photo_verified_at')->nullable()->after('id_photo_verification_status');
            $table->json('id_photo_verification_result')->nullable()->after('id_photo_verified_at');
            $table->decimal('id_photo_confidence_score', 3, 2)->nullable()->after('id_photo_verification_result');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'id_photo_verification_status',
                'id_photo_verified_at',
                'id_photo_verification_result',
                'id_photo_confidence_score',
            ]);
        });
    }
};
