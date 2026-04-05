<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\LessonBranchCourse;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;

echo "=== Populating lesson_branch_course Table ===\n\n";

// Clear existing records
echo "Clearing existing records...\n";
LessonBranchCourse::truncate();

// Get sample data
$branches = Branch::limit(5)->get();
$courses = Course::limit(5)->get();
$lessons = Lesson::limit(10)->get();
$teachers = Teacher::limit(5)->get();

if ($branches->isEmpty() || $courses->isEmpty() || $lessons->isEmpty() || $teachers->isEmpty()) {
    echo "ERROR: Not enough data to populate. Existing data:\n";
    echo "  Branches: " . Branch::count() . "\n";
    echo "  Courses: " . Course::count() . "\n";
    echo "  Lessons: " . Lesson::count() . "\n";
    echo "  Teachers: " . Teacher::count() . "\n";
    exit(1);
}

$created = 0;
$skipped = 0;

// Create mappings for each branch-course combination
foreach ($branches as $branch) {
    foreach ($courses as $course) {
        // Get lessons for this course
        $courseLessons = Lesson::where('course_id', $course->id)->limit(3)->get();
        
        if ($courseLessons->isEmpty()) {
            echo "  Skipping: No lessons found for course {$course->course_name}\n";
            $skipped++;
            continue;
        }
        
        foreach ($courseLessons as $lesson) {
            // Get a random teacher
            $teacher = $teachers->random();
            
            try {
                // Check if record already exists
                $exists = LessonBranchCourse::where([
                    'lesson_id' => $lesson->id,
                    'branch_id' => $branch->id,
                    'course_id' => $course->id,
                ])->exists();
                
                if (!$exists) {
                    LessonBranchCourse::create([
                        'lesson_id' => $lesson->id,
                        'branch_id' => $branch->id,
                        'course_id' => $course->id,
                        'teacher_id' => $teacher->id,
                    ]);
                    $created++;
                    $branchName = $branch->branch_name ?? $branch->name;
                    echo "✓ Created: Branch '{$branchName}' + Course '{$course->course_name}' + Lesson '{$lesson->title}' + Teacher '{$teacher->first_name}'\n";
                } else {
                    $skipped++;
                }
            } catch (\Exception $e) {
                echo "✗ Error creating record: " . $e->getMessage() . "\n";
                $skipped++;
            }
        }
    }
}

echo "\n=== Results ===\n";
echo "Created: $created records\n";
echo "Skipped: $skipped records\n";
echo "Total in table: " . LessonBranchCourse::count() . "\n";

echo "\n=== Sample Records ===\n";
$samples = LessonBranchCourse::with(['lesson', 'course', 'branch', 'teacher'])->limit(5)->get();
foreach ($samples as $sample) {
    echo "- Branch: {$sample->branch?->branch_name}, Course: {$sample->course?->course_name}, Lesson: {$sample->lesson?->title}, Teacher: {$sample->teacher?->first_name}\n";
}
?>
