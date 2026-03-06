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
            // Add new columns at the end of the table
            $table->string('title')->after('summary');
            $table->text('description')->nullable()->after('title');
            $table->unsignedBigInteger('teacher_id')->nullable()->after('description');
            $table->string('status')->default('scheduled')->after('teacher_id');
            $table->string('meeting_id')->nullable()->after('status');
            $table->string('password')->nullable()->after('meeting_id');
            
            // Add access control columns
            $table->string('access_type')->default('students_only')->after('password');
            $table->decimal('public_fee', 10, 2)->nullable()->after('access_type');
            $table->string('currency', 3)->default('USD')->after('public_fee');
            $table->integer('required_payment_percentage')->default(0)->after('currency');
            $table->boolean('allow_public_requests')->default(false)->after('required_payment_percentage');
            $table->integer('max_participants')->nullable()->after('allow_public_requests');
            $table->boolean('auto_approve_online_students')->default(true)->after('max_participants');
            $table->boolean('require_student_approval')->default(false)->after('auto_approve_online_students');
            $table->boolean('device_restriction')->default(true)->after('require_student_approval');
            $table->boolean('recording_enabled')->default(false)->after('device_restriction');
            $table->boolean('chat_enabled')->default(true)->after('recording_enabled');
            $table->boolean('screen_share_enabled')->default(true)->after('chat_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('live_classes', function (Blueprint $table) {
            $table->dropColumn([
                'title', 'description', 'teacher_id', 'status', 'meeting_id', 'password',
                'access_type', 'public_fee', 'currency', 'required_payment_percentage',
                'allow_public_requests', 'max_participants', 'auto_approve_online_students',
                'require_student_approval', 'device_restriction', 'recording_enabled',
                'chat_enabled', 'screen_share_enabled'
            ]);
        });
    }
};
