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
        $settings->mail_site_name = 'XISTI';
        $settings->email = (string) config('xisti.mail.support_address', 'soporte@xistiapp.com');
        $settings->send_receive_email = (string) config('xisti.mail.support_address', 'soporte@xistiapp.com');
        $settings->smtp_user_name = (string) config('xisti.mail.from_address', 'noreply@xistiapp.com');
        $settings->smtp_hostname = 'smtp.gmail.com';
        $settings->smtp_port = 465;
        $settings->smtp_encryption = 'ssl';

        if ($appPassword !== '') {
            $settings->smtp_password = $appPassword;
        }

        $settings->save();
    }
}
