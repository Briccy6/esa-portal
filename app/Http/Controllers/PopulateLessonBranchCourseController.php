<?php

namespace App\Http\Controllers;

use App\Models\LessonBranchCourse;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Teacher;

class PopulateLessonBranchCourseController extends Controller
{
    public function populate()
    {
        // Only allow in development
        if (!env('APP_DEBUG')) {
            return response()->json(['error' => 'Not allowed in production'], 403);
        }

        echo "=== Populating lesson_branch_course Table ===<br><br>";

        // Clear existing records
        echo "Clearing existing records...<br>";
        $cleared = LessonBranchCourse::count();
        LessonBranchCourse::truncate();
        echo "Cleared $cleared records<br><br>";

        // Get sample data
        $branches = Branch::limit(10)->get();
        $courses = Course::limit(10)->get();
        $lessons = Lesson::limit(50)->get();
        $teachers = Teacher::limit(10)->get();

        if ($branches->isEmpty() || $courses->isEmpty() || $lessons->isEmpty() || $teachers->isEmpty()) {
            return response()->json([
                'error' => 'Not enough data to populate',
                'data' => [
                    'branches' => $branches->count(),
                    'courses' => $courses->count(),
                    'lessons' => $lessons->count(),
                    'teachers' => $teachers->count(),
                ]
            ], 400);
        }

        $created = 0;
        $skipped = 0;
        $output = [];

        // Create mappings for each branch-course combination
        foreach ($branches as $branch) {
            foreach ($courses as $course) {
                // Get lessons for this course
                $courseLessons = Lesson::where('course_id', $course->id)->limit(3)->get();
                
                if ($courseLessons->isEmpty()) {
                    $output[] = "⚠ Skipping: No lessons found for course {$course->course_name}";
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
                            $output[] = "✓ Created: Branch '$branchName' + Course '{$course->course_name}' + Lesson '{$lesson->title}' + Teacher '{$teacher->teacher_name}'";
                        } else {
                            $skipped++;
                        }
                    } catch (\Exception $e) {
                        $output[] = "✗ Error: " . $e->getMessage();
                        $skipped++;
                    }
                }
            }
        }

        echo "<h2>Results</h2>";
        echo "<p>Created: <strong>$created</strong> records</p>";
        echo "<p>Skipped: <strong>$skipped</strong> records</p>";
        echo "<p>Total in table: <strong>" . LessonBranchCourse::count() . "</strong></p>";
        
        echo "<h2>Details</h2>";
        echo "<pre>";
        foreach ($output as $line) {
            echo $line . "\n";
        }
        echo "</pre>";

        echo "<h2>Branches Updated</h2>";
        echo "<ul>";
        foreach ($branches as $branch) {
            $branchName = $branch->branch_name ?? $branch->name;
            $count = LessonBranchCourse::where('branch_id', $branch->id)->count();
            echo "<li>$branchName: <strong>$count</strong> lesson-course mappings</li>";
        }
        echo "</ul>";
    }

    public function check()
    {
        $branches = Branch::all();
        $data = [];

        foreach ($branches as $branch) {
            $branchName = $branch->branch_name ?? $branch->name;
            $records = LessonBranchCourse::where('branch_id', $branch->id)
                ->with(['lesson', 'course', 'teacher'])
                ->get();
            
            $data[$branchName] = [
                'branch_id' => $branch->id,
                'record_count' => $records->count(),
                'records' => $records->map(fn($r) => [
                    'lesson' => $r->lesson?->title,
                    'course' => $r->course?->course_name,
                    'teacher' => $r->teacher?->teacher_name,
                ])->toArray()
            ];
        }

        return response()->json($data, 200);
    }
}
