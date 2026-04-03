<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$schoolInfo = \App\Models\SchoolInformation::first();
if ($schoolInfo) {
    echo "School exists: " . $schoolInfo->school_name . PHP_EOL;
    echo "Has logo BLOB: " . (!empty($schoolInfo->logo) ? 'YES (' . strlen($schoolInfo->logo) . ' bytes)' : 'NO') . PHP_EOL;
    echo "Has logo_path: " . (!empty($schoolInfo->logo_path) ? 'YES: ' . $schoolInfo->logo_path : 'NO') . PHP_EOL;
    echo "Has principal_signature_data BLOB: " . (!empty($schoolInfo->principal_signature_data) ? 'YES (' . strlen($schoolInfo->principal_signature_data) . ' bytes)' : 'NO') . PHP_EOL;
    echo "Has principal_signature path: " . (!empty($schoolInfo->principal_signature) ? 'YES: ' . $schoolInfo->principal_signature : 'NO') . PHP_EOL;
    echo "Has stamp_signature_data BLOB: " . (!empty($schoolInfo->stamp_signature_data) ? 'YES (' . strlen($schoolInfo->stamp_signature_data) . ' bytes)' : 'NO') . PHP_EOL;
    echo "Has stamp_signature path: " . (!empty($schoolInfo->stamp_signature) ? 'YES: ' . $schoolInfo->stamp_signature : 'NO') . PHP_EOL;
    echo PHP_EOL . "Testing accessors:" . PHP_EOL;
    echo "logo_url: " . ($schoolInfo->logo_url ? 'Generated (data URL)' : 'NULL') . PHP_EOL;
    echo "principal_signature_url: " . ($schoolInfo->principal_signature_url ? 'Generated (data URL)' : 'NULL') . PHP_EOL;
    echo "stamp_signature_url: " . ($schoolInfo->stamp_signature_url ? 'Generated (data URL)' : 'NULL') . PHP_EOL;
} else {
    echo "NO SCHOOL INFO FOUND" . PHP_EOL;
}

echo PHP_EOL . "Sample student check:" . PHP_EOL;
$student = \App\Models\Student::with('course', 'branch')->first();
if ($student) {
    echo "Student found: " . $student->full_name . PHP_EOL;
    echo "Has passport_photo: " . (!empty($student->passport_photo) ? 'YES: ' . $student->passport_photo : 'NO') . PHP_EOL;
    if ($student->passport_photo && file_exists(public_path($student->passport_photo))) {
        echo "Passport file exists on disk: YES" . PHP_EOL;
    } else {
        echo "Passport file exists on disk: NO" . PHP_EOL;
    }
} else {
    echo "NO STUDENT FOUND" . PHP_EOL;
}
