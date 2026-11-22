<?php

namespace App\Support;

use App\Models\CourseTermBranchFee;
use App\Models\Student;
use App\Models\StudentOptionalFeeResponse;

class OptionalFeeBilling
{
    /**
     * Return practical and trip amounts that are mandatory or accepted by/assigned to the student.
     *
     * @return array{practical: float, trip: float}
     */
    public static function payableAmounts(Student $student, CourseTermBranchFee $fee): array
    {
        $acceptedTypes = StudentOptionalFeeResponse::query()
            ->where('student_id', $student->id)
            ->where('course_term_branch_fee_id', $fee->id)
            ->where('response', 'accepted')
            ->pluck('fee_type')
            ->all();

        return [
            'practical' => $fee->is_practical_mandatory || in_array('practical', $acceptedTypes, true)
                ? (float) $fee->practical_fee
                : 0.0,
            'trip' => $fee->is_trip_mandatory || in_array('trip', $acceptedTypes, true)
                ? (float) $fee->trip_fee
                : 0.0,
        ];
    }

    public static function amount(Student $student, CourseTermBranchFee $fee, string $feeType): float
    {
        if (!in_array($feeType, ['practical', 'trip'], true)) {
            throw new \InvalidArgumentException('Unsupported optional fee type.');
        }

        return self::payableAmounts($student, $fee)[$feeType];
    }
}
