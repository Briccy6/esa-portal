<?php

// Simple staff diagnostic script
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== STAFF DELETION ISSUE DIAGNOSTIC ===\n\n";

try {
    // Get all staff records
    $staff = \App\Models\Staff::with('branches')->get();
    
    echo "Total staff records: " . $staff->count() . "\n\n";
    
    foreach ($staff as $s) {
        echo "ID: {$s->id}, Name: {$s->name}, Role: {$s->role}\n";
        echo "  Branches: " . $s->branches->count() . "\n";
        
        // Check for any potential dependencies
        $branchCount = DB::table('staff_branch')->where('staff_id', $s->id)->count();
        echo "  Branch assignments: {$branchCount}\n";
        
        echo "\n";
    }

    // Check if there are any specific staff members that might be causing issues
    echo "\n=== TESTING DELETION ===\n";
    
    // Try to find a staff member with no dependencies
    $testStaff = \App\Models\Staff::whereDoesntHave('branches')->first();
    
    if ($testStaff) {
        echo "Found staff with no branch assignments: ID {$testStaff->id} - {$testStaff->name}\n";
        echo "Attempting deletion...\n";
        
        try {
            $deleted = $testStaff->delete();
            echo "Deletion result: " . ($deleted ? "SUCCESS" : "FAILED") . "\n";
        } catch (Exception $e) {
            echo "Deletion failed: " . $e->getMessage() . "\n";
        }
    } else {
        echo "No staff members without branch assignments found.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== DIAGNOSTIC COMPLETE ===\n";
