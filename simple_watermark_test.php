<?php

echo "Testing ESA Watermark Setup...\n\n";

// Check if ESA logo exists
$logoPath = __DIR__ . '/public/images/esa.png';
echo "ESA Logo Path: " . $logoPath . "\n";
echo "ESA Logo Exists: " . (file_exists($logoPath) ? "YES" : "NO") . "\n\n";

if (!file_exists($logoPath)) {
    echo "❌ ESA logo not found at: " . $logoPath . "\n";
    echo "   Please ensure the ESA logo exists at public/images/esa.png\n\n";
    
    // Check if the directory exists
    $imagesDir = __DIR__ . '/public/images';
    echo "Images directory exists: " . (is_dir($imagesDir) ? "YES" : "NO") . "\n";
    
    if (!is_dir($imagesDir)) {
        echo "Creating images directory...\n";
        mkdir($imagesDir, 0755, true);
        echo "Images directory created.\n";
    }
    
    // List files in public directory
    $publicDir = __DIR__ . '/public';
    if (is_dir($publicDir)) {
        echo "\nFiles in public directory:\n";
        $files = scandir($publicDir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                echo "- " . $file . "\n";
            }
        }
    }
} else {
    echo "✅ ESA logo found!\n";
    echo "   Logo size: " . filesize($logoPath) . " bytes\n";
    
    // Get image info if it's an image
    $imageInfo = getimagesize($logoPath);
    if ($imageInfo) {
        echo "   Image dimensions: " . $imageInfo[0] . "x" . $imageInfo[1] . " pixels\n";
        echo "   Image type: " . $imageInfo['mime'] . "\n";
    }
}

echo "\nChecking WatermarkService file...\n";
$servicePath = __DIR__ . '/app/Services/WatermarkService.php';
echo "WatermarkService exists: " . (file_exists($servicePath) ? "YES" : "NO") . "\n";

if (file_exists($servicePath)) {
    echo "✅ WatermarkService found!\n";
    echo "   File size: " . filesize($servicePath) . " bytes\n";
} else {
    echo "❌ WatermarkService not found!\n";
}

echo "\nChecking required PHP extensions...\n";

// Check for required extensions
$requiredExtensions = [
    'gd' => 'GD (for image processing)',
    'mbstring' => 'Multibyte String',
    'pdo' => 'PDO (database)',
    'pdo_mysql' => 'PDO MySQL',
];

foreach ($requiredExtensions as $ext => $desc) {
    $loaded = extension_loaded($ext);
    echo "- " . $desc . ": " . ($loaded ? "✅ LOADED" : "❌ MISSING") . "\n";
}

echo "\nWatermark setup test completed.\n";
