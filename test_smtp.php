<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Mail;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing SMTP Connection...\n\n";

try {
    $transport = new \Swift_SmtpTransport('smtp.gmail.com', 587, 'tls');
    $transport->setUsername('excellentservantacademy@gmail.com');
    $transport->setPassword('rzzvzdcoafdreszq');
    
    echo "Attempting to connect to Gmail SMTP...\n";
    $transport->start();
    
    echo "✅ SMTP connection successful!\n";
    
    // Test sending a simple email
    echo "Testing simple email send...\n";
    
    $mailer = new \Swift_Mailer($transport);
    $message = (new \Swift_Message('Test Email'))
        ->setFrom(['excellentservantacademy@gmail.com' => 'Excellent Servant Academy'])
        ->setTo(['test@example.com'])
        ->setBody('This is a test email sent at ' . date('Y-m-d H:i:s'));
    
    $result = $mailer->send($message);
    
    if ($result > 0) {
        echo "✅ Test email sent successfully (recipients: $result)\n";
    } else {
        echo "❌ Test email failed to send\n";
    }
    
    $transport->stop();
    
} catch (\Swift_TransportException $e) {
    echo "❌ SMTP Transport Error: " . $e->getMessage() . "\n";
} catch (\Exception $e) {
    echo "❌ General Error: " . $e->getMessage() . "\n";
}

echo "\nSMTP test completed.\n";
