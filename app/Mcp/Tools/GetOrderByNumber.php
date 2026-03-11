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
class GetOrderByNumber extends Tool
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    protected string $description = 'Get a specific order by order number for a customer. Use when the user provides an order number or reference.';

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'customer_id' => $schema->integer()
                ->description('Customer ID.')
                ->required(),
            'order_number' => $schema->string()
                ->description('The order number (e.g. ORD-C1-1).')
                ->required(),
        ];
    }

    public function handle(Request $request): Response
    {
        $customerId = (int) $request->get('customer_id', 0);
        $orderNumber = (string) $request->get('order_number', '');

        if ($customerId <= 0 || $orderNumber === '') {
            return Response::error('Please provide a valid customer_id and order_number.');
        }

        $order = $this->orderService->getOrderByNumber($customerId, $orderNumber);

        if (! $order) {
            return Response::json([
                'found' => false,
                'message' => "Order {$orderNumber} not found for this customer.",
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
