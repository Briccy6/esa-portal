<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Laravel Mail System...\n\n";

try {
    // Test Laravel mail configuration
    echo "1. Mail Configuration:\n";
    echo "   MAIL_MAILER: " . env('MAIL_MAILER') . "\n";
    echo "   MAIL_HOST: " . env('MAIL_HOST') . "\n";
    echo "   MAIL_PORT: " . env('MAIL_PORT') . "\n";
    echo "   MAIL_USERNAME: " . env('MAIL_USERNAME') . "\n";
    echo "   MAIL_FROM_ADDRESS: " . env('MAIL_FROM_ADDRESS') . "\n";
    echo "   MAIL_FROM_NAME: " . env('MAIL_FROM_NAME') . "\n\n";
    
    // Test mail transport
    echo "2. Testing mail transport...\n";
    
    $mailer = app('mailer');
    echo "   Mailer class: " . get_class($mailer) . "\n";
    
    // Try to get the transport
    $transport = $mailer->getSymfonyTransport();
    echo "   Transport class: " . get_class($transport) . "\n";
    
    // Test sending a simple test email
    echo "\n3. Sending test email...\n";
    
    try {
        Mail::raw('This is a test email sent at ' . now()->format('Y-m-d H:i:s'), function ($message) {
            $message->to('test@example.com')
                    ->subject('Test Email from ESA System')
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        });
        
        echo "   ✅ Test email sent successfully!\n";
    } catch (\Exception $e) {
        echo "   ❌ Email sending failed: " . $e->getMessage() . "\n";
        
        // Check for common Gmail SMTP issues
        if (strpos($e->getMessage(), 'authentication') !== false) {
            echo "   💡 This might be a Gmail authentication issue.\n";
            echo "   💡 Make sure to:\n";
            echo "      - Enable 2-factor authentication on the Gmail account\n";
            echo "      - Use an App Password instead of the regular password\n";
            echo "      - Check if the App Password is correct\n";
        } elseif (strpos($e->getMessage(), 'connection') !== false) {
            echo "   💡 This might be a connection issue.\n";
            echo "   💡 Check if:\n";
            echo "      - The firewall allows outbound connections on port 587\n";
            echo "      - The internet connection is working\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ General error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\nMail test completed.\n";
