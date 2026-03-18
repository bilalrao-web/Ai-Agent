<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected int $maxToolRounds = 5;

    /**
     * @param  array<int, array{role: string, content: string}>  $history
     */
    public function generateResponse(string $userQuery, array $contextData = [], array $history = []): string
    {
        // Pull the key and model dynamically from the .env file
        $apiKey = config('services.gemini.api_key');
        $modelName = config('services.gemini.model');

        if (empty($apiKey)) {
            Log::error('Gemini API key is not configured.');
            return 'Gemini API key is not configured.';
        }

        // Insert the correct model name into the URL
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key={$apiKey}";

        $systemText = 'You are a customer support AI agent. Be concise, max 2 sentences. Only answer based on provided customer data. Customer data: ' . json_encode($contextData);

        $contents = [
            [
                'role' => 'user',
                'parts' => [['text' => $systemText]],
            ],
            [
                'role' => 'model',
                'parts' => [['text' => 'Understood. I will help the customer based on their data.']],
            ],
        ];

        foreach ($history as $msg) {
            $role = ($msg['role'] ?? '') === 'assistant' ? 'model' : 'user';
            $contents[] = [
                'role' => $role,
                'parts' => [['text' => $msg['content'] ?? '']],
            ];
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $userQuery]],
        ];

        try {
            $response = Http::withoutVerifying()->timeout(30)->post($url, [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 150,
                ],
            ]);

            Log::info('Gemini API Status: ' . $response->status());
            Log::info('Gemini API URL: ' . $url);
            Log::info('Gemini API Response: ' . $response->body());

            if (! $response->successful()) {
                Log::error('Gemini API Failed: ' . $response->body());
                return 'Sorry, the AI service is temporarily unavailable.';
            }

            $body = $response->json();
            $text = $body['candidates'][0]['content']['parts'][0]['text'] ?? '';

            return trim($text) ?: 'I could not generate a response for that query.';
        } catch (\Throwable $e) {
            report($e);
            return 'Sorry, an error occurred while processing your request.';
        }
    }

    /**
     * Generate response using Gemini function calling. Model may request tools; we execute and send results back until we get final text.
     */
    public function generateWithToolCalling(string $userQuery, int $customerId, GeminiToolExecutor $executor): string
    {
        // Pull the key and model dynamically from the .env file
        $apiKey = config('services.gemini.api_key');
        $modelName = config('services.gemini.model');

        if (empty($apiKey)) {
            Log::error('Gemini API key is not configured.');
            return 'Gemini API key is not configured. Please set GEMINI_API_KEY in .env';
        }

        // Insert the correct model name into the URL
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key={$apiKey}";

        $systemInstruction = 'You are a helpful customer support AI assistant. You have access to tools to look up orders, tickets, create tickets, and search FAQ. Use them when the customer asks about their order, ticket, or general questions. Always respond in a friendly, concise way. You are speaking for the company.';

        $contents = [
            [
                'role' => 'user',
                'parts' => [['text' => $userQuery]],
            ],
        ];

        $tools = [GeminiToolDefinitions::getTools()];

        $round = 0;
        while ($round < $this->maxToolRounds) {
            $round++;

            $payload = [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 1024,
                ],
                'systemInstruction' => [
                    'parts' => [['text' => $systemInstruction]],
                ],
            ];
            
            if ($round === 1) {
                $payload['tools'] = $tools;
            }

            try {
                $response = Http::timeout(60)->post($url, $payload);
            } catch (\Throwable $e) {
                report($e);
                return 'Sorry, an error occurred: ' . $e->getMessage();
            }

            if (! $response->successful()) {
                $errBody = $response->json();
                $message = $errBody['error']['message'] ?? $response->body();
                return 'Gemini API error (' . $response->status() . '): ' . (is_string($message) ? $message : json_encode($message));
            }

            $body = $response->json();
            $candidate = $body['candidates'][0] ?? null;
            if (! $candidate || empty($candidate['content']['parts'])) {
                return 'I could not generate a response for that query.';
            }

            $parts = $candidate['content']['parts'];
            $modelContent = ['role' => 'model', 'parts' => $parts];
            $contents[] = $modelContent;

            $textPart = null;
            $functionCalls = [];

            foreach ($parts as $part) {
                if (isset($part['text'])) {
                    $textPart = $part['text'];
                }
                if (isset($part['functionCall'])) {
                    $functionCalls[] = $part['functionCall'];
                }
            }

            if ($textPart !== null && trim($textPart) !== '') {
                return trim($textPart);
            }

            if (empty($functionCalls)) {
                return 'I could not generate a response for that query.';
            }

            $functionResponseParts = [];
            foreach ($functionCalls as $fc) {
                $name = $fc['name'] ?? '';
                $args = $fc['args'] ?? [];
                $result = $executor->execute($name, $args);
                $functionResponseParts[] = [
                    'functionResponse' => [
                        'name' => $name,
                        'response' => $result,
                    ],
                ];
            }

            $contents[] = [
                'role' => 'user',
                'parts' => $functionResponseParts,
            ];
        }

        return 'I could not complete your request. Please try again.';
    }
}