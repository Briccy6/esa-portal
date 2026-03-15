<?php

// Temporary fix script for WebRTC syntax error
$file = 'app/Http/Controllers/WebRTCController.php';
$content = file_get_contents($file);

// Fix the syntax error on line 139: 'type' => 'type'
$fixedContent = str_replace("'type'        => 'viewer_ready',", "'type'        => 'viewer_ready',", $content);

file_put_contents($file, $fixedContent);

echo "Fixed syntax error in WebRTCController.php\n";
echo "Changed 'type' to 'type' on line 139\n";
