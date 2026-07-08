<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('SUPER_ADMIN_SEED_PASSWORD');
        if (! $password) {
            $password = Str::password(20);
            $this->command?->warn('SUPER_ADMIN_SEED_PASSWORD not set; generated random password for seed run only.');
        }

        $super_admin_record = [
            [
                'id' => 1,
                'name' => 'Super Admin',
                'email' => env('SUPER_ADMIN_SEED_EMAIL', 'admin@xistiapp.com'),
                'password' => Hash::make($password),
                'roles' => 1,
                'area_id' => 0,
                'is_restrict_admin' => 0,
                'admin_type' => 's',
                'status' => 1,
                'must_change_password' => env('SUPER_ADMIN_SEED_PASSWORD') ? 0 : 1,
                'access_token' => '',
                'device_token' => '',
                'remember_token' => Str::random(60),
            ],
        ];

        DB::table('super_admin')->upsert(
            $super_admin_record,
            ['id'],
            ['name', 'email', 'password', 'roles', 'area_id', 'is_restrict_admin', 'admin_type', 'status', 'must_change_password', 'access_token', 'device_token', 'remember_token']
        );
    }
}
