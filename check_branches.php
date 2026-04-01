<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Branches...\n";

$branches = DB::table('branches')->get();

echo "Total branches: " . $branches->count() . "\n";

if ($branches->count() > 0) {
    foreach ($branches as $branch) {
        echo "ID: {$branch->id}, Name: {$branch->name}\n";
    }
} else {
    echo "No branches found. Creating a default branch...\n";
    DB::table('branches')->insert([
        'name' => 'Main Branch',
        'location' => 'Kigali',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "Created default branch with ID: 1\n";
}
