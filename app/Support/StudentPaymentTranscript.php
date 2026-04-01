<?php

namespace App\Support;

use App\Models\SchoolInformation;
use App\Models\Student;
use App\Models\StudentTransaction;

class StudentPaymentTranscript
{
    public const STUDENT_CARD_FEE = 2000;

    /** @var array<int, array{label: string, type: string, expected: float, paid: float, balance: float}> */
    public static function feeRows(Student $student): array
    {
        $student->loadMissing(['course', 'branch', 'academicYear']);
        $course = $student->course;

        $registration = $course ? (float) $course->registration_fee : 0.0;
        $tuition        = $course ? (float) $course->tuition_fee : 0.0;
        $practical      = $course ? (float) $course->practical_fee : 0.0;
        $trip           = $course ? (float) $course->trip_fee : 0.0;
        $discountPct    = (float) ($student->discount ?? 0);

        $discountable = $tuition + $practical + $trip;
        $discountAmt  = ($discountPct > 0 && $discountable > 0)
            ? $discountable * ($discountPct / 100)
            : 0.0;

        $netTuition   = $discountable > 0 ? $tuition - ($tuition / $discountable * $discountAmt) : $tuition;
        $netPractical = $discountable > 0 ? $practical - ($practical / $discountable * $discountAmt) : $practical;
        $netTrip      = $discountable > 0 ? $trip - ($trip / $discountable * $discountAmt) : $trip;

        $types = [
            ['label' => 'Registration fee', 'type' => 'registration_fee', 'expected' => $registration],
            ['label' => 'Tuition (school fee)', 'type' => 'school_fee', 'expected' => $netTuition],
            ['label' => 'Practical fee', 'type' => 'practical_fee', 'expected' => $netPractical],
            ['label' => 'Trip fee', 'type' => 'trip_fee', 'expected' => $netTrip],
            ['label' => 'Student card', 'type' => 'student_card', 'expected' => (float) self::STUDENT_CARD_FEE],
        ];

        $rows = [];
        foreach ($types as $row) {
            $paid = self::paidSumForType($student->id, $row['type']);
            $exp  = round($row['expected'], 2);
            $pd   = round((float) $paid, 2);
            $bal  = round(max(0, $exp - $pd), 2);
            $rows[] = [
                'label'    => $row['label'],
                'type'     => $row['type'],
                'expected' => $exp,
                'paid'     => $pd,
                'balance'  => $bal,
            ];
        }

        return $rows;
    }

    public static function paidSumForType(int $studentId, string $type): float
    {
        return (float) StudentTransaction::query()
            ->where('student_id', $studentId)
            ->where('type', $type)
            ->whereIn('status', ['successful', 'paid', 'completed'])
            ->sum('amount');
    }

    public static function totalPaidRecorded(int $studentId): float
    {
        return (float) StudentTransaction::query()
            ->where('student_id', $studentId)
            ->whereIn('status', ['successful', 'paid', 'completed'])
            ->sum('amount');
    }

    /**
     * @return array{feeRows: array, totalExpected: float, totalPaid: float, totalOutstanding: float, discountPct: float, discountAmount: float, transactions: \Illuminate\Support\Collection, school: ?\App\Models\SchoolInformation}
     */
    public static function build(Student $student): array
    {
        $student->loadMissing(['course', 'branch', 'academicYear']);
        $feeRows = self::feeRows($student);

        $totalExpected      = round((float) collect($feeRows)->sum('expected'), 2);
        $totalPaidFeeTypes  = round((float) collect($feeRows)->sum('paid'), 2);
        $totalOutstanding   = round(max(0, $totalExpected - $totalPaidFeeTypes), 2);
        $totalPaidAllTypes  = round(self::totalPaidRecorded($student->id), 2);

        $discountPct = (float) ($student->discount ?? 0);
        $course      = $student->course;
        $discountable = $course
            ? (float) $course->tuition_fee + (float) $course->practical_fee + (float) $course->trip_fee
            : 0.0;
        $discountAmount = ($discountPct > 0 && $discountable > 0)
            ? round($discountable * ($discountPct / 100), 2)
            : 0.0;

        $transactions = StudentTransaction::query()
            ->where('student_id', $student->id)
            ->orderByDesc('paid_at')
            ->orderByDesc('created_at')
            ->get();

        return [
            'feeRows'            => $feeRows,
            'totalExpected'      => $totalExpected,
            'totalPaid'          => $totalPaidFeeTypes,
            'totalPaidAllTypes'  => $totalPaidAllTypes,
            'totalOutstanding'   => $totalOutstanding,
            'discountPct'        => $discountPct,
            'discountAmount'     => $discountAmount,
            'transactions'       => $transactions,
            'school'             => SchoolInformation::getActive(),
        ];
    }
}
