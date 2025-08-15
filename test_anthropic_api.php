<?php

/**
 * Test script for Anthropic API integration
 * Run this script to test the API connection and functionality
 */

// Bootstrap Laravel
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\AnthropicApiService;

// Initialize the service
$anthropicService = new AnthropicApiService();

echo "=== Anthropic API Test Script ===\n\n";

// Test 1: Connection Test
echo "1. Testing API Connection...\n";
$isConnected = $anthropicService->testConnection();
if ($isConnected) {
    echo "✓ API connection successful!\n\n";
} else {
    echo "✗ API connection failed!\n\n";
    exit(1);
}

// Test 2: Simple Message
echo "2. Testing Simple Message...\n";
$response = $anthropicService->sendMessage(
    "Hello! Can you tell me about Aspirin?",
    "You are a helpful pharmacy assistant."
);

if ($response) {
    $responseText = $anthropicService->getResponseText($response);
    echo "✓ Response received:\n";
    echo "Response: " . substr($responseText, 0, 200) . "...\n\n";
} else {
    echo "✗ Failed to get response\n\n";
}

// Test 3: Drug Information
echo "3. Testing Drug Information...\n";
$drugInfo = $anthropicService->getDrugInfo("Ibuprofen");

if ($drugInfo) {
    echo "✓ Drug information received:\n";
    echo "Info: " . substr($drugInfo, 0, 200) . "...\n\n";
} else {
    echo "✗ Failed to get drug information\n\n";
}

// Test 4: Drug Interactions
echo "4. Testing Drug Interactions...\n";
$interactions = $anthropicService->checkDrugInteractions(["Aspirin", "Warfarin"]);

if ($interactions) {
    echo "✓ Drug interactions received:\n";
    echo "Interactions: " . substr($interactions, 0, 200) . "...\n\n";
} else {
    echo "✗ Failed to get drug interactions\n\n";
}

echo "=== Test Complete ===\n";
echo "If all tests passed, your Anthropic API integration is working correctly!\n"; 