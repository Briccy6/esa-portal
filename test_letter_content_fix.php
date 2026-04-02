<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test the improved letter content
$student = App\Models\Student::find(270);
$controller = new App\Http\Controllers\ToWhomLetterController();

echo "Testing improved letter content (no repetitions, no ID number):\n\n";

// Test enrollment content
$purpose = "job application";
$content = $controller->generateLetterContent('enrollment', $student, $purpose);

echo "=== ENROLLMENT LETTER ===\n";
echo $content;
echo "\n\n";

echo "Expected structure:\n";
echo "- Course: Course ID: 18 (Name not available)\n";
echo "- Branch: Kigali\n";
echo "- Academic Year: Academic Year ID: 4 (Name not available)\n";
echo "- NO ID Number field\n";
echo "- NO repetitive purpose statements\n";
echo "- NO repetitive contact information\n";
