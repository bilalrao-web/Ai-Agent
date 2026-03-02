<?php

namespace App\Services;

use App\Models\Order;

class OrderService
{
    public function getLatestOrder(int $customerId): ?Order
    {
        return Order::where('customer_id', $customerId)
            ->latest()
            ->first();
    }

    public function getOrderByNumber(int $customerId, string $orderNumber): ?Order
    {
        return Order::where('customer_id', $customerId)
            ->where('order_number', $orderNumber)
            ->first();
    }
}
