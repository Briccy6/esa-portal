<?php

/**
 * Test Gemini API connectivity and the passport photo validation service
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Gemini API Connection...\n\n";

// Check if API key is configured
$apiKey = config('services.gemini.api_key');
$model = config('services.gemini.model');

if (empty($apiKey)) {
    echo "❌ ERROR: Gemini API key is not configured\n";
    echo "Please add GEMINI_API_KEY to your .env file\n";
    exit(1);
}

echo "✓ API Key configured: " . substr($apiKey, 0, 20) . "...\n";
echo "✓ Model: {$model}\n\n";

// Test 1: Simple text request
try {
    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

    $payload = [
        'contents' => [
            [
                'parts' => [
                    ['text' => 'Say "passport photo verification ready" in one sentence'],
                ],
            ],
        ],
    ];

    $response = Illuminate\Support\Facades\Http::timeout(30)
        ->withHeaders([
            'Content-Type' => 'application/json',
            'X-goog-api-key' => $apiKey,
        ])
        ->withOptions([
            'verify' => env('VERIFY_SSL', true),
        ])
        ->post($url, $payload);

    if ($response->successful()) {
        $data = $response->json();
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';
        echo "✓ Text test successful: {$text}\n";
    } else {
        echo "❌ Text test failed: {$response->status()} - {$response->body()}\n";
    }
} catch (Exception $e) {
    echo "❌ Text test error: {$e->getMessage()}\n";
}

echo "\nTesting passport photo validation service...\n\n";

// Test 2: Service initialization
try {
    $service = new App\Services\PassportPhotoVerificationService();
    echo "✓ Service initialized successfully\n";

    // Test 3: Error message mapping
    echo "\nTesting error message mapping:\n";
    $messages = [
        'NO_FACE',
        'MULTIPLE_FACES',
        'COLORED_BACKGROUND',
        'SELFIE',
        'BLURRY_IMAGE',
        'DOCUMENT_SCAN',
    ];

    foreach ($messages as $reason) {
        $message = App\Services\PassportPhotoVerificationService::getErrorMessage($reason);
        echo "  {$reason}: {$message}\n";
    }

} catch (Exception $e) {
    echo "❌ Service test error: {$e->getMessage()}\n";
}

echo "\n✅ Gemini API configuration test completed!\n";
echo "\nNext steps:\n";
echo "1. Upload a passport photo in the student profile page\n";
echo "2. Basic image requirements will be checked and staff must review the photo\n";
echo "3. Check the verification status in the admin panel\n";
