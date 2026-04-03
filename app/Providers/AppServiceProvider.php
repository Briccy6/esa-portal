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

        // View::composer('teacher.layout', function ($view) {
        //     $announcements = Announcement::latest()->take(5)->get();
        //     $view->with('announcements', $announcements);
        // });
    }
}
