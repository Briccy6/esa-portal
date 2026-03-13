<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $student = \App\Models\Student::first();
    if ($student) {
        echo "Found student: " . $student->full_name . " (" . $student->email . ")\n";
        
        // Test email verification creation
        $code = rand(100000, 999999);
        $verification = \App\Models\EmailVerification::updateOrCreate(
            ['email' => $student->email],
            [
                'code' => $code,
                'expires_at' => now()->addMinutes(10),
                'verified' => false,
            ]
        );
        echo "Verification code created: " . $code . "\n";
        echo "Expires at: " . $verification->expires_at . "\n";
        
        // Test mail configuration
        echo "\nTesting mail configuration...\n";
        $config = config('mail');
        echo "Mail driver: " . $config['default'] . "\n";
        echo "Mail host: " . $config['mailers']['smtp']['host'] . "\n";
        echo "Mail port: " . $config['mailers']['smtp']['port'] . "\n";
        echo "Mail from: " . $config['from']['address'] . "\n";
        
        // Test sending email
        echo "\nTesting email sending...\n";
        try {
            \Mail::raw('Test email content', function($message) use ($student) {
                $message->to($student->email)
                        ->subject('Test Email from ESA System');
            });
            echo "Test email sent successfully!\n";
        } catch (\Exception $e) {
            echo "Email sending failed: " . $e->getMessage() . "\n";
        }
        
    } else {
        echo "No students found in database\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
