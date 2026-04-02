<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Checking course_term_fees table structure...\n";

// Check if table exists
if (!Schema::hasTable('course_term_fees')) {
    echo "Table course_term_fees does not exist.\n";
    exit(1);
}

// Get current columns
$columns = DB::select('DESCRIBE course_term_fees');
echo "Current columns:\n";
foreach ($columns as $column) {
    echo "  - {$column->Field} ({$column->Type})\n";
}

// Check if is_active column exists
$hasIsActive = false;
foreach ($columns as $column) {
    if ($column->Field === 'is_active') {
        $hasIsActive = true;
        break;
    }
}

if (!$hasIsActive) {
    echo "\nAdding missing is_active column...\n";
    try {
        DB::statement('ALTER TABLE course_term_fees ADD COLUMN is_active BOOLEAN DEFAULT TRUE AFTER student_card_fee');
        echo "✓ is_active column added successfully\n";
    } catch (Exception $e) {
        echo "✗ Error adding is_active column: " . $e->getMessage() . "\n";
    }
} else {
    echo "\n✓ is_active column already exists\n";
}

// Check if notes column exists
$hasNotes = false;
foreach ($columns as $column) {
    if ($column->Field === 'notes') {
        $hasNotes = true;
        break;
    }
}

if (!$hasNotes) {
    echo "Adding missing notes column...\n";
    try {
        DB::statement('ALTER TABLE course_term_fees ADD COLUMN notes TEXT NULLABLE AFTER is_active');
        echo "✓ notes column added successfully\n";
    } catch (Exception $e) {
        echo "✗ Error adding notes column: " . $e->getMessage() . "\n";
    }
} else {
    echo "✓ notes column already exists\n";
}

// Verify the structure
echo "\nFinal table structure:\n";
$finalColumns = DB::select('DESCRIBE course_term_fees');
foreach ($finalColumns as $column) {
    echo "  - {$column->Field} ({$column->Type})\n";
}

echo "\nTesting query that was failing...\n";
try {
    $result = DB::select('SELECT * FROM course_term_fees WHERE course_id = 18 AND course_id IS NOT NULL AND is_active = 1 LIMIT 1');
    echo "✓ Query executed successfully\n";
    if ($result) {
        echo "✓ Found " . count($result) . " active records for course_id = 18\n";
    } else {
        echo "! No active records found for course_id = 18 (this might be expected)\n";
    }
} catch (Exception $e) {
    echo "✗ Query failed: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";
