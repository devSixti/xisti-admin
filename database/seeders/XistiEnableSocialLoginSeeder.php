<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class XistiEnableSocialLoginSeeder extends Seeder
{
    /**
     * Ensure social / biometric login toggles are enabled for mobile app-version-check.
     */
    public function run(): void
    {
        if (!Schema::hasTable('general_settings')) {
            return;
        }

        if (!DB::table('general_settings')->where('id', 1)->exists()) {
            $this->call(GeneralSettingsSeeder::class);

            return;
        }

        DB::table('general_settings')->where('id', 1)->update([
            'is_google_login' => 1,
            'is_facebook_login' => 1,
            'is_apple_login' => 1,
            'is_finger_login' => 1,
        ]);
    }
}
