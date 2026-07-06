<?php

namespace App\Helpers;

class TransactionalMailHelper
{
    public static function defaultFromAddress(): string
    {
        return (string) config('xisti.mail.from_address', 'noreply@xistiapp.com');
    }

    public static function defaultFromName(): string
    {
        return (string) config('xisti.mail.from_name', 'XISTI');
    }

    public static function resendEnabled(): bool
    {
        $resendKey = (string) config('services.resend.key', env('RESEND_API_KEY', ''));
        $mailer = (string) config('mail.default', env('MAIL_MAILER', 'smtp'));

        return $resendKey !== '' && in_array($mailer, ['resend', 'failover'], true);
    }

    public static function transportConfigured(
        string $smtpUser,
        string $smtpPassword,
        string $smtpHost,
        string $smtpPort,
        string $smtpEncryption
    ): bool {
        if (self::resendEnabled()) {
            return true;
        }

        return $smtpUser !== ''
            && $smtpPassword !== ''
            && $smtpHost !== ''
            && $smtpPort !== ''
            && $smtpEncryption !== '';
    }

    public static function fromAddress(?string $smtpUserName = null): string
    {
        if (self::resendEnabled()) {
            return (string) config('mail.from.address', self::defaultFromAddress());
        }

        $smtpUserName = trim((string) $smtpUserName);
        if ($smtpUserName !== '') {
            return $smtpUserName;
        }

        return (string) config('mail.from.address', self::defaultFromAddress());
    }

    public static function fromName(?string $mailSiteName = null): string
    {
        $mailSiteName = trim((string) $mailSiteName);
        if ($mailSiteName !== '') {
            return $mailSiteName;
        }

        return (string) config('mail.from.name', self::defaultFromName());
    }
}
