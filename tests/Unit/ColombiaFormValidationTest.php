<?php

namespace Tests\Unit;

use App\Support\ColombiaFormValidation;
use PHPUnit\Framework\TestCase;

class ColombiaFormValidationTest extends TestCase
{
    public function test_normalize_colombian_mobile_strips_country_prefix(): void
    {
        $this->assertSame('3001234567', ColombiaFormValidation::normalizeColombianMobile('+573001234567', '+57'));
        $this->assertSame('3001234567', ColombiaFormValidation::normalizeColombianMobile('573001234567', 'CO'));
    }

    public function test_is_valid_colombian_mobile_rejects_short_numbers(): void
    {
        $this->assertFalse(ColombiaFormValidation::isValidColombianMobile('12345', '+57'));
    }

    public function test_format_sms_destination_returns_e164_for_colombia(): void
    {
        $this->assertSame(
            '+573001234567',
            ColombiaFormValidation::formatSmsDestination('+57', '3001234567')
        );
    }
}
