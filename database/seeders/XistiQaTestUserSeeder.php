<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * QA passenger with OTP bypass: use OTP 123456 when the app asks for SMS code.
 * Phone login always sends you to OTP; Twilio is skipped for fix_user_show users.
 */
class XistiQaTestUserSeeder extends Seeder
{
    public const QA_PHONE_LOCAL = '3001234567';

    public const QA_DRIVER_PHONE_LOCAL = '3009876543';

    public const QA_COUNTRY_CODE = '+57';

    public const QA_OTP = '123456';

    public function run(): void
    {
        if (! $this->shouldRun()) {
            $this->command?->warn('XistiQaTestUserSeeder skipped (set XISTI_SEED_QA_USER=1 to run in production).');

            return;
        }

        $user = User::query()
            ->where('contact_number', self::QA_PHONE_LOCAL)
            ->whereIn('country_code', ['+57', '57'])
            ->whereNull('deleted_at')
            ->first();

        if ($user === null) {
            $user = new User();
            $user->contact_number = self::QA_PHONE_LOCAL;
            $user->country_code = self::QA_COUNTRY_CODE;
            $user->login_type = 'email';
            $user->status = 1;
            $user->save();
        }

        $user->first_name = 'Usuario';
        $user->last_name = 'QA XISTI';
        $user->email = 'qa.pasajero@xistiapp.com';
        $user->login_type = 'email';
        $user->login_id = null;
        $user->country_code = self::QA_COUNTRY_CODE;
        $user->contact_number = self::QA_PHONE_LOCAL;
        $user->currency = 'COP';
        $user->language = 'es';
        $user->status = 1;
        $user->is_register = 1;
        $user->verified_at = now();
        $user->fix_user_show = 1;
        $user->is_default_user = 1;
        $user->active_mode = 1;
        $user->is_driver_type = 0;
        $user->device_token = $user->device_token ?: 'qa-device-token';
        $user->save();

        $user->generateAccessToken($user->id);
        if (empty($user->invite_code)) {
            $user->InviteCode($user->id, $user->first_name);
        }

        $this->command?->info('XISTI QA user ready.');
        $this->command?->info('  Phone: '.self::QA_COUNTRY_CODE.' '.self::QA_PHONE_LOCAL);
        $this->command?->info('  Login: enter number → Send OTP → code '.self::QA_OTP);
        $this->command?->info('  user_id: '.$user->id);
    }

    private function shouldRun(): bool
    {
        if (env('XISTI_SEED_QA_USER') === '1' || env('XISTI_SEED_QA_USER') === 'true') {
            return true;
        }

        return app()->environment(['local', 'staging']);
    }
}
