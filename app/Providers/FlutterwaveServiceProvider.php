<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Flutterwave\Config\ForkConfig;
use Flutterwave\Flutterwave;

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
                $config = ForkConfig::setUp(
                    config('services.flutterwave.secret_key'),
                    config('services.flutterwave.public_key'),
                    config('services.flutterwave.encryption_key'),
                    'staging' // Use staging environment
                );
                
                Flutterwave::bootstrap($config);
                
                \Log::info('Flutterwave configured successfully');
                
            } catch (\Exception $e) {
                // Log the error but don't break the application
                \Log::warning('Flutterwave configuration failed: ' . $e->getMessage());
            }
        }
    }
}
