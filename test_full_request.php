<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Get a real student
    $student = App\Models\Student::whereNotNull('email')->where('email', '!=', '')->first();
    if (!$student) {
        echo "No student with email found\n";
        exit;
    }
    
    echo "Testing full request simulation...\n";
    echo "Student: " . $student->reg_number . " - " . $student->email . "\n";
    
    // Create a request that mimics the browser request
    $request = Illuminate\Http\Request::create(
        '/students/send-verification-code', 
        'POST',
        [], // POST data
        [], // Cookies
        [], // Files
        [], // Server variables
        json_encode(['reg_number' => $student->reg_number]) // Content
    );
    
    $request->headers->set('Content-Type', 'application/json');
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');
    $request->headers->set('Accept', 'application/json');
    
    echo "Request created\n";
    
    $controller = new App\Http\Controllers\StudentController();
    $response = $controller->sendVerificationCode($request);
    
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Response: " . $response->getContent() . "\n";
    
    // Check if email was actually sent
    echo "Email verification record created: ";
    $verification = App\Models\EmailVerification::where('email', $student->email)->first();
    echo ($verification ? "YES (Code: " . $verification->code . ")" : "NO") . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
