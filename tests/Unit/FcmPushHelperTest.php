<?php

namespace Tests\Unit;

use App\Helpers\FcmPushHelper;
use PHPUnit\Framework\TestCase;

class FcmPushHelperTest extends TestCase
{
    public function test_android_data_only_applies_only_to_android_login_device(): void
    {
        $ridePayload = ['notification_type' => '7'];

        $this->assertTrue(FcmPushHelper::shouldUseAndroidDataOnly(1, $ridePayload));
        $this->assertFalse(FcmPushHelper::shouldUseAndroidDataOnly(2, $ridePayload));
        $this->assertFalse(FcmPushHelper::shouldUseAndroidDataOnly(0, $ridePayload));
        $this->assertFalse(FcmPushHelper::shouldUseAndroidDataOnly(null, $ridePayload));
    }
}
