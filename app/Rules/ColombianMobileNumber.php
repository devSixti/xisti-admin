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
        if ($this->countryCode !== null && !ColombiaFormValidation::isColombiaCountryCode($this->countryCode)) {
            return;
        }

        if (!ColombiaFormValidation::isValidColombianMobile($value, $this->countryCode)) {
            $fail(__('user_messages.385'));
        }
    }
}
