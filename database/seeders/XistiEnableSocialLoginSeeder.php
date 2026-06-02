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

        $socialLogin = [
            'is_google_login' => 1,
            'is_facebook_login' => 1,
            'is_apple_login' => 1,
            'is_finger_login' => 1,
        ];
        $patch = [];
        foreach ($socialLogin as $column => $value) {
            if (Schema::hasColumn('general_settings', $column)) {
                $patch[$column] = $value;
            }
        }
        if ($patch !== []) {
            DB::table('general_settings')->where('id', 1)->update($patch);
        }
    }
}
