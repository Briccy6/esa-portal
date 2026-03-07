<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subscription;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $subscriptions = [
            [
                'name' => 'Basic',
                'description' => 'Perfect for students starting out',
                'tokens_included' => 25,
                'price' => 5.00,
                'tier' => 'basic',
                'duration_days' => 30,
                'is_active' => true,
                'is_recommended' => false,
                'features' => [
                    '25 exam tokens',
                    'Access to basic exams',
                    'Email support',
                    '30 days validity'
                ],
            ],
            [
                'name' => 'Standard',
                'description' => 'Most popular choice for regular students',
                'tokens_included' => 75,
                'price' => 15.00,
                'tier' => 'standard',
                'duration_days' => 30,
                'is_active' => true,
                'is_recommended' => true,
                'features' => [
                    '75 exam tokens',
                    'Access to all exams',
                    'Priority support',
                    '30 days validity',
                    'Exam analytics'
                ],
            ],
            [
                'name' => 'Premium',
                'description' => 'Best value for serious students',
                'tokens_included' => 200,
                'price' => 35.00,
                'tier' => 'premium',
                'duration_days' => 30,
                'is_active' => true,
                'is_recommended' => false,
                'features' => [
                    '200 exam tokens',
                    'Unlimited exam access',
                    '24/7 support',
                    '30 days validity',
                    'Advanced analytics',
                    'Study materials',
                    'Mock exams'
                ],
            ],
            [
                'name' => 'Student Monthly',
                'description' => 'Affordable monthly plan for students',
                'tokens_included' => 50,
                'price' => 8.00,
                'tier' => 'basic',
                'duration_days' => 30,
                'is_active' => true,
                'is_recommended' => false,
                'features' => [
                    '50 exam tokens monthly',
                    'Access to all exams',
                    'Email support',
                    'Auto-renew option'
                ],
            ],
            [
                'name' => 'Semester Pass',
                'description' => 'Complete semester access',
                'tokens_included' => 500,
                'price' => 80.00,
                'tier' => 'premium',
                'duration_days' => 120,
                'is_active' => true,
                'is_recommended' => false,
                'features' => [
                    '500 exam tokens',
                    '4 months access',
                    'All premium features',
                    'Study group access',
                    'Career guidance'
                ],
            ],
        ];

        foreach ($subscriptions as $subscription) {
            Subscription::create($subscription);
        }
    }
}
