#!/usr/bin/env php
<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ReportCardSetting;
use App\Models\ReportCard;
use App\Models\Student;

echo "=== REPORT CARDS DATABASE DIAGNOSIS ===\n\n";

// 1. Check config values
echo "1. APP CONFIGURATION:\n";
$academicYear = config('app.academic_year');
$currentTerm = config('app.current_term');
echo "   Academic Year: " . ($academicYear ?: "NOT SET") . "\n";
echo "   Current Term: " . ($currentTerm ?: "NOT SET") . "\n\n";

// 2. Check all ReportCardSetting records
echo "2. ALL REPORT CARD SETTINGS:\n";
try {
    $allSettings = ReportCardSetting::all();
    echo "   Total records: " . $allSettings->count() . "\n";
    
    if ($allSettings->isEmpty()) {
        echo "   ⚠️  NO SETTINGS FOUND IN DATABASE\n";
    } else {
        foreach ($allSettings as $setting) {
            echo "\n   Record ID: {$setting->id}\n";
            echo "   - Academic Year: {$setting->academic_year}\n";
            echo "   - Term: {$setting->term}\n";
            echo "   - Release Date: {$setting->report_card_available_at}\n";
            echo "   - Active: " . ($setting->is_active ? "YES" : "NO") . "\n";
            echo "   - Description: " . ($setting->description ?: "N/A") . "\n";
            
            // Check if available
            if ($setting->report_card_available_at) {
                echo "   - Available: " . ($setting->isAvailable() ? "YES" : "NO") . "\n";
                echo "   - Time Remaining: " . ($setting->getTimeRemaining() ?: "AVAILABLE NOW") . "\n";
            }
        }
    }
    echo "\n";
} catch (Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n\n";
}

// 3. Check if getActive() finds anything
echo "3. ACTIVE SETTING (using getActive() method):\n";
try {
    $activeSetting = ReportCardSetting::getActive();
    if ($activeSetting) {
        echo "   ✓ FOUND: ID {$activeSetting->id}\n";
        echo "     - Academic Year: {$activeSetting->academic_year}\n";
        echo "     - Term: {$activeSetting->term}\n";
        echo "     - Is Active: YES\n";
        echo "     - Release Date: {$activeSetting->report_card_available_at}\n";
    } else {
        echo "   ✗ NOT FOUND\n";
        if (!$academicYear && !$currentTerm) {
            echo "     Reason: App configuration not set\n";
        } else {
            echo "     Reason: No matching record for academic_year='{$academicYear}' and term='{$currentTerm}'\n";
        }
    }
    echo "\n";
} catch (Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n\n";
}

// 4. Check ReportCard records
echo "4. REPORT CARDS:\n";
try {
    $totalCards = ReportCard::count();
    echo "   Total records: " . $totalCards . "\n";
    
    if ($totalCards === 0) {
        echo "   ⚠️  NO REPORT CARDS IN DATABASE\n";
    } else {
        $byStatus = ReportCard::select('status', \DB::raw('COUNT(*) as count'))->groupBy('status')->get();
        foreach ($byStatus as $row) {
            echo "   - Status '{$row->status}': {$row->count} cards\n";
        }
    }
    echo "\n";
} catch (Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n\n";
}

// 5. Check first student's fee status
echo "5. STUDENT FEE STATUS (first student):\n";
try {
    $student = Student::first();
    if ($student) {
        echo "   Student ID: {$student->id}\n";
        echo "   Name: {$student->full_name}\n";
        
        $outstanding = $student->getOutstandingFees();
        echo "   Outstanding Fees: " . number_format($outstanding, 2) . " RWF\n";
        echo "   Has Outstanding: " . ($outstanding > 0 ? "YES" : "NO") . "\n";
        
        if ($student->course) {
            echo "\n   Course Fees:\n";
            echo "   - Registration: " . number_format($student->course->registration_fee ?? 0, 2) . "\n";
            echo "   - Trip: " . number_format($student->course->trip_fee ?? 0, 2) . "\n";
            echo "   - Tuition: " . number_format($student->course->tuition_fee ?? 0, 2) . "\n";
            echo "   - Practical: " . number_format($student->course->pratical_fee ?? 0, 2) . "\n";
      echo "   - Discount: " . ($student->discount ?? 0) . "%\n";
        }
        
        // Check school fees
        $schoolFees = $student->schoolFees->sum('amount');
        echo "\n   School Fees Total: " . number_format($schoolFees, 2) . " RWF\n";
        
        // Check transactions
        $transactions = $student->transactions()->where('status', 'successful')->sum('amount');
        echo "   Paid Amount: " . number_format($transactions, 2) . " RWF\n";
    } else {
        echo "   ⚠️  NO STUDENTS IN DATABASE\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n\n";
}

echo "=== END DIAGNOSIS ===\n";
