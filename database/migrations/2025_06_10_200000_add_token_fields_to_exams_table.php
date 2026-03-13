<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure exams table exists before altering
        if (!Schema::hasTable('exams')) {
            return;
        }

        Schema::table('exams', function (Blueprint $table) {
            if (!Schema::hasColumn('exams', 'token_cost')) {
                $table->integer('token_cost')->default(0)->after('is_active')->comment('Tokens required to attempt this exam');
            }
            if (!Schema::hasColumn('exams', 'is_public')) {
                $table->boolean('is_public')->default(false)->after('token_cost')->comment('Whether exam is available to public users');
            }
            if (!Schema::hasColumn('exams', 'public_price')) {
                $table->decimal('public_price', 8, 2)->default(0)->after('is_public')->comment('Price for public users to attempt exam');
            }
            if (!Schema::hasColumn('exams', 'start_time')) {
                $table->timestamp('start_time')->nullable()->after('public_price')->comment('When exam becomes available');
            }
            if (!Schema::hasColumn('exams', 'end_time')) {
                $table->timestamp('end_time')->nullable()->after('start_time')->comment('When exam becomes unavailable');
            }
            if (!Schema::hasColumn('exams', 'allow_public_after_end')) {
                $table->boolean('allow_public_after_end')->default(false)->after('end_time')->comment('Allow public access after exam end time');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('exams')) {
            return;
        }

        Schema::table('exams', function (Blueprint $table) {
            $columns = [
                'token_cost',
                'is_public',
                'public_price',
                'start_time',
                'end_time',
                'allow_public_after_end',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('exams', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

