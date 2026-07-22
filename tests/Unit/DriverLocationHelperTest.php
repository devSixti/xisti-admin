<?php

namespace Tests\Unit;

use App\Support\DriverLocationHelper;
use PHPUnit\Framework\TestCase;

class DriverLocationHelperTest extends TestCase
{
    public function test_valid_coords_require_non_zero_magnitude(): void
    {
        $this->assertTrue(DriverLocationHelper::isValid(6.24, -75.56));
        $this->assertFalse(DriverLocationHelper::isValid(0.0, 0.0));
        $this->assertFalse(DriverLocationHelper::isValid(null, 1.0));
    }
}
