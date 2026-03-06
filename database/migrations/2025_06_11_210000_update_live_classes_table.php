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
            $table->string('title')->after('id');
            $table->text('description')->nullable()->after('title');
            $table->unsignedBigInteger('teacher_id')->nullable()->after('lesson_id');
            $table->string('status')->default('scheduled')->after('password');
            $table->string('meeting_id')->nullable()->after('status');
            $table->string('password')->nullable()->after('meeting_id');
            
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('set null');
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
