<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyConverter
{
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = "https://api.exchangerate.host/convert";

        $apiKey = config('services.currency.api_key', env('CURRENCY_API_KEY'));
        if (empty($apiKey) || $apiKey === 'your_actual_key_here') {
            throw new \Exception('Currency API key is required. Set CURRENCY_API_KEY in .env.');
        }
    }

    /**
     * Convert amount from EUR to target currency
     */
    public function convert(float $amount, string $toCurrency = 'USD'): float
    {
        try {
            $response = Http::get($this->apiUrl, [
                'access_key' => config('services.currency.api_key', env('CURRENCY_API_KEY')), // Add the key here
                'from' => 'EUR',
                'to' => strtoupper($toCurrency),
                'amount' => $amount,
            ]);

            $data = $response->json();

            // Log the full response for debugging (remove in production if verbose)
            Log::info('Currency API Response', ['data' => $data, 'to' => $toCurrency, 'amount' => $amount]);

            if (isset($data['success']) && $data['success'] === true && isset($data['result'])) {
                return round((float) $data['result'], 2);
            }

            // Log failure details
            Log::warning('Currency conversion API failure', [
                'error' => $data['error'] ?? 'Unknown error',
                'to' => $toCurrency,
                'amount' => $amount
            ]);

            return $amount; // Fallback if API fails
        } catch (\Exception $e) {
            Log::error('Currency conversion failed: ' . $e->getMessage());
            return $amount;
        }
    }
}