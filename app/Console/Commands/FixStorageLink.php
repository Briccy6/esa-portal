<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixStorageLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:storage';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
public function handle()
{
    $storagePath = public_path('storage');

    $this->info('Removing existing storage link...');

    // Force delete symlink or folder
    if (is_link($storagePath) || file_exists($storagePath)) {
        if (!@unlink($storagePath)) {
            // If it's a directory, remove recursively
            exec(PHP_OS_FAMILY === 'Windows'
                ? 'rd /s /q "' . $storagePath . '"'
                : 'rm -rf "' . $storagePath . '"'
            );
        }
    }

    $this->info('Creating new storage link...');
    $this->call('storage:link');

    $this->info('✅ Storage link fixed successfully!');
}


}
