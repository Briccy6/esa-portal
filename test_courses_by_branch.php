<?php
/**
 * Test script to debug the "Failed to load courses" issue
 * Run this from the root of your Laravel project
 */

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\Branch;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

$app = require_once 'bootstrap/app.php';
$container = $app->make(\Illuminate\Contracts\Container\Container::class);

// Check 1: Are there any branches?
echo "=== Checking Branches ===\n";
$branches = Branch::all();
echo "Total branches: " . $branches->count() . "\n";
foreach ($branches as $branch) {
    echo "  - Branch ID: {$branch->id}, Name: {$branch->name}\n";
}

// Check 2: Are there any courses?
echo "\n=== Checking Courses ===\n";
$courses = Course::all();
echo "Total courses: " . $courses->count() . "\n";
foreach ($courses->take(5) as $course) {
    echo "  - Course ID: {$course->id}, Name: {$course->course_name}\n";
}

// Check 3: Check branch_course pivot table
echo "\n=== Checking branch_course Pivot Table ===\n";
$pivotData = DB::table('branch_course')->get();
echo "Total branch_course records: " . $pivotData->count() . "\n";
foreach ($pivotData->take(10) as $record) {
    echo "  - Branch ID: {$record->branch_id}, Course ID: {$record->course_id}\n";
}

// Check 4: For each branch, show associated courses
echo "\n=== Courses per Branch (using Eloquent) ===\n";
foreach ($branches as $branch) {
    $courseCount = $branch->courses()->count();
    echo "Branch '{$branch->name}' (ID: {$branch->id}): {$courseCount} courses\n";
    if ($courseCount > 0) {
        foreach ($branch->courses()->take(3)->get() as $course) {
            echo "  - {$course->course_name} (ID: {$course->id})\n";
        }
    }
}

// Check 5: Test the query that should be used by getCoursesByBranch
echo "\n=== Testing Query Methods (First Branch) ===\n";
if ($branches->count() > 0) {
    $testBranch = $branches->first();
    $branchId = $testBranch->id;
    
    echo "Testing with Branch ID: {$branchId}\n";
    
    // Method 1: belongsToMany with wherePivot
    echo "\nMethod 1: wherePivot("branch_id", {$branchId}):\n";
    $courses1 = Course::whereHas('branches', function($q) use ($branchId) {
        $q->wherePivot('branch_id', $branchId);
    })->get();
    echo "  Found " . $courses1->count() . " courses\n";
    
    // Method 2: Direct join
    echo "\nMethod 2: Direct join on branch_course table:\n";
    $courses2 = DB::table('courses')
        ->join('branch_course', 'courses.id', '=', 'branch_course.course_id')
        ->where('branch_course.branch_id', $branchId)
        ->select('courses.id', 'courses.course_name')
        ->distinct()
        ->get();
    echo "  Found " . $courses2->count() . " courses\n";
    
    // Method 3: Direct FK
    echo "\nMethod 3: Direct branch_id FK:\n";
    $courses3 = Course::where('branch_id', $branchId)->get();
    echo "  Found " . $courses3->count() . " courses\n";
}

echo "\n=== Test Complete ===\n";
