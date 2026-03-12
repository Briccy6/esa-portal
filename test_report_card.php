<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$reportCard = App\Models\ReportCard::find(3);
if ($reportCard) {
    echo "Report Card ID: " . $reportCard->id . "\n";
    echo "Student: " . $reportCard->student->name . "\n";
    echo "Subjects: " . $reportCard->subjects->count() . "\n";
    echo "Status: OK\n";
} else {
    echo "Report Card not found\n";
}
