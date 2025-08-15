<?php

namespace App\Http\Controllers;

use App\Services\AnthropicApiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiAssistantController extends Controller
{
    private $anthropicService;

    public function __construct(AnthropicApiService $anthropicService)
    {
        $this->anthropicService = $anthropicService;
    }

    /**
     * Show the AI Assistant interface
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('api_assistant.index');
    }

    /**
     * Test the API connection
     *
     * @return JsonResponse
     */
    public function testConnection()
    {
        try {
            $isConnected = $this->anthropicService->testConnection();
            
            return response()->json([
                'success' => $isConnected,
                'message' => $isConnected ? 'API connection successful' : 'API connection failed',
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error testing API connection: ' . $e->getMessage(),
                'timestamp' => now()
            ], 500);
        }
    }

    /**
     * Send a general message to the AI assistant
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'system_prompt' => 'nullable|string|max:500'
        ]);

        try {
            $response = $this->anthropicService->sendMessage(
                $request->input('message'),
                $request->input('system_prompt')
            );

            if ($response) {
                $responseText = $this->anthropicService->getResponseText($response);
                
                return response()->json([
                    'success' => true,
                    'response' => $responseText,
                    'full_response' => $response,
                    'timestamp' => now()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get response from AI assistant',
                    'timestamp' => now()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending message: ' . $e->getMessage(),
                'timestamp' => now()
            ], 500);
        }
    }

    /**
     * Get drug information
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getDrugInfo(Request $request)
    {
        $request->validate([
            'drug_name' => 'required|string|max:200'
        ]);

        try {
            $drugInfo = $this->anthropicService->getDrugInfo($request->input('drug_name'));

            if ($drugInfo) {
                return response()->json([
                    'success' => true,
                    'drug_name' => $request->input('drug_name'),
                    'information' => $drugInfo,
                    'timestamp' => now()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to retrieve drug information',
                    'timestamp' => now()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving drug information: ' . $e->getMessage(),
                'timestamp' => now()
            ], 500);
        }
    }

    /**
     * Check drug interactions
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkDrugInteractions(Request $request)
    {
        $request->validate([
            'drugs' => 'required|array|min:2',
            'drugs.*' => 'string|max:200'
        ]);

        try {
            $interactions = $this->anthropicService->checkDrugInteractions($request->input('drugs'));

            if ($interactions) {
                return response()->json([
                    'success' => true,
                    'drugs' => $request->input('drugs'),
                    'interactions' => $interactions,
                    'timestamp' => now()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to check drug interactions',
                    'timestamp' => now()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking drug interactions: ' . $e->getMessage(),
                'timestamp' => now()
            ], 500);
        }
    }

    /**
     * Ask pharmacy-related questions
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function askPharmacyQuestion(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:1000'
        ]);

        try {
            $answer = $this->anthropicService->askPharmacyQuestion($request->input('question'));

            if ($answer) {
                return response()->json([
                    'success' => true,
                    'question' => $request->input('question'),
                    'answer' => $answer,
                    'timestamp' => now()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get answer for pharmacy question',
                    'timestamp' => now()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing pharmacy question: ' . $e->getMessage(),
                'timestamp' => now()
            ], 500);
        }
    }
} 