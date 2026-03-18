<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TicketStatusRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $allowed = ['open', 'in_progress', 'resolved', 'closed'];

        if (! in_array($value, $allowed, true)) {
            $fail('The ticket status must be one of: ' . implode(', ', $allowed) . '.');
        }
    }
}
