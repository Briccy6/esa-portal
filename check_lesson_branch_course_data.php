<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\LessonBranchCourse;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Teacher;

echo "=== Checking lesson_branch_course data ===\n\n";

// Check total records
$totalRecords = LessonBranchCourse::count();
echo "Total lesson_branch_course records: $totalRecords\n\n";

// Check branches
$branches = Branch::all();
echo "Available Branches:\n";
foreach ($branches as $branch) {
    $branchName = $branch->branch_name ?? $branch->name;
    $count = LessonBranchCourse::where('branch_id', $branch->id)->count();
    echo "  - ID: {$branch->id}, Name: $branchName, LBC records: $count\n";
}

echo "\n\nAvailable Courses:\n";
$courses = Course::limit(10)->get();
foreach ($courses as $course) {
    echo "  - ID: {$course->id}, Name: {$course->course_name}\n";
}

echo "\n\nAvailable Lessons (first 10):\n";
$lessons = Lesson::limit(10)->get();
foreach ($lessons as $lesson) {
    echo "  - ID: {$lesson->id}, Title: {$lesson->title}\n";
}

echo "\n\nAvailable Teachers (first 10):\n";
$teachers = Teacher::limit(10)->get();
foreach ($teachers as $teacher) {
    echo "  - ID: {$teacher->id}, Name: {$teacher->first_name} {$teacher->last_name}\n";
}

echo "\n\nChecking for 'Kigali' branch specifically:\n";
$kigali = Branch::where('branch_name', 'Kigali')
    ->orWhere('name', 'Kigali')
    ->first();

if ($kigali) {
    echo "Found Kigali: ID = {$kigali->id}\n";
    $kigaliRecords = LessonBranchCourse::where('branch_id', $kigali->id)->with(['lesson', 'course', 'teacher'])->get();
    echo "lesson_branch_course records for Kigali: " . count($kigaliRecords) . "\n";
    
    if (count($kigaliRecords) > 0) {
        echo "\nRecords:\n";
        foreach ($kigaliRecords as $record) {
            echo "  - Lesson: {$record->lesson?->title}, Course: {$record->course?->course_name}, Teacher: {$record->teacher?->first_name}\n";
        }
    }
} else {
    echo "Kigali branch not found\n";
}

echo "\n\n=== SOLUTION ===\n";
if ($totalRecords == 0) {
    echo "The lesson_branch_course table is EMPTY!\n";
    echo "You need to populate it with lesson-course-branch mappings.\n";
    echo "Use the populate_lesson_branch_course.php script to add sample data.\n";
} else if ($kigali && LessonBranchCourse::where('branch_id', $kigali->id)->count() == 0) {
    echo "The Kigali branch has no lesson_branch_course records.\n";
    echo "Use the populate_lesson_branch_course.php script to add data for Kigali.\n";
}
?>
