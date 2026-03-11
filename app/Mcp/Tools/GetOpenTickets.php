<?php

namespace App\Mcp\Tools;

use App\Services\TicketService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class GetOpenTickets extends Tool
{
    public function __construct(
        protected TicketService $ticketService
    ) {}

    protected string $description = 'Get the customer\'s open or in-progress support tickets. Use when the user asks about ticket status, my tickets, or support request status.';

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'customer_id' => $schema->integer()
                ->description('Customer ID to look up tickets for.')
                ->required(),
        ];
    }

    public function handle(Request $request): Response
    {
        $customerId = (int) $request->get('customer_id', 0);

        if ($customerId <= 0) {
            return Response::error('Please provide a valid customer_id.');
        }

        $tickets = $this->ticketService->getOpenTickets($customerId);

        return Response::json([
            'count' => $tickets->count(),
            'tickets' => $tickets->map(fn ($t) => [
                'id' => $t->id,
                'issue_type' => $t->issue_type,
                'status' => $t->status,
                'created_at' => $t->created_at->toIso8601String(),
            ])->toArray(),
        ]);
    }
}
