<?php

namespace App\Rules;

use App\Support\ColombiaFormValidation;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ColombianMobileNumber implements ValidationRule
{
    public function __construct(private readonly ?string $countryCode = null)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!ColombiaFormValidation::isValidInternationalMobile($value, $this->countryCode)) {
            $fail(__('user_messages.385'));
        }
    }
}
