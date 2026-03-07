<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Mark;
use App\Models\Student;
use App\Models\Course;
use App\Models\AcademicYear;
use App\Models\Term;
use App\Models\ReportCard;

echo "Testing Report Card System\n";
echo "========================\n";

// Check existing data
echo "Total marks in system: " . Mark::count() . "\n";
echo "Total students: " . Student::count() . "\n";
echo "Total courses: " . Course::count() . "\n";
echo "Total academic years: " . AcademicYear::count() . "\n";
echo "Total terms: " . Term::count() . "\n";

// Get sample data
if (Student::count() > 0 && Course::count() > 0) {
    $student = Student::first();
    $course = Course::first();
    $academicYear = AcademicYear::first();
    $term = Term::first();
    
    echo "\nSample Data:\n";
    echo "Student: " . $student->full_name . " (ID: " . $student->id . ")\n";
    echo "Course: " . $course->course_name . " (ID: " . $course->id . ")\n";
    echo "Academic Year: " . $academicYear->year . " (ID: " . $academicYear->id . ")\n";
    echo "Term: " . $term->term . " (ID: " . $term->id . ")\n";
    
    // Check marks for this student
    $studentMarks = Mark::where('student_id', $student->id)->get();
    echo "Marks for this student: " . $studentMarks->count() . "\n";
    
    if ($studentMarks->count() > 0) {
        foreach ($studentMarks as $mark) {
            echo "- Mark: " . $mark->value . " (Course: " . $mark->course_id . ", Exam: " . $mark->exam_id . ")\n";
        }
    }
} else {
    echo "\nNo students or courses found. Please create some test data first.\n";
}

echo "\nReport Cards created: " . ReportCard::count() . "\n";

echo "\nTest completed!\n";
