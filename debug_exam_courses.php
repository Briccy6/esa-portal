<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== DIAGNOSTIC: Exam Creation - Courses Issue ===\n\n";

// Check if courses exist
$coursesCount = \App\Models\Course::count();
echo "1. Total courses in database: {$coursesCount}\n";

if ($coursesCount > 0) {
    $courses = \App\Models\Course::all(['id', 'course_name', 'abbreviation']);
    echo "   Available courses:\n";
    foreach ($courses as $course) {
        echo "   - ID: {$course->id}, Name: {$course->course_name} ({$course->abbreviation})\n";
    }
} else {
    echo "   ❌ No courses found in database!\n";
}

// Check if branches exist
$branchesCount = \App\Models\Branch::count();
echo "\n2. Total branches in database: {$branchesCount}\n";

if ($branchesCount > 0) {
    $branches = \App\Models\Branch::all(['id', 'name']);
    echo "   Available branches:\n";
    foreach ($branches as $branch) {
        echo "   - ID: {$branch->id}, Name: {$branch->name}\n";
    }
} else {
    echo "   ❌ No branches found in database!\n";
}

// Check branch-course assignments
echo "\n3. Branch-Course assignments:\n";
$branchCourseCount = \Illuminate\Support\Facades\DB::table('branch_course')->count();
echo "   Total branch-course assignments: {$branchCourseCount}\n";

if ($branchCourseCount > 0) {
    $assignments = \Illuminate\Support\Facades\DB::table('branch_course')
        ->join('branches', 'branch_course.branch_id', '=', 'branches.id')
        ->join('courses', 'branch_course.course_id', '=', 'courses.id')
        ->select('branches.name as branch_name', 'courses.course_name', 'courses.abbreviation')
        ->get();
    
    echo "   Current assignments:\n";
    foreach ($assignments as $assignment) {
        echo "   - Branch: {$assignment->branch_name} → Course: {$assignment->course_name} ({$assignment->abbreviation})\n";
    }
} else {
    echo "   ❌ No branch-course assignments found!\n";
}

// Test the API endpoint that the form uses
echo "\n4. Testing API endpoint /api/branches/{branch}/courses:\n";
if ($branchesCount > 0) {
    $firstBranch = \App\Models\Branch::first();
    $coursesForBranch = $firstBranch->courses()->get(['courses.id', 'course_name']);
    
    echo "   Branch '{$firstBranch->name}' (ID: {$firstBranch->id}) has {$coursesForBranch->count()} courses:\n";
    foreach ($coursesForBranch as $course) {
        echo "   - ID: {$course->id}, Name: {$course->course_name}\n";
    }
}

// Recommendations
echo "\n=== RECOMMENDATIONS ===\n";
if ($coursesCount === 0) {
    echo "❌ Run the courses seeder: php artisan db:seed --class=CoursesTableSeeder\n";
}

if ($branchesCount === 0) {
    echo "❌ Create branches first\n";
}

if ($coursesCount > 0 && $branchesCount > 0 && $branchCourseCount === 0) {
    echo "❌ Assign courses to branches. The exam form requires branch-course assignments.\n";
    echo "   You can assign courses to branches using:\n";
    echo "   php artisan tinker\n";
    echo "   >>> \$branch = App\\Models\\Branch::first();\n";
    echo "   >>> \$courses = App\\Models\\Course::all();\n";
    echo "   >>> \$branch->courses()->attach(\$courses);\n";
}

if ($coursesCount > 0 && $branchesCount > 0 && $branchCourseCount > 0) {
    echo "✅ Everything looks good. The issue might be in the frontend or API route.\n";
}

echo "\n=== DIAGNOSTIC COMPLETE ===\n";
