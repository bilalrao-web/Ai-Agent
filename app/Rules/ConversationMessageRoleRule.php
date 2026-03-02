<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ConversationMessageRoleRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $allowed = ['user', 'assistant', 'system'];
        if (! in_array($value, $allowed, true)) {
            $fail('The message role must be one of: user, assistant, system.');
        }
    }
}
