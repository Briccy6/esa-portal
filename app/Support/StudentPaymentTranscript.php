<?php

namespace App\Support;

use App\Models\SchoolInformation;
use App\Models\Student;
use App\Models\StudentTransaction;
use App\Models\Term;

class StudentPaymentTranscript
{
    public const STUDENT_CARD_FEE = 2000;

    /**
     * Group fee payments by type and term for detailed breakdown
     */
    public static function feeRowsWithTerms(Student $student): array
    {
        $student->loadMissing(['course', 'branch', 'academicYear']);
        $course = $student->course;

        // Get term-specific fees or fallback to legacy fees
        $currentTerm = Term::where('start_date', '<=', now())
                           ->where('end_date', '>=', now())
                           ->first();
        
        $termFees = null;
        if ($currentTerm) {
            $termFees = $course->getFeesForTerm($currentTerm->id);
        }
        
        if (!$termFees) {
            // Fallback to legacy fee structure
            $termFees = $course->getLegacyFees();
        }

        $registration = $termFees->registration_fee ?? 0;
        $tuition        = $termFees->tuition_fee ?? 0;
        $practical      = $termFees->practical_fee ?? 0;
        $trip           = $termFees->trip_fee ?? 0;
        $discountPct    = (float) ($student->discount ?? 0);

        $discountable = $tuition + $practical + $trip;
        $discountAmt  = ($discountPct > 0 && $discountable > 0)
            ? $discountable * ($discountPct / 100)
            : 0.0;

        $netTuition   = $discountable > 0 ? $tuition - ($tuition / $discountable * $discountAmt) : $tuition;
        $netPractical = $discountable > 0 ? $practical - ($practical / $discountable * $discountAmt) : $practical;
        $netTrip      = $discountable > 0 ? $trip - ($trip / $discountable * $discountAmt) : $trip;

        $feeTypes = [
            ['label' => 'Registration fee', 'type' => 'registration', 'expected' => $registration],
            ['label' => 'Tuition (school fee)', 'type' => 'school_fee', 'expected' => $netTuition],
            ['label' => 'Practical fee', 'type' => 'practical_fee', 'expected' => $netPractical],
            ['label' => 'Trip fee', 'type' => 'trip_fee', 'expected' => $netTrip],
            ['label' => 'Student card', 'type' => 'student_card', 'expected' => (float) self::STUDENT_CARD_FEE],
        ];

        $rows = [];
        
        // Get all transactions grouped by type and term
        foreach ($feeTypes as $feeType) {
            $transactions = StudentTransaction::query()
                ->where('student_id', $student->id)
                ->where('type', $feeType['type'])
                ->with('term')
                ->whereIn('status', ['successful', 'paid', 'completed'])
                ->orderBy('paid_at')
                ->get();

            if ($transactions->isEmpty()) {
                // No transactions for this fee type, show expected amount with no term
                $rows[] = [
                    'label'    => $feeType['label'],
                    'type'     => $feeType['type'],
                    'expected' => round($feeType['expected'], 2),
                    'paid'     => 0.00,
                    'balance'  => round($feeType['expected'], 2),
                    'term_name' => 'No Term',
                    'term'     => null,
                ];
            } else {
                // Group transactions by term for this fee type
                $termGroups = [];
                foreach ($transactions as $transaction) {
                    $term = $transaction->term;
                    $termKey = $term ? $term->id : 'no-term';
                    $termName = $term ? $term->name : 'No Term';
                    
                    if (!isset($termGroups[$termKey])) {
                        $termGroups[$termKey] = [
                            'term' => $term,
                            'term_name' => $termName,
                            'paid' => 0,
                            'count' => 0
                        ];
                    }
                    
                    $termGroups[$termKey]['paid'] += $transaction->amount;
                    $termGroups[$termKey]['count']++;
                }
                
                // Create a row for each term group
                foreach ($termGroups as $termGroup) {
                    $paid = round($termGroup['paid'], 2);
                    $balance = round(max(0, $feeType['expected'] - $paid), 2);
                    
                    $label = $feeType['label'];
                    if ($termGroup['count'] > 1) {
                        $label .= ' (' . $termGroup['count'] . ' payments)';
                    }
                    
                    $rows[] = [
                        'label'    => $label,
                        'type'     => $feeType['type'],
                        'expected' => round($feeType['expected'], 2),
                        'paid'     => $paid,
                        'balance'  => $balance,
                        'term_name' => $termGroup['term_name'],
                        'term'     => $termGroup['term'],
                    ];
                }
            }
        }

        return $rows;
    }

