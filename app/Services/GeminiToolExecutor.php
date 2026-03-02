<?php

namespace App\Services;

use App\Models\Faq;

class GeminiToolExecutor
{
    public function __construct(
        protected int $customerId,
        protected OrderService $orderService,
        protected TicketService $ticketService
    ) {}

    /**
     * Execute a tool by name with the given args. Returns a JSON-serializable array for Gemini.
     */
    public function execute(string $name, array $args): array
    {
        return match ($name) {
            'get_latest_order' => $this->getLatestOrder(),
            'get_order_by_number' => $this->getOrderByNumber($args['order_number'] ?? ''),
            'get_open_tickets' => $this->getOpenTickets(),
            'create_ticket' => $this->createTicket(
                $args['issue_type'] ?? 'General',
                $args['description'] ?? ''
            ),
            'search_faq' => $this->searchFaq($args['query'] ?? ''),
            default => ['error' => "Unknown tool: {$name}"],
        };
    }

    protected function getLatestOrder(): array
    {
        $order = $this->orderService->getLatestOrder($this->customerId);
        if (! $order) {
            return ['found' => false, 'message' => 'No orders found for this customer.'];
        }
        return [
            'found' => true,
            'order' => [
                'order_number' => $order->order_number,
                'status' => $order->status,
                'delivery_date' => $order->delivery_date?->toDateString(),
                'amount' => $order->amount,
            ],
        ];
    }

    protected function getOrderByNumber(string $orderNumber): array
    {
        $order = $this->orderService->getOrderByNumber($this->customerId, $orderNumber);
        if (! $order) {
            return ['found' => false, 'message' => "Order {$orderNumber} not found for this customer."];
        }
        return [
            'found' => true,
            'order' => [
                'order_number' => $order->order_number,
                'status' => $order->status,
                'delivery_date' => $order->delivery_date?->toDateString(),
                'amount' => $order->amount,
            ],
        ];
    }

    protected function getOpenTickets(): array
    {
        $tickets = $this->ticketService->getOpenTickets($this->customerId);
        return [
            'count' => $tickets->count(),
            'tickets' => $tickets->map(fn ($t) => [
                'id' => $t->id,
                'issue_type' => $t->issue_type,
                'status' => $t->status,
                'created_at' => $t->created_at->toIso8601String(),
            ])->toArray(),
        ];
    }

    protected function createTicket(string $issueType, string $description): array
    {
        $ticket = $this->ticketService->createTicket($this->customerId, $issueType, $description);
        return [
            'success' => true,
            'ticket_id' => $ticket->id,
            'message' => 'Support ticket created successfully. Our team will look into it.',
        ];
    }

    protected function searchFaq(string $query): array
    {
        $faqs = Faq::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('question', 'like', "%{$query}%")
                    ->orWhere('answer', 'like', "%{$query}%");
            })
            ->get(['question', 'answer']);
        if ($faqs->isEmpty()) {
            return ['found' => false, 'message' => 'No matching FAQ found.'];
        }
        return [
            'found' => true,
            'faqs' => $faqs->map(fn ($f) => ['question' => $f->question, 'answer' => $f->answer])->toArray(),
        ];
    }
}
