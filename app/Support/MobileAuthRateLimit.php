<?php

namespace App\Support;

use Database\Seeders\XistiQaTestUserSeeder;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;

/**
 * JSON rate limits for mobile login / OTP (avoid HTML 429 pages in the app).
 */
class MobileAuthRateLimit
{
    public static function register(): void
    {
        RateLimiter::for('customer-login', function (Request $request) {
            $phone = self::normalizePhone($request);
            $bucket = $phone !== '' ? 'login:'.$phone : 'login:ip:'.$request->ip();
            $perMinute = self::isQaPhone($phone) ? 300 : 120;

            return Limit::perMinute($perMinute)
                ->by($bucket)
                ->response(fn () => self::tooManyAttemptsResponse($request));
        });

        RateLimiter::for('customer-otp', function (Request $request) {
            $phone = self::normalizePhone($request);
            $bucket = $phone !== '' ? 'otp:'.$phone : 'otp:uid:'.($request->get('user_id') ?? $request->ip());
            $perMinute = self::isQaPhone($phone) ? 120 : 60;

            return Limit::perMinute($perMinute)
                ->by($bucket)
                ->response(fn () => self::tooManyAttemptsResponse($request));
        });

        RateLimiter::for('customer-resend-otp', function (Request $request) {
            $phone = self::normalizePhone($request);
            $bucket = $phone !== '' ? 'resend:'.$phone : 'resend:uid:'.($request->get('user_id') ?? $request->ip());
            $perMinute = self::isQaPhone($phone) ? 60 : 30;

            return Limit::perMinute($perMinute)
                ->by($bucket)
                ->response(fn () => self::tooManyAttemptsResponse($request));
        });
    }

    private static function normalizePhone(Request $request): string
    {
        $raw = (string) ($request->get('contact_number') ?? $request->get('phone') ?? '');
        $country = (string) ($request->get('select_country_code') ?? $request->get('country_code') ?? '+57');

        $phone = ColombiaFormValidation::normalizeColombianMobile($raw, $country);
        if ($phone !== '') {
            return $phone;
        }

        $userId = (int) $request->get('user_id');
        if ($userId > 0 && Schema::hasTable('users')) {
            $stored = (string) DB::table('users')->where('id', $userId)->value('contact_number');

            return ColombiaFormValidation::normalizeColombianMobile($stored, '+57');
        }

        return '';
    }

    private static function isQaPhone(string $phone): bool
    {
        return $phone !== '' && in_array($phone, XistiQaTestUserSeeder::qaPhoneLocals(), true);
    }

    private static function tooManyAttemptsResponse(Request $request): \Illuminate\Http\JsonResponse
    {
        $lang = strtolower((string) $request->header('select-language', 'es'));
        $message = str_starts_with($lang, 'es')
            ? 'Demasiados intentos. Espera un momento e inténtalo de nuevo.'
            : 'Too many attempts. Please wait a moment and try again.';

        return response()->json([
            'status' => 0,
            'message' => $message,
            'message_code' => 429,
        ], 429);
    }
}
