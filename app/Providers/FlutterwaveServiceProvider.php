<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Flutterwavedev\Flutterwave\Flutterwave;

class FlutterwaveServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only configure Flutterwave if all required keys are present
        if (config('services.flutterwave.secret_key') && config('services.flutterwave.public_key')) {
            try {
                Flutterwave::configure([
                    'secret_key' => config('services.flutterwave.secret_key'),
                    'public_key' => config('services.flutterwave.public_key'),
                    'encryption_key' => config('services.flutterwave.encryption_key'),
                ]);
            } catch (\Exception $e) {
                // Log the error but don't break the application
                \Log::warning('Flutterwave configuration failed: ' . $e->getMessage());
            }
        }
    }
}
