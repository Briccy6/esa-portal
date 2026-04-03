<?php
// Load Laravel environment
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ReportCardSetting;
use App\Models\ReportCard;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

echo "=== REPORT CARDS DIAGNOSIS ===\n\n";

// 1. Check ReportCardSetting records
echo "1. ReportCardSetting Records:\n";
echo "   Config values: academic_year=" . config('app.academic_year', 'NOT SET') . ", term=" . config('app.current_term', 'NOT SET') . "\n\n";

$allSettings = ReportCardSetting::all();
echo "   Total settings found: " . $allSettings->count() . "\n";

if ($allSettings->isEmpty()) {
    echo "   ⚠️  NO RECORDS FOUND!\n";
} else {
    foreach ($allSettings as $setting) {
        echo "   - ID: {$setting->id}\n";
        echo "     Academic Year: {$setting->academic_year}\n";
        echo "     Term: {$setting->term}\n";
        echo "     Release Date: {$setting->report_card_available_at}\n";
        echo "     Is Active: " . ($setting->is_active ? "YES" : "NO") . "\n";
        echo "     Is Available: " . ($setting->isAvailable() ? "YES" : "NO") . "\n";
        echo "     Description: {$setting->description}\n\n";
    }
}

// 2. Try to get active setting
echo "2. Active Setting (using getActive() method):\n";
$activeSetting = ReportCardSetting::getActive();
if ($activeSetting) {
    echo "   ✓ Found: ID={$activeSetting->id}, Academic Year={$activeSetting->academic_year}, Term={$activeSetting->term}\n";
} else {
    echo "   ✗ NO ACTIVE SETTING FOUND\n";
}
echo "\n";

// 3. Check ReportCard records
echo "3. ReportCard Records:\n";
$allReportCards = ReportCard::all();
echo "   Total report cards: " . $allReportCards->count() . "\n";

if ($allReportCards->isEmpty()) {
    echo "   ⚠️  NO REPORT CARDS FOUND!\n";
} else {
    $byStatus = $allReportCards->groupBy('status');
    foreach ($byStatus as $status => $cards) {
        echo "   - Status '{$status}': " . $cards->count() . " cards\n";
    }
    echo "\n   Sample report cards:\n";
    foreach ($allReportCards->take(3) as $rc) {
        echo "   - ID: {$rc->id}, Student: {$rc->student_id}, Status: {$rc->status}, Created: {$rc->created_at}\n";
    }
}
echo "\n";

// 4. Check a student's outstanding fees
echo "4. Student Fee Status (checking first student):\n";
$student = Student::first();
if ($student) {
    echo "   Student ID: {$student->id}\n";
    echo "   Student Name: {$student->full_name}\n";
    echo "   Outstanding Fees: " . $student->getOutstandingFees() . "\n";
    echo "   Has Outstanding: " . ($student->hasOutstandingFees() ? "YES" : "NO") . "\n";
    
    // Check school fees
    $schoolFees = DB::table('school_fees')->where('student_id', $student->id)->get();
    echo "   School Fees records: " . $schoolFees->count() . "\n";
    if ($schoolFees->isNotEmpty()) {
        echo "      Total fees: " . $schoolFees->sum('amount') . "\n";
    }
    
    // Check transactions
    $transactions = DB::table('student_transactions')->where('student_id', $student->id)->get();
    echo "   Student Transactions: " . $transactions->count() . "\n";
    if ($transactions->isNotEmpty()) {
        $paid = $transactions->where('status', 'successful')->sum('amount');
        echo "      Paid (successful): " . $paid . "\n";
    }
    
    // Check course fees
    if ($student->course) {
        echo "   Course Fees:\n";
        echo "      Registration: " . ($student->course->registration_fee ?? 0) . "\n";
        echo "      Trip: " . ($student->course->trip_fee ?? 0) . "\n";
        echo "      Tuition: " . ($student->course->tuition_fee ?? 0) . "\n";
        echo "      Practical: " . ($student->course->pratical_fee ?? 0) . "\n";
        echo "      Discount: " . ($student->discount ?? 0) . "%\n";
    }
} else {
    echo "   ⚠️  NO STUDENTS FOUND!\n";
}
echo "\n";

// 5. Check database tables
echo "5. Database Table State:\n";
echo "   report_card_settings: " . DB::table('report_card_settings')->count() . " records\n";
echo "   report_cards: " . DB::table('report_cards')->count() . " records\n";
echo "   students: " . DB::table('students')->count() . " records\n";
echo "\n";

echo "=== END DIAGNOSIS ===\n";
