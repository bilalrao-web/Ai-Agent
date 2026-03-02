<?php

namespace App\Services;

class QueryProcessorService
{
    public array $simulatedQueries = [
        'order_status' => 'What is the status of my latest order?',
        'ticket_creation' => 'I have an issue, my delivered product is damaged.',
        'ticket_status' => 'Can you check the status of my open ticket?',
        'general_faq' => 'What are your customer support working hours?',
    ];

    public function __construct(
        protected GeminiService $geminiService,
        protected OrderService $orderService,
        protected TicketService $ticketService,
        protected CallLogService $callLogService
    ) {}

    /**
     * Process a query using Gemini tool calling. queryType can be a key from simulatedQueries or any free text.
     */
    public function process(string $queryType, int $customerId): array
    {
        $query = $this->simulatedQueries[$queryType] ?? $queryType;

        $executor = new GeminiToolExecutor($customerId, $this->orderService, $this->ticketService);
        $response = $this->geminiService->generateWithToolCalling($query, $customerId, $executor);

        $callLog = $this->callLogService->createLog($customerId, $query);
        $this->callLogService->addMessage($callLog->id, 'user', $query);
        $this->callLogService->addMessage($callLog->id, 'assistant', $response);

        return [
            'query' => $query,
            'response' => $response,
            'call_log_id' => $callLog->id,
        ];
    }
}
