<?php

// Check teachers table as well
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TEACHERS TABLE ANALYSIS ===\n\n";

try {
    // Get all teacher records
    $teachers = DB::table('teachers')->get();
    
    echo "Total teacher records: " . $teachers->count() . "\n\n";
    
    foreach ($teachers as $teacher) {
        echo "ID: {$teacher->id}, Name: {$teacher->teacher_name}\n";
        
        // Check for dependencies
        $dependencies = [];
        
        // Check exams
        $examCount = DB::table('exams')->where('teacher_id', $teacher->id)->count();
        if ($examCount > 0) {
            $dependencies[] = "Exams: {$examCount}";
        }
        
        // Check lessons
        $lessonCount = DB::table('lessons')->where('teacher_id', $teacher->id)->count();
        if ($lessonCount > 0) {
            $dependencies[] = "Lessons: {$lessonCount}";
        }
        
        // Check question banks
        $qbankCount = DB::table('question_banks')->where('teacher_id', $teacher->id)->count();
        if ($qbankCount > 0) {
            $dependencies[] = "Question Banks: {$qbankCount}";
        }
        
        // Check branch_teacher
        $branchTeacherCount = DB::table('branch_teacher')->where('teacher_id', $teacher->id)->count();
        if ($branchTeacherCount > 0) {
            $dependencies[] = "Branch Assignments: {$branchTeacherCount}";
        }
        
        // Check teacher_course
        $teacherCourseCount = DB::table('teacher_course')->where('teacher_id', $teacher->id)->count();
        if ($teacherCourseCount > 0) {
            $dependencies[] = "Course Assignments: {$teacherCourseCount}";
        }
        
        // Check resources
        $resourceCount = DB::table('resources')->where('teacher_id', $teacher->id)->count();
        if ($resourceCount > 0) {
            $dependencies[] = "Resources: {$resourceCount}";
        }
        
        // Check teacher_dailies
        $dailyCount = DB::table('teacher_dailies')->where('teacher_id', $teacher->id)->count();
        if ($dailyCount > 0) {
            $dependencies[] = "Daily Records: {$dailyCount}";
        }
        
        // Check live_classes
        $liveClassCount = DB::table('live_classes')->where('teacher_id', $teacher->id)->count();
        if ($liveClassCount > 0) {
            $dependencies[] = "Live Classes: {$liveClassCount}";
        }
        
        if (!empty($dependencies)) {
            echo "  DEPENDENCIES:\n";
            foreach ($dependencies as $dep) {
                echo "    - {$dep}\n";
            }
        } else {
            echo "  No dependencies found\n";
        }
        
        echo "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== ANALYSIS COMPLETE ===\n";
