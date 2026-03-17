<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Checking terms table structure...\n";
    $columns = \DB::select('DESCRIBE terms');
    
    echo "Columns found:\n";
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})" . ($column->Null === 'NO' ? ' NOT NULL' : '') . "\n";
    }
    
    echo "\nChecking if 'term' column exists...\n";
    $termColumn = collect($columns)->firstWhere('Field', 'term');
    if ($termColumn) {
        echo "Found 'term' column:\n";
        print_r($termColumn);
    } else {
        echo "No 'term' column found!\n";
    }
    
    echo "\nChecking if 'name' column exists...\n";
    $nameColumn = collect($columns)->firstWhere('Field', 'name');
    if ($nameColumn) {
        echo "Found 'name' column:\n";
        print_r($nameColumn);
    } else {
        echo "No 'name' column found!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
