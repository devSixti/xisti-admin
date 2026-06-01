<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Replaces the legacy SourceGuardian AuthAlertClass::fetchFCMBearerToken().
 */
class FcmAccessTokenService
{
    private const FCM_SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';

    private const CACHE_KEY = 'xisti.fcm.access_token';

    private const CACHE_TTL_SECONDS = 3300;

    public function fetchFCMBearerToken(): string
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, function () {
            $credentials = $this->serviceAccountCredentials();
            $token = $credentials->fetchAuthToken();

            if (! is_array($token) || empty($token['access_token'])) {
                Log::error('xisti.fcm.token_fetch_failed');

                throw new RuntimeException('Unable to fetch Firebase Cloud Messaging access token.');
            }

            return (string) $token['access_token'];
        });
    }

    public function clearCachedToken(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private function serviceAccountCredentials(): ServiceAccountCredentials
    {
        $jsonPath = trim((string) config('firebase-cloud-messaging.service_account_path', ''));
        if ($jsonPath !== '' && is_readable($jsonPath)) {
            return new ServiceAccountCredentials([self::FCM_SCOPE], $jsonPath);
        }

        $inline = config('firebase-cloud-messaging.configurations');
        if (! is_array($inline) || empty($inline['client_email']) || empty($inline['private_key'])) {
            throw new RuntimeException('Firebase service account is not configured.');
        }

        return new ServiceAccountCredentials([self::FCM_SCOPE], $inline);
    }
}
