<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\Student;

class StudentRegistrationService
{
    public function generateUniqueRegNumber($courseAbbreviation, $yearRange): string
    {
        $course = Course::where('abbreviation', $courseAbbreviation)->first();
        $academicYear = AcademicYear::where('year', $yearRange)->first();

        if (!$course || !$academicYear) {
            return 'ESA' . strtoupper($courseAbbreviation) . time() . $yearRange;
        }

        $prefix = 'ESA' . strtoupper($courseAbbreviation);
        $existingNumbers = Student::where('course_id', $course->id)
            ->where('academic_year_id', $academicYear->id)
            ->where('reg_number', 'like', $prefix . '%')
            ->pluck('reg_number')
            ->map(function ($regNumber) use ($prefix, $yearRange) {
                $pattern = '/^' . preg_quote($prefix, '/') . '(\d+)' . preg_quote($yearRange, '/') . '$/';
                return preg_match($pattern, $regNumber, $matches) ? (int) $matches[1] : 0;
            })
            ->filter()
            ->toArray();

        $nextNumber = empty($existingNumbers) ? 1 : max($existingNumbers) + 1;
        $regNumber = $prefix . str_pad($nextNumber, 2, '0', STR_PAD_LEFT) . $yearRange;

        while (Student::where('reg_number', $regNumber)->exists()) {
            $nextNumber++;
            $regNumber = $prefix . str_pad($nextNumber, 2, '0', STR_PAD_LEFT) . $yearRange;

            if ($nextNumber > 9999) {
                return $prefix . uniqid() . $yearRange;
            }
        }

        return $regNumber;
    }
}
