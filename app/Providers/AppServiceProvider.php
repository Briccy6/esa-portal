<?php

namespace App\Providers;

use App\Models\Announcement;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register mail namespace for mail components
        View::addNamespace('mail', resource_path('views/vendor/mail'));

        // Ensure storage link exists
        if (!file_exists(public_path('storage'))) {
            try {
                \Illuminate\Support\Facades\Artisan::call('storage:link');
            } catch (\Exception $e) {
                // Log the error but don't break the application
                \Illuminate\Support\Facades\Log::error('Failed to create storage link: ' . $e->getMessage());
            }
        }

        View::composer('staff.partials.sidebar_sec', function ($view) {
            $view->with('branches', \App\Models\Branch::orderBy('name')->get(['id', 'name']));
            $view->with('courses', \App\Models\Course::orderBy('course_name')->get(['id', 'course_name']));

            $academicYears = \App\Models\AcademicYear::active()->orderByDesc('starting_date')->get(['id', 'year']);
            if ($academicYears->isEmpty()) {
                $academicYears = \App\Models\AcademicYear::orderByDesc('starting_date')->get(['id', 'year']);
            }

            $view->with('academicYears', $academicYears);
        });

        // View::composer('teacher.layout', function ($view) {
        //     $announcements = Announcement::latest()->take(5)->get();
        //     $view->with('announcements', $announcements);
        // });
    }
}
