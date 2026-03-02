<?php

namespace App\Services;

class GrokToolDefinitions
{
    /**
     * Tool definitions for x.ai Grok (OpenAI-style function calling).
     *
     * @return array<int, array{type: string, name: string, description: string, parameters: array}>
     */
    public static function getTools(): array
    {
        return [
            [
                'type' => 'function',
                'name' => 'get_latest_order',
                'description' => 'Get the latest order for the customer. Use when customer asks about order status, my order, latest order, or delivery status.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => (object) [],
                ],
            ],
            [
                'type' => 'function',
                'name' => 'get_order_by_number',
                'description' => 'Get a specific order by order number. Use when customer provides an order number or reference.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'order_number' => [
                            'type' => 'string',
                            'description' => 'The order number (e.g. ORD-C1-1)',
                        ],
                    ],
                    'required' => ['order_number'],
                ],
            ],
            [
                'type' => 'function',
                'name' => 'get_open_tickets',
                'description' => "Get the customer's open or in-progress support tickets. Use when customer asks about ticket status, my tickets, or support request status.",
                'parameters' => [
                    'type' => 'object',
                    'properties' => (object) [],
                ],
            ],
            [
                'type' => 'function',
                'name' => 'create_ticket',
                'description' => 'Create a new support ticket for the customer. Use when customer reports an issue, wants to complain, or needs to log a problem (e.g. damaged product, wrong item, refund).',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'issue_type' => [
                            'type' => 'string',
                            'description' => 'Short category e.g. Damaged product, Wrong item, Refund request, Delivery delay',
                        ],
                        'description' => [
                            'type' => 'string',
                            'description' => 'Detailed description of the issue',
                        ],
                    ],
                    'required' => ['issue_type', 'description'],
                ],
            ],
            [
                'type' => 'function',
                'name' => 'search_faq',
                'description' => 'Search FAQ for common questions like working hours, return policy, how to track order, how to create a ticket. Use for general support questions.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => [
                            'type' => 'string',
                            'description' => 'Search query or topic (e.g. working hours, return policy)',
                        ],
                    ],
                    'required' => ['query'],
                ],
            ],
        ];
    }
}
