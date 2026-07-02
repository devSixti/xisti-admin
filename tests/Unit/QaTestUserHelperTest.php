<?php

namespace Tests\Unit;

use App\Models\User;
use App\Support\LocalOtpBypass;
use App\Support\QaTestUserHelper;
use Database\Seeders\XistiQaTestUserSeeder;
use Tests\TestCase;

class QaTestUserHelperTest extends TestCase
{
    private function makeUser(string $phone, int $fixUserShow = 1, int $isDefaultUser = 1): User
    {
        $user = new User();
        $user->contact_number = $phone;
        $user->country_code = XistiQaTestUserSeeder::QA_COUNTRY_CODE;
        $user->fix_user_show = $fixUserShow;
        $user->is_default_user = $isDefaultUser;

        return $user;
    }

    public function test_accepts_fixed_otp_for_rider_and_driver_qa_phones(): void
    {
        foreach (QaTestUserHelper::qaPhoneLocals() as $phone) {
            $user = $this->makeUser($phone);
            $this->assertTrue(QaTestUserHelper::isQaUser($user), "Expected QA user for {$phone}");
            $this->assertTrue(QaTestUserHelper::acceptsFixedOtp($user, LocalOtpBypass::FIXED_OTP));
            $this->assertFalse(QaTestUserHelper::acceptsFixedOtp($user, '000000'));
        }
    }

    public function test_flags_alone_do_not_grant_qa_otp_bypass(): void
    {
        $user = $this->makeUser('3001111111');
        $this->assertFalse(QaTestUserHelper::isQaUser($user));
        $this->assertFalse(QaTestUserHelper::acceptsFixedOtp($user, LocalOtpBypass::FIXED_OTP));
    }
}
