<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mark;
use App\Models\Student;

echo "=== Testing Registration Number Display ===\n\n";

// Get a sample mark to check student data
$sampleMark = Mark::with(['student', 'course', 'branch', 'lesson'])->first();

if ($sampleMark) {
    echo "Sample Mark Data:\n";
    echo "Mark ID: {$sampleMark->id}\n";
    echo "Student ID: {$sampleMark->student_id}\n";
    echo "Student Name: {$sampleMark->student->full_name}\n";
    echo "Student Reg Number: {$sampleMark->student->reg_number}\n";
    echo "Course: {$sampleMark->course->course_name}\n";
    echo "Branch: {$sampleMark->branch->name}\n";
    echo "Lesson: {$sampleMark->lesson->title}\n\n";
    
    // Test the data structure used in controllers
    $testStudentId = $sampleMark->student_id;
    $students[$testStudentId] = [
        'name' => $sampleMark->student->full_name,
        'course' => $sampleMark->course->course_name,
        'branch' => $sampleMark->branch->name,
        'reg_number' => $sampleMark->student->reg_number
    ];
    
    echo "Controller Data Structure:\n";
    echo "Student ID: $testStudentId\n";
    echo "Data: " . json_encode($students[$testStudentId], JSON_PRETTY_PRINT) . "\n\n";
    
    echo "Display Logic:\n";
    echo "Reg Number: {$students[$testStudentId]['reg_number']}\n";
    echo "Fallback to ID: " . ($students[$testStudentId]['reg_number'] ?? $testStudentId) . "\n";
    
} else {
    echo "No marks found in database.\n";
}

// Check all students to see reg numbers
echo "\nAll Students with Reg Numbers:\n";
$allStudents = Student::take(5)->get(['id', 'full_name', 'reg_number']);
foreach ($allStudents as $student) {
    echo "ID: {$student->id}, Name: {$student->full_name}, Reg No: {$student->reg_number}\n";
}

echo "\n=== Test Complete ===\n";
