<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class AnthropicApiService
{
    private $apiKey;
    private $baseUrl = 'https://api.anthropic.com/v1';

    public function __construct()
    {
        // Get API key from environment variable
        $this->apiKey = env('ANTHROPIC_API_KEY', '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');
    }

    /**
     * Send a message to Claude API
     *
     * @param string $message The user message
     * @param string $systemPrompt Optional system prompt
     * @param array $options Additional options
     * @return array|null
     */
    public function sendMessage($message, $systemPrompt = null, $options = [])
    {
        try {
            $messages = [];
            if ($systemPrompt) {
                $messages[] = [
                    'role' => 'system',
                    'content' => $systemPrompt
                ];
            }
            $messages[] = [
                'role' => 'user',
                'content' => $message
            ];

            $body = [
                'model' => $options['model'] ?? 'claude-3-5-sonnet-20241022',
                'max_tokens' => $options['max_tokens'] ?? 1000,
                'messages' => $messages
            ];

            $client = new Client([
                'timeout' => 30,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'x-api-version' => '2023-06-01',
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ]
            ]);

            $response = $client->post($this->baseUrl . '/messages', [
                'json' => $body
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            Log::info('Anthropic API response', [
                'status' => $response->getStatusCode(),
                'response' => $responseData
            ]);

            return $responseData;

        } catch (RequestException $e) {
            Log::error('Anthropic API request failed', [
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Anthropic API error', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Test the API connection
     *
     * @return bool
     */
    public function testConnection()
    {
        $response = $this->sendMessage(
            'Testing. Just say hi and nothing else.',
            'You are a test assistant.'
        );

        return $response !== null && isset($response['content'][0]['text']);
    }

    /**
     * Get the response text from API response
     *
     * @param array $response
     * @return string|null
     */
    public function getResponseText($response)
    {
        if (isset($response['content'][0]['text'])) {
            return $response['content'][0]['text'];
        }
        
        return null;
    }

    /**
     * Send a pharmacy-related query
     *
     * @param string $query
     * @return string|null
     */
    public function askPharmacyQuestion($query)
    {
        $systemPrompt = "You are a helpful assistant for a pharmacy management system. Provide accurate, professional advice about medications, drug interactions, and pharmacy operations. Always recommend consulting with healthcare professionals for medical advice.";
        
        $response = $this->sendMessage($query, $systemPrompt);
        
        return $this->getResponseText($response);
    }

    /**
     * Get drug information
     *
     * @param string $drugName
     * @return string|null
     */
    public function getDrugInfo($drugName)
    {
        $query = "Provide general information about the drug: {$drugName}. Include common uses, typical dosage forms, and any important warnings. Note that this is for informational purposes only.";
        
        return $this->askPharmacyQuestion($query);
    }

    /**
     * Check for potential drug interactions
     *
     * @param array $drugs
     * @return string|null
     */
    public function checkDrugInteractions($drugs)
    {
        $drugList = implode(', ', $drugs);
        $query = "Check for potential interactions between these drugs: {$drugList}. Provide a general overview of known interactions and recommend consulting a pharmacist or healthcare provider.";
        
        return $this->askPharmacyQuestion($query);
    }
} 