<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyConverter
{
    protected string $apiUrl;
    protected string $apiKey;

    public function __construct()
    {
        // New API base URL (the rest of the path will be built in the convert method)
        $this->apiUrl = "https://v6.exchangerate-api.com/v6/";

        // Retrieve and validate the API Key
        $apiKey = config('services.currency.api_key', env('CURRENCY_API_KEY'));

        if (empty($apiKey) || $apiKey === 'your_actual_key_here') {
            // Throwing an exception is good practice if a critical dependency is missing
            throw new \Exception('Currency API key is required. Set CURRENCY_API_KEY in .env.');
        }

        $this->apiKey = $apiKey;
    }

    /**
     * Convert amount from EUR to target currency
     */
    public function convert(float $amount, string $toCurrency = 'USD'): float
    {
        $fromCurrency = 'EUR';
        $upperToCurrency = strtoupper($toCurrency);

        // 1. Construct the new API endpoint.
        // To convert FROM EUR, we must request EUR as the base code.
        $fullApiUrl = $this->apiUrl . $this->apiKey . '/latest/' . $fromCurrency;

        try {
            // 2. Fetch the conversion rates for the EUR base
            $response = Http::get($fullApiUrl);
            $data = $response->json();

            // Log the full response for debugging
            Log::info('Currency API Response (New)', [
                'data' => $data,
                'from' => $fromCurrency,
                'to' => $upperToCurrency,
                'amount' => $amount
            ]);

            // 3. Check for successful result and required rate data
            // New API uses 'result' = 'success' and 'conversion_rates' array
            if (isset($data['result']) && $data['result'] === 'success' && 
                isset($data['conversion_rates'][$upperToCurrency])) 
            {
                // Extract the rate for the target currency
                $rate = $data['conversion_rates'][$upperToCurrency];
                
                // 4. Calculate the converted amount manually (amount * rate)
                $result = $amount * $rate;
                
                return round((float) $result, 2);
            }

            // Log failure details if success check fails
            Log::warning('Currency conversion API failure', [
                'error' => $data['error-type'] ?? 'Unknown API error', // New API uses 'error-type'
                'to' => $upperToCurrency,
                'amount' => $amount
            ]);

            return $amount; // Fallback if API fails
        } catch (\Exception $e) {
            Log::error('Currency conversion failed: ' . $e->getMessage());
            return $amount;
        }
    }
}
