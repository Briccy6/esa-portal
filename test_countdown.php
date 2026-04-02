<?php

// Test the countdown endpoint directly
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/student/report-cards/countdown');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: " . $httpCode . "\n";
echo "Response: " . $response . "\n";

$data = json_decode($response, true);
if ($data) {
    echo "\nParsed Response:\n";
    echo "Can Access: " . ($data['can_access'] ? 'Yes' : 'No') . "\n";
    echo "Reason: " . $data['reason'] . "\n";
    echo "Requires Auth: " . ($data['requires_auth'] ? 'Yes' : 'No') . "\n";
    if (isset($data['countdown_data']) && $data['countdown_data']) {
        echo "Countdown: " . $data['countdown_data']['days'] . " days, " . $data['countdown_data']['hours'] . " hours\n";
    }
} else {
    echo "Failed to parse JSON response\n";
}
