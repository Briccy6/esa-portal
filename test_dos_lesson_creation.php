<?php

// Test script to verify DOS lesson creation functionality
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DOS Lesson Creation Test ===\n\n";

try {
    // Test 1: Check if branches exist
    echo "1. Testing Branch Data:\n";
    $branches = \App\Models\Branch::all();
    echo "   Found {$branches->count()} branches\n";
    foreach ($branches as $branch) {
        echo "   - {$branch->name} (ID: {$branch->id})\n";
    }
    
    // Test 2: Check if courses exist
    echo "\n2. Testing Course Data:\n";
    $courses = \App\Models\Course::with('branches')->limit(5)->get();
    echo "   Found {$courses->count()} courses (showing first 5)\n";
    foreach ($courses as $course) {
        $branchNames = $course->branches->pluck('name')->implode(', ');
        echo "   - {$course->course_name} (Branches: {$branchNames})\n";
    }
    
    // Test 3: Test the getCoursesForBranches method
    echo "\n3. Testing getCoursesForBranches Method:\n";
    $controller = new \App\Http\Controllers\LessonController();
    
    // Test with first branch if available
    if ($branches->count() > 0) {
        $request = new Request();
        $request->merge(['branch_ids' => [$branches->first()->id]]);
        
        $response = $controller->getCoursesForBranches($request);
        $data = $response->getData();
        
        echo "   Response Status: " . $response->getStatusCode() . "\n";
        echo "   Courses for branch '{$branches->first()->name}': " . count($data->courses) . "\n";
        
        foreach ($data->courses as $course) {
            echo "   - {$course->course_name} ({$course->abbreviation})\n";
        }
    } else {
        echo "   No branches found to test with\n";
    }
    
    // Test 4: Check if LessonBranchCourse model exists
    echo "\n4. Testing LessonBranchCourse Model:\n";
    if (class_exists('\App\Models\LessonBranchCourse')) {
        echo "   ✓ LessonBranchCourse model exists\n";
    } else {
        echo "   ✗ LessonBranchCourse model missing\n";
    }
    
    echo "\n=== Test Complete ===\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
