<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Check if email_verifications table exists and its structure
    $tableExists = \Illuminate\Support\Facades\Schema::hasTable('email_verifications');
    echo "Email verifications table exists: " . ($tableExists ? 'Yes' : 'No') . "\n";
    
    if ($tableExists) {
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('email_verifications');
        echo "Columns: " . implode(', ', $columns) . "\n";
        
        // Check if id column is auto-increment
        $idColumn = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM email_verifications WHERE Field = 'id'")[0];
        echo "ID column details: " . json_encode($idColumn) . "\n";
    }
    
    // Test creating a verification record with manual ID
    echo "\nTesting manual record creation...\n";
    $student = \App\Models\Student::first();
    if ($student) {
        echo "Found student: " . $student->full_name . " (" . $student->email . ")\n";
        
        // Delete any existing verification for this email
        \App\Models\EmailVerification::where('email', $student->email)->delete();
        
        // Get next ID
        $nextId = \Illuminate\Support\Facades\DB::table('email_verifications')->max('id') + 1;
        echo "Next ID: " . $nextId . "\n";
        
        // Insert with explicit ID
        $code = rand(100000, 999999);
        \Illuminate\Support\Facades\DB::table('email_verifications')->insert([
            'id' => $nextId,
            'email' => $student->email,
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
            'verified' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        echo "Verification record created with ID: " . $nextId . "\n";
        echo "Code: " . $code . "\n";
        
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
        
        // Test the actual verification email
        echo "\nTesting verification email...\n";
        try {
            \Mail::to($student->email)->send(new \App\Mail\StudentEmailVerificationMail($code));
            echo "Verification email sent successfully!\n";
        } catch (\Exception $e) {
            echo "Verification email failed: " . $e->getMessage() . "\n";
        }
        
    } else {
        echo "No students found in database\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
