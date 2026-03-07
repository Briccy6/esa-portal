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
        Schema::table('live_classes', function (Blueprint $table) {
            if (!Schema::hasColumn('live_classes', 'title')) {
                $table->string('title')->after('id');
            }
            if (!Schema::hasColumn('live_classes', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (!Schema::hasColumn('live_classes', 'teacher_id')) {
                $table->unsignedBigInteger('teacher_id')->nullable()->after('description');
                $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('set null');
            }
            if (!Schema::hasColumn('live_classes', 'meeting_id')) {
                $table->string('meeting_id')->nullable()->after('teacher_id');
            }
            if (!Schema::hasColumn('live_classes', 'password')) {
                $table->string('password')->nullable()->after('meeting_id');
            }
            if (!Schema::hasColumn('live_classes', 'status')) {
                $table->string('status')->default('scheduled')->after('password');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('live_classes', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropColumn([
                'title',
                'description', 
                'teacher_id',
                'status',
                'meeting_id',
                'password'
            ]);
        });
    }
};
