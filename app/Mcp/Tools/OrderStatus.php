<?php

namespace App\Mcp\Tools;

use App\Models\Order;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class OrderStatus extends Tool
{
    protected string $description = 'Get order status for a customer. Use when the user asks about order status, my order, delivery, or tracking. Provide customer_id to get their orders.';

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'customer_id' => $schema->integer()
                ->description('Customer ID to look up orders for.')
                ->required(),
        ];
    }

    public function handle(Request $request): Response
    {
        $customerId = (int) $request->get('customer_id', 0);

        if ($customerId <= 0) {
            return Response::error('Please provide a valid customer_id.');
        }

        $orders = Order::where('customer_id', $customerId)
            ->latest()
            ->take(10)
            ->get(['id', 'order_number', 'status', 'delivery_date', 'amount']);

        return Response::json([
            'customer_id' => $customerId,
            'orders' => $orders->map(fn (Order $order) => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'delivery_date' => $order->delivery_date?->toDateString(),
                'amount' => (float) $order->amount,
            ])->toArray(),
        ]);
    }
}
