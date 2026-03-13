<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $subscriptions = [
            [
                'name' => 'Basic',
                'description' => 'Perfect for students starting out',
                'tokens_included' => 25,
                'price' => 12500.00, // 25 tokens * 500 RWF each
                'tier' => 'basic',
                'duration_days' => 30,
                'is_active' => true,
                'is_recommended' => false,
                'features' => json_encode([
                    '25 exam tokens',
                    'Access to basic exams',
                    'Email support',
                    '30 days validity'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Standard',
                'description' => 'Most popular choice for regular students',
                'tokens_included' => 75,
                'price' => 37500.00, // 75 tokens * 500 RWF each
                'tier' => 'standard',
                'duration_days' => 30,
                'is_active' => true,
                'is_recommended' => true,
                'features' => json_encode([
                    '75 exam tokens',
                    'Access to all exams',
                    'Priority support',
                    '30 days validity',
                    'Exam analytics'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Premium',
                'description' => 'Best value for serious students',
                'tokens_included' => 200,
                'price' => 100000.00, // 200 tokens * 500 RWF each
                'tier' => 'premium',
                'duration_days' => 30,
                'is_active' => true,
                'is_recommended' => false,
                'features' => json_encode([
                    '200 exam tokens',
                    'Unlimited exam access',
                    '24/7 support',
                    '30 days validity',
                    'Advanced analytics',
                    'Study materials',
                    'Mock exams'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Student Monthly',
                'description' => 'Affordable monthly plan for students',
                'tokens_included' => 50,
                'price' => 25000.00, // 50 tokens * 500 RWF each
                'tier' => 'basic',
                'duration_days' => 30,
                'is_active' => true,
                'is_recommended' => false,
                'features' => json_encode([
                    '50 exam tokens monthly',
                    'Access to all exams',
                    'Email support',
                    'Auto-renew option'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Semester Pass',
                'description' => 'Complete semester access',
                'tokens_included' => 500,
                'price' => 250000.00, // 500 tokens * 500 RWF each
                'tier' => 'premium',
                'duration_days' => 120,
                'is_active' => true,
                'is_recommended' => false,
                'features' => json_encode([
                    '500 exam tokens',
                    '4 months access',
                    'All premium features',
                    'Study group access',
                    'Career guidance'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert subscriptions only if they don't already exist
        foreach ($subscriptions as $subscription) {
            DB::table('subscriptions')->updateOrInsert(
                ['name' => $subscription['name']],
                $subscription
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('subscriptions')->whereIn('name', [
            'Basic',
            'Standard', 
            'Premium',
            'Student Monthly',
            'Semester Pass'
        ])->delete();
    }
};
