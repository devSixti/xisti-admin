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

    public function test_normalize_recipients_deduplicates_and_maps_login_device(): void
    {
        $normalized = FcmPushHelper::normalizeRecipients([
            ['device_token' => 'token-a', 'login_device' => 1],
            'token-b',
            ['device_token' => 'token-a', 'login_device' => 2],
            ['device_token' => '', 'login_device' => 1],
        ]);

        $this->assertSame([
            ['token' => 'token-a', 'login_device' => 1],
            ['token' => 'token-b', 'login_device' => null],
        ], $normalized);
    }

    public function test_send_to_topic_returns_null_for_empty_topic(): void
    {
        $this->assertNull(FcmPushHelper::sendToTopic('', 'Title', 'Body'));
    }
}