    /** @var array<int, array{label: string, type: string, expected: float, paid: float, balance: float}> */
    public static function feeRows(Student $student): array
    {
        $student->loadMissing(['course', 'branch', 'academicYear']);
        $course = $student->course;

        // Get term-specific fees or fallback to legacy fees
        $currentTerm = Term::where('start_date', '<=', now())
                           ->where('end_date', '>=', now())
                           ->first();
        
        $termFees = null;
        if ($currentTerm) {
            $termFees = $course->getFeesForTerm($currentTerm->id);
        }
        
        if (!$termFees) {
            // Fallback to legacy fee structure
            $termFees = $course->getLegacyFees();
        }

        $registration = $termFees->registration_fee ?? 0;
        $tuition        = $termFees->tuition_fee ?? 0;
        $practical      = $termFees->practical_fee ?? 0;
        $trip           = $termFees->trip_fee ?? 0;
        $discountPct    = (float) ($student->discount ?? 0);

        $discountable = $tuition + $practical + $trip;
        $discountAmt  = ($discountPct > 0 && $discountable > 0)
            ? $discountable * ($discountPct / 100)
            : 0.0;

        $netTuition   = $discountable > 0 ? $tuition - ($tuition / $discountable * $discountAmt) : $tuition;
        $netPractical = $discountable > 0 ? $practical - ($practical / $discountable * $discountAmt) : $practical;
        $netTrip      = $discountable > 0 ? $trip - ($trip / $discountable * $discountAmt) : $trip;

        $types = [
            ['label' => 'Registration fee', 'type' => 'registration', 'expected' => $registration],
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
     * Group transactions by term for better transcript organization
     */
    public static function groupTransactionsByTerm(Student $student): array
    {
        $transactions = StudentTransaction::query()
            ->where('student_id', $student->id)
            ->with('term')
            ->orderByDesc('paid_at')
            ->orderByDesc('created_at')
            ->get();

        $grouped = [];
        foreach ($transactions as $transaction) {
            $term = $transaction->term;
            $termKey = $term ? $term->id : 'no-term';
            $termName = $term ? $term->name : 'No Term';
            
            if (!isset($grouped[$termKey])) {
                $grouped[$termKey] = [
                    'term' => $term,
                    'term_name' => $termName,
                    'transactions' => [],
                    'total_paid' => 0
                ];
            }
            
            $grouped[$termKey]['transactions'][] = $transaction;
            if (in_array($transaction->status, ['successful', 'paid', 'completed'])) {
                $grouped[$termKey]['total_paid'] += $transaction->amount;
            }
        }

        return $grouped;
    }

    /**
     * @return array{feeRows: array, feeRowsWithTerms: array, totalExpected: float, totalPaid: float, totalOutstanding: float, discountPct: float, discountAmount: float, transactions: \Illuminate\Support\Collection, school: ?\App\Models\SchoolInformation, termGroups: array}
     */
    public static function build(Student $student): array
    {
        $student->loadMissing(['course', 'branch', 'academicYear']);
        $feeRows = self::feeRows($student);
        $feeRowsWithTerms = self::feeRowsWithTerms($student);

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
            ->with('term')
            ->orderByDesc('paid_at')
            ->orderByDesc('created_at')
            ->get();

        // Group transactions by term
        $termGroups = self::groupTransactionsByTerm($student);

        return [
            'student'            => $student,
            'feeRows'            => $feeRows,
            'feeRowsWithTerms'   => $feeRowsWithTerms,
            'totalExpected'      => $totalExpected,
            'totalPaid'          => $totalPaidFeeTypes,
            'totalPaidAllTypes'  => $totalPaidAllTypes,
            'totalOutstanding'   => $totalOutstanding,
            'discountPct'        => $discountPct,
            'discountAmount'     => $discountAmount,
            'transactions'       => $transactions,
            'termGroups'         => $termGroups,
            'school'             => SchoolInformation::getActive(),
        ];
    }
}
