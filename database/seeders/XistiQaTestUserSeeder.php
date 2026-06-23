<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * QA passenger + driver with OTP bypass 123456 (QaTestUserHelper).
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

        $rider = $this->seedRider();
        $driver = $this->seedDriver();

        $this->command?->info('XISTI QA users ready.');
        $this->command?->info('  Rider: '.self::QA_COUNTRY_CODE.' '.self::QA_PHONE_LOCAL.' (id '.$rider->id.')');
        $this->command?->info('  Driver: '.self::QA_COUNTRY_CODE.' '.self::QA_DRIVER_PHONE_LOCAL.' (id '.$driver->id.')');
        $this->command?->info('  OTP for both: '.self::QA_OTP);
    }

    private function seedRider(): User
    {
        $user = User::query()
            ->where('contact_number', self::QA_PHONE_LOCAL)
            ->whereIn('country_code', ['+57', '57'])
            ->whereNull('deleted_at')
            ->orderByDesc('is_register')
            ->orderByDesc('id')
            ->first();

        if ($user === null) {
            $user = new User();
            $user->contact_number = self::QA_PHONE_LOCAL;
            $user->country_code = self::QA_COUNTRY_CODE;
            $user->login_type = 'email';
            $user->status = 1;
            $user->save();
        }

        $user->first_name = 'Laura';
        $user->last_name = 'Vega';
        $user->email = 'qa.pasajero@xistiapp.com';
        $user->login_type = 'email';
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
        $user->device_token = $user->device_token ?: 'qa-rider-device';
        $user->save();
        $user->generateAccessToken($user->id);
        if (empty($user->invite_code)) {
            $user->InviteCode($user->id, $user->first_name);
        }

        return $user;
    }

    private function seedDriver(): User
    {
        $user = User::query()
            ->where('contact_number', self::QA_DRIVER_PHONE_LOCAL)
            ->whereIn('country_code', ['+57', '57'])
            ->whereNull('deleted_at')
            ->orderByDesc('is_register')
            ->orderByDesc('id')
            ->first();

        if ($user === null) {
            $user = new User();
            $user->contact_number = self::QA_DRIVER_PHONE_LOCAL;
            $user->country_code = self::QA_COUNTRY_CODE;
            $user->login_type = 'email';
            $user->status = 1;
            $user->save();
        }

        $user->first_name = 'Carlos';
        $user->last_name = 'Mendoza';
        $user->email = 'qa.conductor@xistiapp.com';
        $user->login_type = 'email';
        $user->country_code = self::QA_COUNTRY_CODE;
        $user->contact_number = self::QA_DRIVER_PHONE_LOCAL;
        $user->currency = 'COP';
        $user->language = 'es';
        $user->status = 1;
        $user->is_register = 1;
        $user->verified_at = now();
        $user->fix_user_show = 1;
        $user->is_default_user = 1;
        $user->is_driver_type = 1;
        $user->is_driver_status = 1;
        $user->driver_vehicle_status = 1;
        $user->driver_doc_status = 1;
        $user->driver_current_status = 1;
        $user->active_mode = 2;
        $user->device_token = $user->device_token ?: 'qa-driver-device';
        $user->save();
        $user->generateAccessToken($user->id);
        if (empty($user->invite_code)) {
            $user->InviteCode($user->id, $user->first_name);
        }

        $carTypeId = (int) DB::table('transport_vehicle_type')->where('service_id', 1)->value('id');
        if ($carTypeId <= 0) {
            $carTypeId = (int) DB::table('transport_vehicle_type')->insertGetId([
                'service_id' => 1,
                'name' => 'Sedán',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $payload = [
            'vehicle_type_id' => $carTypeId,
            'current_lat' => 4.6243,
            'current_long' => -74.0636,
            'vehicle_company' => 'Chevrolet',
            'plat_no' => 'MDE123',
            'model_year' => 2022,
            'model_name' => 'Spark GT',
            'vehicle_color' => 'Blanco',
            'doc_status' => 1,
            'accept_transport' => 1,
            'accept_delivery' => 1,
            'accept_encomiendas' => 1,
            'search_distance_filter' => 50,
            'updated_at' => now(),
        ];

        if (DB::table('transport_driver_details')->where('user_id', $user->id)->exists()) {
            DB::table('transport_driver_details')->where('user_id', $user->id)->update($payload);
        } else {
            $payload['user_id'] = $user->id;
            $payload['created_at'] = now();
            DB::table('transport_driver_details')->insert($payload);
        }

        return $user;
    }

    private function shouldRun(): bool
    {
        if (env('XISTI_SEED_QA_USER') === '1' || env('XISTI_SEED_QA_USER') === 'true') {
            return true;
        }

        return app()->environment(['local', 'staging']);
    }
}
