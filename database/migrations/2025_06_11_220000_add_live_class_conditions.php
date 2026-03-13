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
            if (!Schema::hasColumn('live_classes', 'access_type')) {
                $table->string('access_type')->default('students_only')->after('password'); // students_only, public, both
            }
            if (!Schema::hasColumn('live_classes', 'public_fee')) {
                $table->decimal('public_fee', 10, 2)->nullable()->after('access_type');
            }
            if (!Schema::hasColumn('live_classes', 'currency')) {
                $table->string('currency', 3)->default('USD')->after('public_fee');
            }
            if (!Schema::hasColumn('live_classes', 'required_payment_percentage')) {
                $table->integer('required_payment_percentage')->default(0)->after('currency');
            }
            if (!Schema::hasColumn('live_classes', 'allow_public_requests')) {
                $table->boolean('allow_public_requests')->default(false)->after('required_payment_percentage');
            }
            if (!Schema::hasColumn('live_classes', 'max_participants')) {
                $table->integer('max_participants')->nullable()->after('allow_public_requests');
            }
            if (!Schema::hasColumn('live_classes', 'auto_approve_online_students')) {
                $table->boolean('auto_approve_online_students')->default(true)->after('max_participants');
            }
            if (!Schema::hasColumn('live_classes', 'require_student_approval')) {
                $table->boolean('require_student_approval')->default(false)->after('auto_approve_online_students');
            }
            if (!Schema::hasColumn('live_classes', 'device_restriction')) {
                $table->boolean('device_restriction')->default(true)->after('require_student_approval');
            }
            if (!Schema::hasColumn('live_classes', 'recording_enabled')) {
                $table->boolean('recording_enabled')->default(false)->after('device_restriction');
            }
            if (!Schema::hasColumn('live_classes', 'chat_enabled')) {
                $table->boolean('chat_enabled')->default(true)->after('recording_enabled');
            }
            if (!Schema::hasColumn('live_classes', 'screen_share_enabled')) {
                $table->boolean('screen_share_enabled')->default(true)->after('chat_enabled');
            }
            
            // Remove old columns if they exist
            if (Schema::hasColumn('live_classes', 'branch_id')) {
                $table->dropColumn('branch_id');
            }
            if (Schema::hasColumn('live_classes', 'course_id')) {
                $table->dropColumn('course_id');
            }
            if (Schema::hasColumn('live_classes', 'lesson_id')) {
                $table->dropColumn('lesson_id');
            }
        });

        // Create pivot tables for many-to-many relationships
        if (!Schema::hasTable('live_class_branches')) {
            Schema::create('live_class_branches', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('live_class_id');
                $table->unsignedBigInteger('branch_id');
                $table->timestamps();

                $table->foreign('live_class_id')->references('id')->on('live_classes')->onDelete('cascade');
                $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
                $table->unique(['live_class_id', 'branch_id']);
            });
        }

        if (!Schema::hasTable('live_class_courses')) {
            Schema::create('live_class_courses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('live_class_id');
                $table->unsignedBigInteger('course_id');
                $table->timestamps();

                $table->foreign('live_class_id')->references('id')->on('live_classes')->onDelete('cascade');
                $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
                $table->unique(['live_class_id', 'course_id']);
            });
        }

        if (!Schema::hasTable('live_class_lessons')) {
            Schema::create('live_class_lessons', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('live_class_id');
                $table->unsignedBigInteger('lesson_id');
                $table->timestamps();

                $table->foreign('live_class_id')->references('id')->on('live_classes')->onDelete('cascade');
                $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
                $table->unique(['live_class_id', 'lesson_id']);
            });
        }

        // Create live_class_participants table
        if (!Schema::hasTable('live_class_participants')) {
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
        }

        // Create live_class_requests table
        if (!Schema::hasTable('live_class_requests')) {
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
                $table->foreign('approved_by')->references('id')->on('admins')->onDelete('set null');
                $table->index(['live_class_id', 'status']);
            });
        }

        // Create live_class_payments table
        if (!Schema::hasTable('live_class_payments')) {
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
        }

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

        Schema::table('live_classes', function (Blueprint $table) {
            $table->dropColumn([
                'access_type',
                'public_fee',
                'currency',
                'required_payment_percentage',
                'allow_public_requests',
                'max_participants',
                'auto_approve_online_students',
                'require_student_approval',
                'device_restriction',
                'recording_enabled',
                'chat_enabled',
                'screen_share_enabled'
            ]);
        });

        // Note: We're not removing branch_type column as it might be used elsewhere
    }
};
