<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('to_whom_letters', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable()->change();
            $table->string('recipient_type')->default('student');
            $table->string('recipient_name')->nullable();
            $table->string('recipient_role')->nullable();
            $table->string('verification_token', 64)->nullable()->unique();
        });

        DB::table('to_whom_letters')
            ->whereNull('verification_token')
            ->orderBy('id')
            ->chunkById(100, function ($letters) {
                foreach ($letters as $letter) {
                    DB::table('to_whom_letters')
                        ->where('id', $letter->id)
                        ->update(['verification_token' => Str::random(64)]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('to_whom_letters', function (Blueprint $table) {
            $table->dropUnique(['verification_token']);
            $table->dropColumn([
                'recipient_type',
                'recipient_name',
                'recipient_role',
                'verification_token',
            ]);
        });
    }
};
