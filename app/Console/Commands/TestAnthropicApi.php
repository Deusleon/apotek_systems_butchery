<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AnthropicApiService;

class TestAnthropicApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anthropic:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the Anthropic API integration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('=== Anthropic API Test Command ===');
        $this->line('');

        $anthropicService = new AnthropicApiService();

        // Test 1: Connection Test
        $this->info('1. Testing API Connection...');
        $isConnected = $anthropicService->testConnection();
        if ($isConnected) {
            $this->info('✓ API connection successful!');
        } else {
            $this->error('✗ API connection failed!');
            return 1;
        }
        $this->line('');

        // Test 2: Simple Message
        $this->info('2. Testing Simple Message...');
        $response = $anthropicService->sendMessage(
            "Hello! Can you tell me about Aspirin?",
            "You are a helpful pharmacy assistant."
        );

        if ($response) {
            $responseText = $anthropicService->getResponseText($response);
            $this->info('✓ Response received:');
            $this->line('Response: ' . substr($responseText, 0, 200) . '...');
        } else {
            $this->error('✗ Failed to get response');
        }
        $this->line('');

        // Test 3: Drug Information
        $this->info('3. Testing Drug Information...');
        $drugInfo = $anthropicService->getDrugInfo("Ibuprofen");

        if ($drugInfo) {
            $this->info('✓ Drug information received:');
            $this->line('Info: ' . substr($drugInfo, 0, 200) . '...');
        } else {
            $this->error('✗ Failed to get drug information');
        }
        $this->line('');

        // Test 4: Drug Interactions
        $this->info('4. Testing Drug Interactions...');
        $interactions = $anthropicService->checkDrugInteractions(["Aspirin", "Warfarin"]);

        if ($interactions) {
            $this->info('✓ Drug interactions received:');
            $this->line('Interactions: ' . substr($interactions, 0, 200) . '...');
        } else {
            $this->error('✗ Failed to get drug interactions');
        }
        $this->line('');

        $this->info('=== Test Complete ===');
        $this->info('If all tests passed, your Anthropic API integration is working correctly!');

        return 0;
    }
} 