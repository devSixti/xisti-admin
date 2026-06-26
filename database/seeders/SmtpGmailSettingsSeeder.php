<?php

namespace Database\Seeders;

use App\Models\GeneralSettings;
use Illuminate\Database\Seeder;

class SmtpGmailSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = GeneralSettings::query()->first();
        if ($settings === null) {
            return;
        }

        $appPassword = (string) env('GMAIL_APP_PASSWORD', '');

        $settings->send_mail = 1;
        $settings->mail_site_name = 'XISTI App';
        $settings->email = 'soportexisti@gmail.com';
        $settings->send_receive_email = 'soportexisti@gmail.com';
        $settings->smtp_user_name = 'soportexisti@gmail.com';
        $settings->smtp_hostname = 'smtp.gmail.com';
        $settings->smtp_port = 465;
        $settings->smtp_encryption = 'ssl';

        if ($appPassword !== '') {
            $settings->smtp_password = $appPassword;
        }

        $settings->save();
    }
}
