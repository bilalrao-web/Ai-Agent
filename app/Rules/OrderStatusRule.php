<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OrderStatusRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $allowed = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (! in_array($value, $allowed, true)) {
            $fail('The order status must be one of: ' . implode(', ', $allowed) . '.');
        }
    }
}
