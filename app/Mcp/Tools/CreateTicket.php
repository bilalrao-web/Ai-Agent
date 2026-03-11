<?php

namespace App\Mcp\Tools;

use App\Services\TicketService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class CreateTicket extends Tool
{
    public function __construct(
        protected TicketService $ticketService
    ) {}

    protected string $description = 'Create a new support ticket for the customer. Use when the user reports an issue, wants to complain, or needs to log a problem (e.g. damaged product, wrong item, refund).';

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'customer_id' => $schema->integer()
                ->description('Customer ID to create the ticket for.')
                ->required(),
            'issue_type' => $schema->string()
                ->description('Short category e.g. Damaged product, Wrong item, Refund request, Delivery delay.')
                ->required(),
            'description' => $schema->string()
                ->description('Detailed description of the issue.')
                ->required(),
        ];
    }

    public function handle(Request $request): Response
    {
        $customerId = (int) $request->get('customer_id', 0);
        $issueType = (string) $request->get('issue_type', 'General');
        $description = (string) $request->get('description', '');

        if ($customerId <= 0) {
            return Response::error('Please provide a valid customer_id.');
        }

        $ticket = $this->ticketService->createTicket($customerId, $issueType, $description);

        return Response::json([
            'success' => true,
            'ticket_id' => $ticket->id,
            'message' => 'Support ticket created successfully. Our team will look into it.',
        ]);
    }
}
