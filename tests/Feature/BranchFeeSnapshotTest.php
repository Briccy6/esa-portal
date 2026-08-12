<?php

use App\Models\AcademicYear;
use App\Models\Branch;
use App\Models\Course;
use App\Models\CourseTermBranchFee;
use App\Models\Student;
use App\Models\Term;

it('prefers branch-specific pivot fee data when showing admin fee breakdown', function () {
    $academicYear = AcademicYear::create([
        'year' => '2026-2027',
        'starting_date' => '2026-01-01',
        'expiry_date' => '2027-12-31',
        'is_ongoing' => true,
    ]);

    $branch = Branch::create([
        'name' => 'Kigali Branch',
        'location' => 'Kigali',
    ]);

    $course = Course::create([
        'course_name' => 'Computer Science',
        'abbreviation' => 'CS',
        'course_type' => 'Science',
        'course_code' => 'CS-101',
        'registration_fee' => 5000,
        'tuition_fee' => 100000,
        'practical_fee' => 30000,
        'trip_fee' => 15000,
    ]);

    $term = Term::create([
        'name' => 'Term 1',
        'academic_year_id' => $academicYear->id,
        'start_date' => '2026-01-01',
        'end_date' => '2026-03-31',
        'is_active' => true,
    ]);

    CourseTermBranchFee::create([
        'course_id' => $course->id,
        'term_id' => $term->id,
        'branch_id' => $branch->id,
        'registration_fee' => 3000,
        'tuition_fee' => 80000,
        'practical_fee' => 25000,
        'trip_fee' => 12000,
        'student_card_fee' => 2000,
        'is_active' => true,
    ]);

    $snapshot = $course->getFeeSnapshotForBranchAndAcademicYear($branch->id, $academicYear->id);

    expect($snapshot->registration_fee)->toBe(3000.0)
        ->and($snapshot->tuition_fee)->toBe(80000.0)
        ->and($snapshot->practical_fee)->toBe(25000.0)
        ->and($snapshot->trip_fee)->toBe(12000.0)
        ->and($snapshot->source)->toBe('branch_pivot');

    Student::create([
        'full_name' => 'Jane Doe',
        'sex' => 'Female',
        'email' => 'jane@example.com',
        'phone' => '0780000000',
        'branch_id' => $branch->id,
        'course_id' => $course->id,
        'reg_number' => 'REG-001',
        'password' => bcrypt('password'),
        'academic_year_id' => $academicYear->id,
    ]);
});
