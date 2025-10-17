<?php

namespace App\Services;

use LucianoTonet\GroqPHP\Groq;
use Illuminate\Support\Facades\Log;

class AIDescriptionEnhancer
{
    private Groq $client;
    private string $model = 'llama-3.3-70b-versatile';

    public function __construct()
    {
        $apiKey = config('services.groq.api_key');
        
        if (empty($apiKey)) {
            Log::error('[v0] Groq API key is not configured in .env file');
            throw new \Exception('Groq API key is not configured. Please add GROQ_API_KEY to your .env file.');
        }
        
        Log::info('[v0] Initializing Groq client with API key: ' . substr($apiKey, 0, 10) . '...');
        $this->client = new Groq($apiKey);
    }

    /**
     * Enhance a donation description using Groq API.
     *
     * @param string $originalDescription
     * @return string Enhanced description or original if failed
     */
    public function enhance(string $originalDescription): string
    {
        if (empty(trim($originalDescription))) {
            Log::warning('[v0] Empty description provided to enhance');
            return $originalDescription;
        }

        Log::info('[v0] Starting enhancement for description', [
            'original' => $originalDescription,
            'length' => strlen($originalDescription)
        ]);

        try {
            Log::info('[v0] Sending request to Groq API', [
                'model' => $this->model,
                'original_text' => $originalDescription
            ]);
            
            $response = $this->client->chat()->completions()->create([
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => "Rewrite and expand this donation description to be much more detailed and compelling (aim for 100-150 words). Add information about environmental benefits, potential uses, and why someone would want this item. Make it persuasive and engaging.

Original description: {$originalDescription}

Enhanced description:",
                    ],
                ],
                'max_tokens' => 400,
                'temperature' => 1.0,
                'top_p' => 0.95,
            ]);

            Log::info('[v0] Raw Groq API Response', [
                'response_type' => gettype($response),
                'response_keys' => is_array($response) ? array_keys($response) : (is_object($response) ? get_object_vars($response) : 'unknown'),
                'full_response' => print_r($response, true)
            ]);

            $responseArray = is_array($response) ? $response : json_decode(json_encode($response), true);
            
            $enhanced = '';
            
            // Extract content from response array
            if (isset($responseArray['choices'][0]['message']['content'])) {
                $enhanced = trim($responseArray['choices'][0]['message']['content']);
            }
            
            Log::info('[v0] Extracted enhanced text', [
                'enhanced' => $enhanced,
                'enhanced_length' => strlen($enhanced)
            ]);
            
            if (empty($enhanced)) {
                Log::error('[v0] Could not extract enhanced text from response');
                return $originalDescription;
            }
            
            if ($enhanced === $originalDescription) {
                Log::warning('[v0] AI returned identical text');
                return $originalDescription;
            }
            
            Log::info('[v0] AI Description Enhanced Successfully', [
                'original' => $originalDescription,
                'enhanced' => $enhanced,
                'original_length' => strlen($originalDescription),
                'enhanced_length' => strlen($enhanced),
                'expansion_ratio' => round(strlen($enhanced) / strlen($originalDescription), 2)
            ]);

            return $enhanced;
            
        } catch (\Exception $e) {
            Log::error('[v0] AI Description Enhancement Failed', [
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_class' => get_class($e),
                'stack_trace' => $e->getTraceAsString(),
                'original_description' => $originalDescription,
            ]);
            
            return $originalDescription;
        }
    }
}
