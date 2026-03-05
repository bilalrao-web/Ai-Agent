<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    protected int $maxToolRounds = 5;

    public function generateResponse(string $userQuery, array $contextData = [], array $history = []): string
    {
        $apiKey = config('services.gemini.api_key');
        if (empty($apiKey)) {
            return 'Gemini API key is not configured. Please set GEMINI_API_KEY in .env';
        }

        $model = config('services.gemini.model', 'gemini-2.0-flash');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $systemInstruction = 'You are a friendly customer support AI agent speaking over the phone. '
            . 'Keep responses short and clear (1–2 sentences) so they work well for voice. '
            . 'Customer context: ' . json_encode($contextData);

        $contents = [];
        foreach ($history as $msg) {
            $role = ($msg['role'] ?? '') === 'assistant' ? 'model' : 'user';
            $contents[] = ['role' => $role, 'parts' => [['text' => $msg['content'] ?? '']]];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $userQuery]]];

        try {
            $response = Http::timeout(30)->post($url, [
                'contents' => $contents,
                'systemInstruction' => ['parts' => [['text' => $systemInstruction]]],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 512,
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);
            return 'Sorry, an error occurred. Please try again later.';
        }

        if (! $response->successful()) {
            return 'Sorry, the AI service is temporarily unavailable. Please try again later.';
        }

        $body = $response->json();
        $text = $body['candidates'][0]['content']['parts'][0]['text'] ?? '';
        return trim($text) ?: 'I could not generate a response for that. Is there anything else I can help with?';
    }

    public function generateWithToolCalling(string $userQuery, int $customerId, GeminiToolExecutor $executor): string
    {
        $apiKey = config('services.gemini.api_key');
        if (empty($apiKey)) {
            return 'Gemini API key is not configured. Please set GEMINI_API_KEY in .env';
        }

        $model = config('services.gemini.model', 'gemini-2.0-flash');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

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
