<?php

namespace Tests\Unit;

use App\Models\User;
use App\Support\LocalOtpBypass;
use App\Support\QaTestUserHelper;
use Database\Seeders\XistiQaTestUserSeeder;
use Tests\TestCase;

class QaTestUserHelperTest extends TestCase
{
    public function test_accepts_fixed_otp_for_rider_and_driver_qa_phones(): void
    {
        foreach (QaTestUserHelper::qaPhoneLocals() as $phone) {
            $user = new User([
                'contact_number' => $phone,
                'country_code' => XistiQaTestUserSeeder::QA_COUNTRY_CODE,
                'fix_user_show' => 1,
                'is_default_user' => 1,
            ]);
            $this->assertTrue(QaTestUserHelper::isQaUser($user), "Expected QA user for {$phone}");
            $this->assertTrue(QaTestUserHelper::acceptsFixedOtp($user, LocalOtpBypass::FIXED_OTP));
            $this->assertFalse(QaTestUserHelper::acceptsFixedOtp($user, '000000'));
        }
    }

    public function test_flags_alone_do_not_grant_qa_otp_bypass(): void
    {
        $user = new User([
            'contact_number' => '3001111111',
            'country_code' => XistiQaTestUserSeeder::QA_COUNTRY_CODE,
            'fix_user_show' => 1,
            'is_default_user' => 1,
        ]);
        $this->assertFalse(QaTestUserHelper::isQaUser($user));
        $this->assertFalse(QaTestUserHelper::acceptsFixedOtp($user, LocalOtpBypass::FIXED_OTP));
    }
}
