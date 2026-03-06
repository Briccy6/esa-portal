<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Policies\ExamPolicy;
use App\Policies\QuestionBankPolicy;
use App\Models\Exam;
use App\Models\QuestionBank;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Exam::class => ExamPolicy::class,
        QuestionBank::class => QuestionBankPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define gates for additional permissions
        Gate::define('manage-exams', function ($user) {
            return $user->admin || $user->teacher;
        });

        Gate::define('manage-question-banks', function ($user) {
            return $user->admin || $user->teacher;
        });

        Gate::define('view-all-exams', function ($user) {
            return $user->admin || $user->staff;
        });

        Gate::define('view-all-question-banks', function ($user) {
            return $user->admin || $user->staff;
        });

        Gate::define('grade-exams', function ($user) {
            return $user->admin || $user->teacher;
        });

        Gate::define('access-exam-analytics', function ($user) {
            return $user->admin || $user->teacher;
        });
    }
}
