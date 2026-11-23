<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lesson_assignments', function (Blueprint $table) {
            $table->longText('question')->nullable()->after('description');
            $table->string('attachment_path')->nullable()->after('allowed_file_types');
            $table->string('attachment_original_name')->nullable()->after('attachment_path');
        });
    }

    public function down(): void
    {
        Schema::table('lesson_assignments', function (Blueprint $table) {
            $table->dropColumn(['question', 'attachment_path', 'attachment_original_name']);
        });
    }
};
