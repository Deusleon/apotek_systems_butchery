<?php

// Simple test to verify API key
$apiKey = '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';

echo "Testing API key: " . substr($apiKey, 0, 20) . "...\n\n";

$url = 'https://api.anthropic.com/v1/messages';
$data = [
    'model' => 'claude-3-5-sonnet-20241022',
    'max_tokens' => 100,
    'messages' => [
        [
            'role' => 'user',
            'content' => 'Hello! Just say hi.'
        ]
    ]
];

$headers = [
    'Content-Type: application/json',
    'x-api-version: 2023-06-01',
    'Authorization: Bearer ' . $apiKey
];

echo "Making request to: $url\n";
echo "Headers: " . json_encode($headers, JSON_PRETTY_PRINT) . "\n";
echo "Data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n\n";

// Use cURL to test
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if ($error) {
    echo "cURL Error: $error\n";
}
echo "Response: $response\n"; 