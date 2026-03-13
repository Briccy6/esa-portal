<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "Checking school_informations table structure...\n";

// Check if table exists
if (!Schema::hasTable('school_informations')) {
    echo "ERROR: school_informations table does not exist!\n";
    exit(1);
}

echo "Table exists. Checking columns...\n";

$columns = Schema::getColumnListing('school_informations');
echo "Columns: " . implode(', ', $columns) . "\n";

$requiredColumns = ['logo', 'principal_signature_data', 'stamp_signature_data'];

foreach ($requiredColumns as $column) {
    if (in_array($column, $columns)) {
        echo "✓ Column '$column' exists\n";
    } else {
        echo "✗ Column '$column' missing\n";
    }
}

// Test basic model functionality
try {
    $schoolInfo = \App\Models\SchoolInformation::getActive();
    if ($schoolInfo) {
        echo "✓ Model can retrieve active school information\n";
    } else {
        echo "- No active school information found (this is OK for first time)\n";
    }
} catch (Exception $e) {
    echo "✗ Model error: " . $e->getMessage() . "\n";
}

echo "Test completed.\n";
