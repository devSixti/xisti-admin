<?php

namespace App\Jobs;

use App\Helpers\TransactionalMailHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class AutoMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        $data = $this->data;
        $email = $data['email'];
        $path = $data['path'];
        $subject = $data['subject'];
        $mail_site_name = $data['mail_site_name'];
        $smtp_user_name = $data['smtp_user_name'];
        $smtp_password = $data['smtp_password'];
        $smtp_hostname = $data['smtp_hostname'];
        $smtp_port = $data['smtp_port'];
        $smtp_encryption = $data['smtp_encryption'];

        if (TransactionalMailHelper::resendEnabled()) {
            $mailer = (string) config('mail.default', 'resend');
            Config::set('mail.default', $mailer === 'failover' ? 'failover' : 'resend');
        } else {
            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.username', $smtp_user_name);
            Config::set('mail.mailers.smtp.password', $smtp_password);
            Config::set('mail.mailers.smtp.host', $smtp_hostname);
            Config::set('mail.mailers.smtp.port', $smtp_port);
            Config::set('mail.mailers.smtp.encryption', $smtp_encryption);
        }

        $fromAddress = TransactionalMailHelper::fromAddress($smtp_user_name);
        $fromName = TransactionalMailHelper::fromName($mail_site_name);

        Mail::send($path, $data, function ($message) use ($email, $subject, $fromAddress, $fromName) {
            $message->from($fromAddress, $fromName);
            $message->to($email);
            $message->subject($subject);
        });
    }
}
