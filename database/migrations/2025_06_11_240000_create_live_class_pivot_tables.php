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
        // Create pivot tables for many-to-many relationships
        Schema::create('live_class_branches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('live_class_id');
            $table->unsignedBigInteger('branch_id');
            $table->timestamps();

            $table->foreign('live_class_id')->references('id')->on('live_classes')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unique(['live_class_id', 'branch_id']);
        });

        Schema::create('live_class_courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('live_class_id');
            $table->unsignedBigInteger('course_id');
            $table->timestamps();

            $table->foreign('live_class_id')->references('id')->on('live_classes')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->unique(['live_class_id', 'course_id']);
        });

        Schema::create('live_class_lessons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('live_class_id');
            $table->unsignedBigInteger('lesson_id');
            $table->timestamps();

            $table->foreign('live_class_id')->references('id')->on('live_classes')->onDelete('cascade');
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            $table->unique(['live_class_id', 'lesson_id']);
        });

        // Create live_class_participants table
        Schema::create('live_class_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('live_class_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_type'); // student, teacher, admin, staff, public
            $table->string('email')->nullable();
            $table->string('name')->nullable();
            $table->timestamp('joined_at');
            $table->timestamp('left_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('device_info')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->json('browser_info')->nullable();
            $table->timestamps();

            $table->foreign('live_class_id')->references('id')->on('live_classes')->onDelete('cascade');
            $table->index(['live_class_id', 'user_type', 'is_active']);
            $table->index(['email', 'is_active']);
        });

        // Create live_class_requests table
        Schema::create('live_class_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('live_class_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_type'); // student, public
            $table->string('email')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->text('reason')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejected_reason')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('live_class_id')->references('id')->on('live_classes')->onDelete('cascade');
            $table->index(['live_class_id', 'status']);
        });

        // Create live_class_payments table
        Schema::create('live_class_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('live_class_id');
            $table->string('user_type'); // public
            $table->string('email');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3);
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->unique();
            $table->string('status')->default('pending'); // pending, completed, failed, refunded
            $table->string('payment_gateway')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->text('refund_reason')->nullable();
            $table->timestamps();

            $table->foreign('live_class_id')->references('id')->on('live_classes')->onDelete('cascade');
            $table->index(['live_class_id', 'status']);
            $table->index(['email', 'status']);
        });

        // Add branch_type to branches table if not exists
        if (!Schema::hasColumn('branches', 'branch_type')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->string('branch_type')->default('physical')->after('name'); // online, physical, hybrid
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_class_payments');
        Schema::dropIfExists('live_class_requests');
        Schema::dropIfExists('live_class_participants');
        Schema::dropIfExists('live_class_lessons');
        Schema::dropIfExists('live_class_courses');
        Schema::dropIfExists('live_class_branches');
        
        if (Schema::hasColumn('branches', 'branch_type')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->dropColumn('branch_type');
            });
        }
    }
};
