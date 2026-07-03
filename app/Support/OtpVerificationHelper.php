<?php

namespace App\Support;

use App\Models\UserVerification;
use Illuminate\Support\Facades\Schema;

final class OtpVerificationHelper
{
    public const RESEND_COOLDOWN_SECONDS = 60;

    public static function hasRecentPending(int $userId, ?int $cooldownSeconds = null): bool
    {
        $cooldownSeconds = $cooldownSeconds ?? self::RESEND_COOLDOWN_SECONDS;
        if ($cooldownSeconds <= 0 || ! Schema::hasColumn('user_verification', 'verification_sent_at')) {
            return false;
        }

        $sentAt = UserVerification::query()->where('user_id', $userId)->value('verification_sent_at');
        if ($sentAt === null) {
            return false;
        }

        return now()->diffInSeconds($sentAt) < $cooldownSeconds;
    }

    public static function assignChannel(UserVerification $record, string $channel): void
    {
        if (Schema::hasColumn('user_verification', 'verification_channel')) {
            $record->verification_channel = $channel;
        }
    }

    public static function markSent(UserVerification $record): void
    {
        if (Schema::hasColumn('user_verification', 'verification_sent_at')) {
            $record->verification_sent_at = now();
        }
    }

    public static function lastChannelForUser(int $userId, string $default = 'sms'): string
    {
        if (! Schema::hasColumn('user_verification', 'verification_channel')) {
            return $default;
        }

        $channel = UserVerification::query()
            ->where('user_id', $userId)
            ->value('verification_channel');

        return in_array($channel, ['sms', 'whatsapp'], true) ? $channel : $default;
    }
}
