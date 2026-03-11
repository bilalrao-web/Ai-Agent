<?php

namespace App\Mcp\Tools;

use App\Services\OrderService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class GetLatestOrder extends Tool
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    protected string $description = 'Get the latest order for a customer. Use when the user asks about order status, my order, latest order, or delivery status.';

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'customer_id' => $schema->integer()
                ->description('Customer ID to look up the latest order for.')
                ->required(),
        ];
    }

    public function handle(Request $request): Response
    {
        $customerId = (int) $request->get('customer_id', 0);

        if ($customerId <= 0) {
            return Response::error('Please provide a valid customer_id.');
        }

        $order = $this->orderService->getLatestOrder($customerId);

        if (! $order) {
            return Response::json([
                'found' => false,
                'message' => 'No orders found for this customer.',
            ]);
        }

        return Response::json([
            'found' => true,
            'order' => [
                'order_number' => $order->order_number,
                'status' => $order->status,
                'delivery_date' => $order->delivery_date?->toDateString(),
                'amount' => (float) $order->amount,
            ],
        ]);
    }
}
