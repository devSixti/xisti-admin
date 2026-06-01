<?php

namespace App\Rules;

use App\Support\ColombiaFormValidation;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ColombianVehiclePlate implements ValidationRule
{
    public function __construct(private readonly ?int $vehicleTypeId = null)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!ColombiaFormValidation::isValidVehiclePlate($value, $this->vehicleTypeId)) {
            $fail(__('driver_messages.371'));
        }
    }
}
