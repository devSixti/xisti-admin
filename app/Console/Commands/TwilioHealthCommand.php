<?php

namespace App\Console\Commands;

use App\Models\GeneralSettings;
use App\Support\TwilioSettings;
use Illuminate\Console\Command;
use Twilio\Rest\Client;

class TwilioHealthCommand extends Command
{
    protected $signature = 'twilio:health
                            {--sync : Copy env/config Twilio credentials into general_settings}';

    protected $description = 'Check Twilio account status for OTP Verify (never prints secrets)';

    public function handle(): int
    {
        $settings = GeneralSettings::query()->first();
        if ($settings === null) {
            $this->error('general_settings row missing.');

            return self::FAILURE;
        }

        if ($this->option('sync')) {
            $sid = trim((string) config('services.twilio.account_sid', ''));
            $token = trim((string) config('services.twilio.auth_token', ''));
            $verify = trim((string) config('services.twilio.verify_service_sid', ''));
            if ($sid === '' || $token === '' || $verify === '') {
                $this->error('Cannot --sync: set TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN, TWILIO_VERIFY_SERVICE_SID in .env and rebuild config cache.');

                return self::FAILURE;
            }
            $settings->twilio_service_key = $sid;
            $settings->twilio_auth_token = $token;
            $settings->twilio_verify_service_key = $verify;
            $settings->save();
            $this->info('Synced Twilio credentials from env/config into general_settings.');
            $settings->refresh();
        }

        TwilioSettings::hydrate($settings);

        $this->line('OTP enabled: '.(($settings->is_otp_verification ?? 0) == 1 ? 'yes' : 'no'));
        $this->line('OTP method: '.(($settings->otp_method ?? '') === '' ? '(empty)' : (string) $settings->otp_method).' (1=Twilio)');
        $this->line('Account SID: '.TwilioSettings::sidPrefix($settings));
        $this->line('Configured: '.(TwilioSettings::isConfigured($settings) ? 'yes' : 'no'));

        if (! TwilioSettings::isConfigured($settings)) {
            $this->error('Twilio credentials incomplete.');

            return self::FAILURE;
        }

        try {
            $client = new Client(
                TwilioSettings::accountSid($settings),
                TwilioSettings::authToken($settings)
            );
            $account = $client->api->v2010->accounts(TwilioSettings::accountSid($settings))->fetch();
            $status = (string) ($account->status ?? '');
            $this->line('Account status: '.$status);
            $this->line('Friendly name: '.((string) ($account->friendlyName ?? '')));

            if (strtolower($status) !== 'active') {
                $this->error('Twilio account is not active. Reactivate/pay in Twilio Console or replace with a live Account SID + Auth Token + Verify Service SID.');

                return self::FAILURE;
            }

            $serviceSid = TwilioSettings::verifyServiceSid($settings);
            $service = $client->verify->v2->services($serviceSid)->fetch();
            $this->line('Verify service: '.((string) ($service->friendlyName ?? $serviceSid)));
            $this->info('Twilio health OK.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Twilio API error: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
