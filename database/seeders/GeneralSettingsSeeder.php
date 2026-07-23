<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GeneralSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $general_settings_record =[
            'id' => 1,
            'website_name' => 'XISTI',
            'website_logo' => 'xisti-logo.png',
            'website_favicon' => 'xisti-favicon.png',
            'address' => 'Medellín, Antioquia, Colombia.',
            'contact_no' => '+57 3000000000',
            'email' => 'soporte@xistiapp.com',
            'send_receive_email' => 'soporte@xistiapp.com',
            'site_url' => 'https://admin.xistiapp.com/',
            'copy_right' => '© Copyright 2026 XISTI App',
            'facebook_link' => 'https://www.facebook.com/',
            'instagram_link' => 'https://www.instagram.com/accounts/login/',
            'linkedin_link' => 'https://www.linkedin.com/login',
            'map_key' => 'CHANGE_ME_GOOGLE_MAPS_BROWSER_KEY',
            'server_map_key' => 'CHANGE_ME_GOOGLE_MAPS_SERVER_KEY',
            'fcm_user_topic_name' => 'XistiUser',
            'fcm_driver_topic_name' => 'XistiDriver',
            'user_playstore_link' => NULL,
            'user_appstore_link' => NULL,
            'driver_delivery_playstore_link' => NULL,
            'driver_delivery_appstore_link' => NULL,
            'used_user_discount' => 20,
            'used_user_discount_type' => 1,
            'refer_user_discount' => 10,
            'refer_user_discount_type' => 1,
            'about_us_youtube_link' => NULL,
            'user_timeout' => 40,
            'driver_algorithm' => 0,
            'max_driver_reassign' => 1,
            'day_allow' => 0,
            'send_mail' => 1,
            'mail_site_name' => 'XISTI',
            'smtp_user_name' => 'noreply@xistiapp.com',
            'smtp_password' => 'CHANGE_ME',
            'smtp_hostname' => 'smtp.googlemail.com',
            'smtp_port' => '465',
            'smtp_encryption' => 'ssl',
            'is_otp_verification' => 0,
            'otp_method' => 0,
            'rounding_amount_module' => 0,
            'cash_payment' => 1,
            'card_payment' => 1,
            'wallet_payment' => 1,
            'auto_settle_wallet' => 0,
            'ride_eta_consider' => 0,
            'app_key' => env('XISTI_APP_KEY', 'XistiChangeThisAppKeyBeforeProduction'),
            'ride_otp' => '1',
            'address_lat' => '6.2442',
            'address_long' => '-75.5812',
            'login_timeout_time' => 525600,
            'fcm_bearer_token' => NULL,
            'fcm_bearer_token_expiry_date' => NULL,
            'fcm_bearer_token_expiry_mins' => 55,
            'auto_approve' => 0,
            'min_cashout' => 0,
            'max_cashout' => 0,
            'driver_min_amount' => 0,
            'is_google_login' => 1,
            'is_facebook_login' => 1,
            'is_apple_login' => 1,
            'is_finger_login' => 1,
            'twilio_service_key' => null,
            'twilio_auth_token' => null,
            'twilio_verify_service_key' => null,
//            'created_at' => NULL,
//            'updated_at' => NULL,
        ];
        $record = [];
        foreach ($general_settings_record as $column => $value) {
            if ($column === 'id' || Schema::hasColumn('general_settings', $column)) {
                $record[$column] = $value;
            }
        }

        DB::table('general_settings')->updateOrInsert(['id' => 1], $record);

        $xistiColumns = [
            'fare_negotiation_step' => (int) config('xisti.fare_negotiation_step_cop', 500),
            'vat_rate_on_commission' => 19.00,
            'driver_cancel_until_status' => 3,
            'enable_expreso_mobile' => 0,
            'enable_encomiendas_mobile' => 1,
            'enable_xisti_new_home_layout' => 1,
        ];
        $patch = [];
        foreach ($xistiColumns as $column => $value) {
            if (Schema::hasColumn('general_settings', $column)) {
                $patch[$column] = $value;
            }
        }
        if ($patch !== []) {
            DB::table('general_settings')->where('id', 1)->update($patch);
        }
    }
}
