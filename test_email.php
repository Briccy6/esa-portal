<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Mail;
use App\Mail\NesaRegistrationStatusMail;
use App\Models\Student;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing NESA Email Configuration...\n\n";

try {
    // Test 1: Basic Mail Configuration
    echo "1. Testing basic mail configuration...\n";
    $config = config('mail');
    echo "   Mail Driver: " . $config['default'] . "\n";
    echo "   Mail Host: " . $config['mailers']['smtp']['host'] . "\n";
    echo "   Mail Port: " . $config['mailers']['smtp']['port'] . "\n";
    echo "   Mail Username: " . $config['mailers']['smtp']['username'] . "\n";
    $fromAddress = $config['mailers']['smtp']['from']['address'] ?? 'Not configured';
    echo "   Mail From: " . $fromAddress . "\n\n";
    
    // Test 2: Find a test student
    echo "2. Finding a test student...\n";
    $student = \App\Models\Student::find(269);
    if (!$student) {
        echo "   Student 269 not found, using first available student...\n";
        $student = \App\Models\Student::first();
    }
    
    if ($student) {
        echo "   Found student: " . $student->full_name . " (ID: " . $student->id . ")\n";
        echo "   Student email: " . ($student->email ?? 'No email') . "\n\n";
        
        if ($student->email) {
            // Test 3: Try to send a test email
            echo "3. Sending test email...\n";
            
            $emailContent = "This is a test NESA registration status email sent at " . now()->format('Y-m-d H:i:s');
            
            try {
                Mail::to($student->email)->send(new NesaRegistrationStatusMail($student, 'approved', $emailContent));
                echo "   ✅ Email sent successfully to " . $student->email . "\n";
            } catch (\Exception $e) {
                echo "   ❌ Email sending failed: " . $e->getMessage() . "\n";
                echo "   Error details: " . $e->getTraceAsString() . "\n";
            }
        } else {
            echo "   ⚠️  Student has no email address, skipping email test\n";
        }
    } else {
        echo "   ❌ No students found in database\n";
    }
    
    echo "\n4. Testing mail queue connection...\n";
    try {
        $transport = Mail::getSwiftMailer()->getTransport();
        echo "   Mail transport class: " . get_class($transport) . "\n";
        
        if (method_exists($transport, 'start')) {
            $transport->start();
            echo "   ✅ Mail transport started successfully\n";
            $transport->stop();
        }
    } catch (\Exception $e) {
        echo "   ❌ Mail transport error: " . $e->getMessage() . "\n";
    }
    
} catch (\Exception $e) {
    echo "❌ General error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\nEmail test completed.\n";
