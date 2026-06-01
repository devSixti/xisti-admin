<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ColombianNationalId implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $digits = preg_replace('/\D/', '', (string) $value);

        if (!preg_match('/^[0-9]{6,10}$/', $digits)) {
            $fail(__('user_messages.387'));
        }
    }
}
