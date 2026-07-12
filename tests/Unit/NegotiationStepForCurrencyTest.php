<?php

namespace Tests\Unit;

use App\Helpers\AppMobileSettingsHelper;
use App\Models\WorldCurrency;
use PHPUnit\Framework\TestCase;

class NegotiationStepForCurrencyTest extends TestCase
{
    public function test_cop_keeps_admin_step(): void
    {
        $currency = new WorldCurrency();
        $currency->currency_code = 'COP';
        $currency->ratio = 1;
        $this->assertSame(500, AppMobileSettingsHelper::negotiationStepForCurrency(null, $currency));
    }

    public function test_usd_scales_cop_step_to_at_least_one(): void
    {
        $currency = new WorldCurrency();
        $currency->currency_code = 'USD';
        $currency->ratio = 0.000278;
        $this->assertSame(1, AppMobileSettingsHelper::negotiationStepForCurrency(null, $currency));
    }
}
