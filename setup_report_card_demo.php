<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Get the currently logged-in student or use student ID 1
$studentId = 1; // You can modify this

// Check if student exists
$student = DB::table('students')->where('id', $studentId)->first();
if (!$student) {
    echo "Student ID {$studentId} not found!\n";
    exit(1);
}

echo "=== STUDENT: {$student->full_name} ===\n";

// Check if report cards exist for this student
$reportCards = DB::table('report_cards')->where('student_id', $studentId)->get();
echo "Report Cards: " . count($reportCards) . "\n";

// Get academic year and term for first report card if it exists
if ($reportCards->isNotEmpty()) {
    $firstCard = $reportCards->first();
    echo "First Report Card: Academic Year ID={$firstCard->academic_year_id}, Term ID={$firstCard->term_id}\n";
    
    // Get the actual academic year and term names
    $academicYear = DB::table('academic_years')->where('id', $firstCard->academic_year_id)->first();
    $term = DB::table('terms')->where('id', $firstCard->term_id)->first();
    
    echo "Academic Year Name: " . ($academicYear ? $academicYear->year : 'N/A') . "\n";
    echo "Term Name: " . ($term ? $term->name : 'N/A') . "\n";
    
    // Now check if settings exist for this combo
    $settings = DB::table('report_card_settings')
        ->where('academic_year', $academicYear->year ?? '')
        ->where('term', $term->name ?? '')
        ->where('is_active', true)
        ->first();
    
    if (!$settings) {
        echo "\n⚠️  No Report Card Settings configured for this academic year/term!\n";
        echo "\nCreating sample settings...\n";
        
        DB::table('report_card_settings')->insert([
            'academic_year' => $academicYear->year ?? '2026',
            'term' => $term->name ?? 'Term 1',
            'report_card_available_at' => Carbon::now()->addDays(2),
            'description' => 'Sample report card setting for testing',
            'is_active' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        
        echo "✓ Sample settings created!\n";
    } else {
        echo "\n✓ Report Card Settings found!\n";
        echo "Available at: " . $settings->report_card_available_at . "\n";
        echo "Active: " . ($settings->is_active ? 'Yes' : 'No') . "\n";
    }
} else {
    echo "\n⚠️  No report cards found for this student!\n";
    echo "\nYou need to create report cards through the admin panel first.\n";
}

echo "\n=== STUDENT FEES ===\n";
$outstanding = DB::table('students')->where('id', $studentId)->value('outstanding_amount') ?? 0;
echo "Outstanding Amount: RWF " . number_format($outstanding, 2) . "\n";

if ($outstanding > 0) {
    echo "\nClearing outstanding fees for testing...\n";
    DB::table('students')->where('id', $studentId)->update(['outstanding_amount' => 0]);
    echo "✓ Fees cleared!\n";
}

echo "\nDone!\n";
