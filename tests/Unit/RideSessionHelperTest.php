<?php

namespace Tests\Unit;

use App\Helpers\RideSessionHelper;
use PHPUnit\Framework\TestCase;

class RideSessionHelperTest extends TestCase
{
    public function test_terminal_statuses_exclude_settlement_phases(): void
    {
        $this->assertContains(4, RideSessionHelper::TERMINAL_STATUSES);
        $this->assertContains(9, RideSessionHelper::TERMINAL_STATUSES);
        $this->assertNotContains(7, RideSessionHelper::TERMINAL_STATUSES);
        $this->assertNotContains(8, RideSessionHelper::TERMINAL_STATUSES);
    }

    public function test_active_trip_statuses_cover_in_progress_flow(): void
    {
        $this->assertContains(5, RideSessionHelper::ACTIVE_TRIP_STATUSES);
        $this->assertContains(6, RideSessionHelper::ACTIVE_TRIP_STATUSES);
        $this->assertNotContains(8, RideSessionHelper::ACTIVE_TRIP_STATUSES);
    }

    public function test_settlement_statuses_are_payment_and_rating(): void
    {
        $this->assertSame([7, 8], RideSessionHelper::SETTLEMENT_STATUSES);
    }
}
