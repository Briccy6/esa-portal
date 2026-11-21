<?php

// Test ESA Live Support Chat System
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ESA Live Support Chat System Test ===\n\n";

// Test 1: Check if Gemini service is configured
echo "1. Testing Gemini API Configuration...\n";
$apiKey = config('services.gemini.api_key');
$model = config('services.gemini.model');

if (empty($apiKey)) {
    echo "   ❌ ERROR: Gemini API key is not configured\n";
} else {
    echo "   ✅ API Key configured: " . substr($apiKey, 0, 20) . "...\n";
    echo "   ✅ Model: {$model}\n";
}

// Test 2: Check if models exist
echo "\n2. Testing Chat Models...\n";
$models = [
    'SupportConversation' => App\Models\SupportConversation::class,
    'SupportMessage' => App\Models\SupportMessage::class,
    'SupportInternalNote' => App\Models\SupportInternalNote::class,
];

foreach ($models as $name => $class) {
    if (class_exists($class)) {
        echo "   ✅ {$name} model exists\n";
    } else {
        echo "   ❌ {$name} model missing\n";
    }
}

// Test 3: Check if tables exist
echo "\n3. Testing Database Tables...\n";
$tables = [
    'support_conversations' => 'SupportConversation',
    'support_messages' => 'SupportMessage',
    'support_internal_notes' => 'SupportInternalNote',
];

foreach ($tables as $table => $name) {
    if (Schema::hasTable($table)) {
        echo "   ✅ {$table} table exists\n";
    } else {
        echo "   ❌ {$table} table missing\n";
    }
}

// Test 4: Check if Gemini service can be instantiated
echo "\n4. Testing GeminiChatService...\n";
try {
    $geminiService = new App\Services\GeminiChatService();
    echo "   ✅ GeminiChatService instantiated successfully\n";

    // Test language detection
    $testMessages = [
        'Hello, I need help with my assignment' => 'en',
        'Murakoze, ndashaka ubufasha n\'amasomo' => 'rw',
    ];

    foreach ($testMessages as $message => $expected) {
        $detected = $geminiService->detectLanguage($message);
        if ($detected === $expected) {
            echo "   ✅ Language detection works: '{$message}' -> {$detected}\n";
        } else {
            echo "   ⚠️  Language detection may need improvement: '{$message}' -> {$detected} (expected {$expected})\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Test 5: Check if routes are registered
echo "\n5. Testing Routes...\n";
$routes = [
    'admin.support.dashboard' => 'Admin Support Dashboard',
    'api.chat.conversation' => 'Chat API - Get Conversation',
    'api.chat.message' => 'Chat API - Send Message',
    'api.chat.history' => 'Chat API - Get History',
    'api.chat.request-human' => 'Chat API - Request Human',
];

foreach ($routes as $name => $description) {
    try {
        $url = route($name);
        echo "   ✅ {$name}: {$description}\n";
        echo "      URL: {$url}\n";
    } catch (Exception $e) {
        echo "   ❌ {$name}: {$description} - Route not found\n";
    }
}

// Test 6: Check if chat widget component exists
echo "\n6. Testing Chat Widget Component...\n";
$widgetPath = 'resources/views/components/chat-widget.blade.php';
if (file_exists($widgetPath)) {
    echo "   ✅ Chat widget component exists\n";
} else {
    echo "   ❌ Chat widget component missing\n";
}

// Test 7: Check if admin dashboard view exists
echo "\n7. Testing Admin Dashboard View...\n";
$dashboardPath = 'resources/views/admin/support/dashboard.blade.php';
if (file_exists($dashboardPath)) {
    echo "   ✅ Admin dashboard view exists\n";
} else {
    echo "   ❌ Admin dashboard view missing\n";
}

// Test 8: Check if chat widget is included in layouts
echo "\n8. Testing Chat Widget Integration...\n";
$layouts = [
    'teacher' => 'resources/views/teacher/layout.blade.php',
    'student' => 'resources/views/students/app.blade.php',
    'staff' => 'resources/views/staff/layout.blade.php',
];

foreach ($layouts as $name => $path) {
    if (file_exists($path)) {
        $content = file_get_contents($path);
        if (str_contains($content, 'chat-widget')) {
            echo "   ✅ Chat widget included in {$name} layout\n";
        } else {
            echo "   ❌ Chat widget NOT included in {$name} layout\n";
        }
    } else {
        echo "   ⚠️  {$name} layout not found\n";
    }
}

echo "\n=== Test Complete ===\n";
echo "\nNext steps:\n";
echo "1. Test the chat widget in the browser\n";
echo "2. Test the admin support dashboard\n";
echo "3. Verify Gemini AI responses\n";
echo "4. Test human takeover functionality\n";
echo "5. Test role-based access control\n";
