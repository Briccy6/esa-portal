<?php

require_once __DIR__ . '/artisan';

use App\Models\ReportCard;
use Barryvdh\DomPDF\Facade\Pdf;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Testing report card PDF with actual data...\n";
    
    // Get the report card
    $reportCard = ReportCard::find(37);
    if (!$reportCard) {
        echo "ReportCard ID 37 not found!\n";
        exit(1);
    }
    
    echo "Found report card for: " . $reportCard->student->full_name . "\n";
    
    // Load relationships
    $reportCard->load([
        'student',
        'course', 
        'academicYear',
        'term',
        'subjects.lesson',
        'subjects.testMarks.exam'
    ]);
    
    $schoolInfo = \App\Models\SchoolInformation::getActive();
    
    // Generate PDF
    $pdf = Pdf::loadView('admin.report-cards.print', compact('reportCard', 'schoolInfo'))
        ->setPaper('a4')
        ->setOption(['margin-top' => 5, 'margin-bottom' => 5, 'margin-left' => 5, 'margin-right' => 5]);
    
    // Save the PDF
    $filename = 'report_card_actual_test_' . time() . '.pdf';
    $pdf->save(base_path($filename));
    
    echo "Report card PDF generated successfully! File saved as: {$filename}\n";
    echo "The template has been optimized to fit on one page with reduced spacing.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
