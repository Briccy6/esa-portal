<?php

namespace Tests\Unit;

use App\Models\Student;
use PHPUnit\Framework\TestCase;

class StudentProfileCompletionTest extends TestCase
{
    public function test_it_reports_missing_profile_fields(): void
    {
        $student = new Student();

        $this->assertSame(
            [
                'Full name',
                'Telephone number',
                'Email address',
                'Sex',
                'Profile photo',
                'Identification type',
                'Identification number',
                'Identification photo',
                'Education level',
            ],
            $student->missingProfileFields()
        );
        $this->assertFalse($student->hasCompletedProfile());
    }

    public function test_it_marks_a_fully_populated_profile_as_complete(): void
    {
        $student = new Student();
        foreach ([
            'full_name',
            'phone',
            'email',
            'sex',
            'passport_photo',
            'identification_type',
            'id_number',
            'id_photo',
            'education_level',
        ] as $field) {
            $student->setAttribute($field, 'provided');
        }

        $this->assertSame([], $student->missingProfileFields());
        $this->assertTrue($student->hasCompletedProfile());
    }

    public function test_it_uses_the_kigali_profile_completion_deadline(): void
    {
        $deadline = Student::profileCompletionDeadline();

        $this->assertSame('2026-11-22 12:00:00', $deadline->format('Y-m-d H:i:s'));
        $this->assertSame('Africa/Kigali', $deadline->getTimezone()->getName());
    }
}
