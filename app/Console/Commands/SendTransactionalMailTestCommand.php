<?php

namespace App\Console\Commands;

use App\Helpers\EmailBrandLayoutHelper;
use App\Helpers\TransactionalMailHelper;
use App\Jobs\AutoMail;
use App\Models\EmailTemplates;
use App\Models\GeneralSettings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SendTransactionalMailTestCommand extends Command
{
    protected $signature = 'xisti:send-mail-test
                            {email : Recipient address}
                            {--seed : Refresh branded templates before sending}
                            {--only= : Comma-separated template types (default: showcase set)}';

    protected $description = 'Send XISTI branded transactional email samples via Resend/SMTP';

    /** @var list<string> */
    private const SHOWCASE_TYPES = [
        'customer_signup',
        'password_reset_request',
        'ride_invoice_email',
        'promo_code_offer',
        'wallet_topup_receipt',
        'request_completed',
    ];

    public function handle(): int
    {
        $recipient = trim((string) $this->argument('email'));
        if (! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address.');

            return self::FAILURE;
        }

        if ($this->option('seed')) {
            Artisan::call('db:seed', ['--class' => 'BrandedEmailTemplatesSeeder', '--force' => true]);
            $this->info('Branded templates refreshed.');
        }

        $settings = GeneralSettings::query()->first();
        if ($settings === null) {
            $this->error('general_settings row not found.');

            return self::FAILURE;
        }

        if ((int) $settings->send_mail !== 1) {
            $this->warn('send_mail is disabled in general_settings; enabling for this test run.');
            $settings->send_mail = 1;
            $settings->save();
        }

        $types = $this->resolveTypes();
        $merge = EmailBrandLayoutHelper::sampleMergeData();
        $fromAddress = TransactionalMailHelper::fromAddress($settings->smtp_user_name);
        $fromName = TransactionalMailHelper::fromName($settings->mail_site_name);

        $this->info('Mailer: '.(TransactionalMailHelper::resendEnabled() ? 'Resend' : 'SMTP'));
        $this->info("From: {$fromName} <{$fromAddress}>");

        $sent = 0;
        foreach ($types as $type) {
            $template = EmailTemplates::query()->where('type', $type)->where('status', 1)->first();
            if ($template === null) {
                $this->warn("Skipping missing template: {$type}");

                continue;
            }

            $html = str_replace(array_keys($merge), array_values($merge), $template->content);
            $subject = '[XISTI Test] '.$template->title;

            $job = new AutoMail([
                'path' => 'mail_template.temp',
                'email' => $recipient,
                'subject' => $subject,
                'mail_site_name' => $fromName,
                'template_content' => $html,
                'smtp_user_name' => (string) $settings->smtp_user_name,
                'smtp_password' => (string) $settings->smtp_password,
                'smtp_hostname' => (string) $settings->smtp_hostname,
                'smtp_port' => (string) $settings->smtp_port,
                'smtp_encryption' => (string) $settings->smtp_encryption,
            ]);
            $job->handle();

            $this->line("✓ Sent: {$type}");
            $sent++;
        }

        if ($sent === 0) {
            $this->error('No emails were sent. Run with --seed to install templates.');

            return self::FAILURE;
        }

        $this->info("Done. {$sent} email(s) sent to {$recipient}.");

        return self::SUCCESS;
    }

    /**
     * @return list<string>
     */
    private function resolveTypes(): array
    {
        $only = trim((string) $this->option('only'));
        if ($only === '') {
            return self::SHOWCASE_TYPES;
        }

        return array_values(array_filter(array_map('trim', explode(',', $only))));
    }
}
