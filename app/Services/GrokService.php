<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GrokService
{
    protected int $maxToolRounds = 5;

    protected string $baseUrl = 'https://api.x.ai/v1';

    /**
     * Generate response using Grok (x.ai) function calling. Uses /v1/responses API.
     */
    public function generateWithToolCalling(string $userQuery, int $customerId, GeminiToolExecutor $executor): string
    {
        $apiKey = config('services.grok.api_key');
        if (empty($apiKey)) {
            return 'Grok API key is not configured. Please set XAI_API_KEY in .env';
        }

        $model = config('services.grok.model', 'grok-4-latest');
        $url = "{$this->baseUrl}/responses";

        $systemInstruction = 'You are a helpful customer support AI assistant. You have access to tools to look up orders, tickets, create tickets, and search FAQ. Use them when the customer asks about their order, ticket, or general questions. Always respond in a friendly, concise way. You are speaking for the company.';

        $input = [
            ['role' => 'system', 'content' => $systemInstruction],
            ['role' => 'user', 'content' => $userQuery],
        ];
        $tools = GrokToolDefinitions::getTools();

        $round = 0;
        $previousResponseId = null;
        $nextInput = null;

        while ($round < $this->maxToolRounds) {
            $round++;

            $payload = [
                'model' => $model,
                'tools' => $tools,
            ];
            if ($round === 1) {
                $payload['input'] = $input;
            } else {
                $payload['previous_response_id'] = $previousResponseId;
                $payload['input'] = $nextInput;
            }

            try {
                $response = Http::timeout(60)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $apiKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->post($url, $payload);
            } catch (\Throwable $e) {
                report($e);
                return 'Sorry, an error occurred: ' . $e->getMessage();
            }

            if (! $response->successful()) {
                $errBody = $response->json();
                $message = $errBody['error']['message'] ?? $response->body();
                return 'Grok API error (' . $response->status() . '): ' . (is_string($message) ? $message : json_encode($message));
            }

            $body = $response->json();
            $previousResponseId = $body['id'] ?? null;
            $output = $body['output'] ?? [];

            $textContent = null;
            $functionCalls = [];

            foreach ($output as $item) {
                $type = $item['type'] ?? '';
                if ($type === 'message') {
                    $content = $item['content'] ?? [];
                    if (is_array($content) && isset($content[0]['text'])) {
                        $textContent = $content[0]['text'];
                    } elseif (is_string($content)) {
                        $textContent = $content;
                    }
                }
                if ($type === 'function_call') {
                    $functionCalls[] = [
                        'call_id' => $item['call_id'] ?? '',
                        'name' => $item['name'] ?? '',
                        'arguments' => $item['arguments'] ?? '{}',
                    ];
                }
            }

            if ($textContent !== null && trim($textContent) !== '') {
                return trim($textContent);
            }

            if (empty($functionCalls)) {
                return 'I could not generate a response for that query.';
            }

            $nextInput = [];
            foreach ($functionCalls as $fc) {
                $name = $fc['name'];
                $args = json_decode($fc['arguments'], true) ?? [];
                $result = $executor->execute($name, $args);
                $nextInput[] = [
                    'type' => 'function_call_output',
                    'call_id' => $fc['call_id'],
                    'output' => json_encode($result),
                ];
            }
        }

        return 'I could not complete your request. Please try again.';
    }
}
