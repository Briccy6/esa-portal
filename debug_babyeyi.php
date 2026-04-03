<?php

// Debug script to help troubleshoot babyeyi visibility issues
// Place this anywhere accessible or use tinker

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Babyeyi;
use App\Models\Student;
use App\Models\CourseTermFee;
use Illuminate\Support\Facades\DB;

// Colors for CLI output
$colors = [
    'reset' => "\033[0m",
    'red' => "\033[91m",
    'green' => "\033[92m",
    'yellow' => "\033[93m",
    'blue' => "\033[94m",
    'header' => "\033[95m"
];

function print_header($text) {
    global $colors;
    echo "\n{$colors['header']}=== {$text} ==={$colors['reset']}\n";
}

function print_success($text) {
    global $colors;
    echo "{$colors['green']}✓ {$text}{$colors['reset']}\n";
}

function print_error($text) {
    global $colors;
    echo "{$colors['red']}✗ {$text}{$colors['reset']}\n";
}

function print_info($text) {
    global $colors;
    echo "{$colors['blue']}ℹ {$text}{$colors['reset']}\n";
}

// Start diagnostics
print_header("BABYEYI VISIBILITY DIAGNOSTIC");

// 1. Check published babyeyis
echo "\n{$colors['yellow']}1. Checking Published Babyeyis in Database:{$colors['reset']}\n";
$publishedCount = Babyeyi::where('status', 'published')->count();
$allCount = Babyeyi::count();

print_info("Total babyeyis: $allCount");
print_info("Published babyeyis: $publishedCount");

if ($publishedCount > 0) {
    print_success("Published babyeyis found!");
    
    $babyeyis = Babyeyi::where('status', 'published')
        ->with(['course', 'term', 'academicYear'])
        ->get();
    
    echo "\nPublished Babyeyis Details:\n";
    foreach ($babyeyis as $i => $b) {
        echo "  [{$i}] Course ID: {$b->course_id}, Term ID: {$b->term_id}, " .
             "Course: {$b->course->course_name ?? 'N/A'}, Term: {$b->term->name ?? 'N/A'}\n";
    }
} else {
    print_error("No published babyeyis found!");
    print_info("To publish: Go to Babyeyi Dashboard and click Publish");
}

// 2. Check students
echo "\n{$colors['yellow']}2. Checking Students:{$colors['reset']}\n";
$studentCount = Student::count();
print_info("Total students: $studentCount");

$studentsWithCourse = Student::whereNotNull('course_id')->with('course', 'branch')->get();
print_info("Students with course assigned: " . $studentsWithCourse->count());

if ($studentsWithCourse->count() > 0) {
    echo "\nStudents with Courses:\n";
    foreach ($studentsWithCourse->take(5) as $i => $s) {
        $branch = $s->branch ? $s->branch->name : 'No Branch';
        echo "  [{$i}] Name: {$s->full_name}, Course ID: {$s->course_id}, " .
             "Course: {$s->course->course_name ?? 'N/A'}, Branch: $branch\n";
    }
    if ($studentsWithCourse->count() > 5) {
        echo "  ... and " . ($studentsWithCourse->count() - 5) . " more\n";
    }
} else {
    print_error("No students with courses assigned!");
}

// 3. Check course term fees
echo "\n{$colors['yellow']}3. Checking Course Term Fees:{$colors['reset']}\n";
$feeCount = CourseTermFee::where('is_active', true)->count();
print_info("Active course term fees: $feeCount");

$fees = CourseTermFee::where('is_active', true)
    ->with(['course', 'term'])
    ->get();

if ($fees->count() > 0) {
    echo "\nActive Course Term Fees:\n";
    foreach ($fees->take(5) as $i => $f) {
        $total = $f->registration_fee + $f->tuition_fee + $f->practical_fee + 
                 $f->trip_fee + $f->student_card_fee;
        echo "  [{$i}] Course: {$f->course->course_name ?? 'N/A'}, " .
             "Term: {$f->term->name ?? 'N/A'}, Total: {$total}\n";
    }
    if ($fees->count() > 5) {
        echo "  ... and " . ($fees->count() - 5) . " more\n";
    }
}

// 4. Matching Analysis
echo "\n{$colors['yellow']}4. Matching Analysis (Babyeyi vs Student vs Fees):{$colors['reset']}\n";

if ($publishedCount > 0 && $studentsWithCourse->count() > 0) {
    $student = $studentsWithCourse->first();
    $matchingBabyeyis = Babyeyi::where('status', 'published')
        ->where('course_id', $student->course_id)
        ->get();
    
    echo "\nTesting with Student: {$student->full_name} (Course ID: {$student->course_id})\n";
    print_info("Matching babyeyis for this student: " . $matchingBabyeyis->count());
    
    foreach ($matchingBabyeyis as $b) {
        $fees = CourseTermFee::where('course_id', $student->course_id)
            ->where('term_id', $b->term_id)
            ->where('is_active', true)
            ->first();
        
        if ($fees) {
            print_success("Babyeyi has matching fees for student!");
        } else {
            print_error("Babyeyi exists but NO matching fees for student!");
        }
    }
}

print_header("DIAGNOSTIC COMPLETE");

echo "\n{$colors['yellow']}Recommendations:{$colors['reset']}\n";
if ($publishedCount == 0) {
    print_info("Generate and publish babyeyis using: Babyeyi → Bulk Generate → Select Term → Check Auto-Publish");
} elseif ($studentsWithCourse->count() == 0) {
    print_info("Assign courses to students first");
} else {
    print_success("Everything looks good! If still having issues, check:");
    echo "  - Student email matches database\n";
    echo "  - Course ID is set correctly\n";
    echo "  - CourseTermFees is configured for the course and term\n";
}

echo "\n";
