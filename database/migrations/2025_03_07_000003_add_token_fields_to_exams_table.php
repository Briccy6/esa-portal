<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->integer('token_cost')->default(0)->after('is_active')->comment('Tokens required to attempt this exam');
            $table->boolean('is_public')->default(false)->after('token_cost')->comment('Whether exam is available to public users');
            $table->decimal('public_price', 8, 2)->default(0)->after('is_public')->comment('Price for public users to attempt exam');
            $table->timestamp('start_time')->nullable()->after('public_price')->comment('When exam becomes available');
            $table->timestamp('end_time')->nullable()->after('start_time')->comment('When exam becomes unavailable');
            $table->boolean('allow_public_after_end')->default(false)->after('end_time')->comment('Allow public access after exam end time');
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn([
                'token_cost',
                'is_public',
                'public_price',
                'start_time',
                'end_time',
                'allow_public_after_end'
            ]);
        });
    }
};
