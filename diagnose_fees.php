<?php
// Quick diagnostic script to check fee database

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\StudentTransaction;
use App\Models\CourseTermFee;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

echo "\n=== DIAGNOSTIC: Fee System Debug ===\n";

// Check 1: What transaction types exist?
echo "\n1. TRANSACTION TYPES IN student_transactions TABLE:\n";
$types = StudentTransaction::distinct('type')->pluck('type')->toArray();
echo "   Types found: " . json_encode($types) . "\n";
echo "   Count by type:\n";
foreach ($types as $type) {
    $count = StudentTransaction::where('type', $type)->count();
    $successCount = StudentTransaction::where('type', $type)->where('status', 'successful')->count();
    echo "     - '$type': $count total, $successCount successful\n";
}

// Check 2: Do course_term_fees exist?
echo "\n2. COURSE_TERM_FEES STATUS:\n";
$totalFees = CourseTermFee::where('is_active', true)->count();
echo "   Active fee records: $totalFees\n";
if ($totalFees > 0) {
    $sampleFee = CourseTermFee::where('is_active', true)->first();
    echo "   Sample fee record:\n";
    echo "     Course ID: {$sampleFee->course_id}\n";
    echo "     Term ID: {$sampleFee->term_id}\n";
    echo "     Registration: {$sampleFee->registration_fee} RWF\n";
    echo "     Tuition: {$sampleFee->tuition_fee} RWF\n";
    echo "     Practical: {$sampleFee->practical_fee} RWF\n";
    echo "     Trip: {$sampleFee->trip_fee} RWF\n";
    echo "     Card: {$sampleFee->student_card_fee} RWF\n";
    echo "     Total: " . ($sampleFee->registration_fee + $sampleFee->tuition_fee + $sampleFee->practical_fee + $sampleFee->trip_fee + $sampleFee->student_card_fee) . " RWF\n";
} else {
    echo "   ⚠️  NO ACTIVE FEE RECORDS!\n";
}

// Check 3: Sample student fees and payments
echo "\n3. SAMPLE STUDENT FEE CALCULATION:\n";
$student = Student::where('course_id', '!=', null)->first();
if ($student) {
    echo "   Student: {$student->full_name} (ID: {$student->id})\n";
    echo "   Course: {$student->course_id}, Year: {$student->academic_year_id}\n";
    
    // Get required fees
    $fees = CourseTermFee::where('course_id', $student->course_id)
        ->where('is_active', true)
        ->whereHas('term', function($q) use ($student) {
            $q->where('academic_year_id', $student->academic_year_id);
        })
        ->get();
    
    echo "   Fee records found: " . count($fees) . "\n";
    if (count($fees) > 0) {
        $totalRequired = 0;
        foreach ($fees as $fee) {
            $termTotal = ($fee->registration_fee ?? 0) + ($fee->tuition_fee ?? 0) + ($fee->practical_fee ?? 0) + ($fee->trip_fee ?? 0) + ($fee->student_card_fee ?? 0);
            $totalRequired += $termTotal;
        }
        echo "   Total fees required: " . number_format($totalRequired) . " RWF\n";
    }
    
    // Get payments (all types)
    $payments = StudentTransaction::where('student_id', $student->id)
        ->whereIn('type', ['registration', 'school_fee', 'practical_fee', 'trip_fee', 'student_card'])
        ->where('status', 'successful')
        ->get();
    
    $totalPaid = $payments->sum('amount');
    echo "   Successful payments (all types): " . count($payments) . "\n";
    echo "   Total paid: " . number_format($totalPaid) . " RWF\n";
    
    if (count($fees) > 0) {
        $outstanding = max(0, $totalRequired - $totalPaid);
        echo "   Outstanding: " . number_format($outstanding) . " RWF\n";
    }
    
    // Show payment breakdown by type
    if (count($payments) > 0) {
        echo "   Payments by type:\n";
        $typeGroups = $payments->groupBy('type');
        foreach ($typeGroups as $type => $typePayments) {
            $amount = $typePayments->sum('amount');
            echo "     - $type: " . number_format($amount) . " RWF (" . count($typePayments) . " transactions)\n";
        }
    }
} else {
    echo "   ⚠️  NO STUDENTS FOUND!\n";
}

// Check 4: Students in a specific course
echo "\n4. CHECKING FORM 1 STUDENTS (Course ID 20):\n";
$form1Students = Student::where('course_id', 20)->count();
echo "   Total Form 1 students: $form1Students\n";
if ($form1Students > 0) {
    $form1WithOutstanding = 0;
    $totalOutstandingAmount = 0;
    
    $students = Student::where('course_id', 20)->get();
    foreach ($students as $s) {
        // Check if has outstanding fees
        $fees = CourseTermFee::where('course_id', 20)
            ->where('is_active', true)
            ->get();
        
        if ($fees->count() > 0) {
            $required = $fees->sum(function($f) {
                return ($f->registration_fee ?? 0) + ($f->tuition_fee ?? 0) + ($f->practical_fee ?? 0) + ($f->trip_fee ?? 0) + ($f->student_card_fee ?? 0);
            });
            
            $paid = StudentTransaction::where('student_id', $s->id)
                ->whereIn('type', ['registration', 'school_fee', 'practical_fee', 'trip_fee', 'student_card'])
                ->where('status', 'successful')
                ->sum('amount');
            
            $outstanding = max(0, $required - $paid);
            if ($outstanding > 0) {
                $form1WithOutstanding++;
                $totalOutstandingAmount += $outstanding;
            }
        }
    }
    
    echo "   Students with outstanding fees: $form1WithOutstanding\n";
    echo "   Total outstanding amount: " . number_format($totalOutstandingAmount) . " RWF\n";
    if ($form1WithOutstanding === 0) {
        echo "   ⚠️  WARNING: No students with outstanding fees detected!\n";
    }
}

echo "\n=== END DIAGNOSTIC ===\n\n";
