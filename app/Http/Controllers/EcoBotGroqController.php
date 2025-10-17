<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class EcoBotGroqController extends Controller
{
    public function chat(Request $request): JsonResponse
    {
        $message = trim((string) $request->input('message', ''));
        if ($message === '') {
            return response()->json(['reply' => 'Veuillez poser une question.'], 422);
        }

        // petit anti-spam: 1 msg / 5s par IP
        $cdKey = 'ecobot:cd:' . sha1($request->ip());
        if (Cache::has($cdKey)) {
            return response()->json(['reply' => "Doucement 😅 Réessaie dans quelques secondes."]);
        }

        $apiKey = config('services.groq.api_key');
        if (!$apiKey) {
            return response()->json(['reply' => 'Clé API Groq manquante.'], 500);
        }

        // ✅ only supported models here (order = preference)
        $candidates = [
            'llama-3.1-70b-versatile',
            'llama-3.1-8b-instant',
            'gemma2-9b-it',
        ];

        $system = "Tu es ÉcoBot 🌿, assistant écologique. Réponds en FRANÇAIS, clair et concret (2 à 6 phrases). "
                . "Donne des conseils de tri/recyclage réalistes pour la France/UE quand c’est pertinent.";

        $http = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ])->withOptions([
                'verify'          => false, // ok en local Windows; mets true en prod
                'timeout'         => 25,
                'connect_timeout' => 10,
            ]);

        $lastError = null;

        try {
            foreach ($candidates as $model) {
                $payload = [
                    'model'       => $model,
                    'temperature' => 0.4,
                    'max_tokens'  => 400,
                    'messages'    => [
                        ['role' => 'system', 'content' => $system],
                        ['role' => 'user',   'content' => $message],
                    ],
                ];

                $resp = $http->post('https://api.groq.com/openai/v1/chat/completions', $payload);

                if ($resp->successful()) {
                    $text = trim((string) data_get($resp->json(), 'choices.0.message.content', ''));
                    if ($text !== '') {
                        Cache::put($cdKey, true, now()->addSeconds(5));
                        return response()->json(['reply' => $text]);
                    }
                    // empty text: treat as failure and try next model
                    $lastError = 'Réponse vide du modèle.';
                    continue;
                }

                // remember last error for final message
                $code = $resp->status();
                $json = $resp->json();
                $msg  = $json['error']['message'] ?? $json['message'] ?? mb_strimwidth($resp->body() ?? '', 0, 1000, '…');
                $lastError = "HTTP $code – $msg";

                // if model is decommissioned/not found, continue to next candidate
                if ($code === 400 && preg_match('~(decommissioned|not found|unknown model)~i', $msg)) {
                    Log::warning('Groq model not available', ['model' => $model, 'msg' => $msg]);
                    continue;
                }

                // for 429 rate-limit: tell user nicely and stop
                if ($code === 429) {
                    return response()->json([
                        'reply' => "Je suis un peu saturé en ce moment (429). Réessaie dans quelques secondes 🙏.",
                    ], 429);
                }

                // other error: try next model anyway
                Log::warning('Groq call failed', ['model' => $model, 'code' => $code, 'msg' => $msg]);
            }

            // none worked
            return response()->json([
                'reply' => "Oups, l’IA a refusé la requête :\n" . ($lastError ?? 'erreur inconnue'),
            ], 502);

        } catch (\Throwable $e) {
            Log::error('EcoBotGroq exception', ['e' => $e->getMessage()]);
            return response()->json([
                'reply' => "Erreur de connexion à l’IA : " . $e->getMessage(),
            ], 502);
        }
    }
}
