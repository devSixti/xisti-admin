<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Replaces the legacy SourceGuardian AuthAlertClass::checkAuthorizationApp().
 * Validates the mobile Authorization header against general_settings.app_key.
 */
class AppAuthorizationService
{
    public const AUTH_PREFIX_LENGTH = 57;

    public const AUTH_SUFFIX_LENGTH = 43;

    public const AUTH_DIGEST_LENGTH = 32;

    public function checkAuthorizationApp(Request $request): JsonResponse
    {
        $appKey = $this->resolveAppKey();
        if ($appKey === '') {
            Log::warning('xisti.auth.missing_app_key');

            return $this->deny(__('user_messages.0'), 0);
        }

        $authorization = trim((string) $request->header('Authorization', ''));
        if ($authorization === '') {
            return $this->deny(__('user_messages.0'), 0);
        }

        $expectedDigest = $this->buildExpectedDigest($appKey);
        $providedDigest = $this->extractDigestFromAuthorization($authorization);

        if ($providedDigest === null || ! hash_equals($expectedDigest, $providedDigest)) {
            Log::warning('xisti.auth.invalid_header', [
                'host' => $request->getHost(),
                'length' => strlen($authorization),
            ]);

            return $this->deny(__('user_messages.0'), 0);
        }

        $allowedHost = trim((string) config('xisti.allowed_admin_host', ''));
        if ($allowedHost !== '' && strcasecmp($request->getHost(), $allowedHost) !== 0) {
            Log::warning('xisti.auth.invalid_host', [
                'host' => $request->getHost(),
                'allowed' => $allowedHost,
            ]);

            return $this->deny(__('user_messages.0'), 0);
        }

        return response()->json([
            'status' => 1,
            'message' => __('user_messages.1'),
            'message_code' => 1,
        ]);
    }

    public function buildExpectedDigest(string $appKey): string
    {
        return md5(base64_encode($appKey));
    }

    public function extractDigestFromAuthorization(string $authorization): ?string
    {
        $minimumLength = self::AUTH_PREFIX_LENGTH + self::AUTH_DIGEST_LENGTH + self::AUTH_SUFFIX_LENGTH;
        if (strlen($authorization) < $minimumLength) {
            return null;
        }

        $digest = substr($authorization, self::AUTH_PREFIX_LENGTH, self::AUTH_DIGEST_LENGTH);
        if (! preg_match('/^[a-f0-9]{32}$/i', $digest)) {
            return null;
        }

        return strtolower($digest);
    }

    public function buildAuthorizationHeader(string $appKey): string
    {
        $digest = $this->buildExpectedDigest($appKey);
        $prefix = $this->randomString(self::AUTH_PREFIX_LENGTH);
        $suffix = $this->randomString(self::AUTH_SUFFIX_LENGTH);

        return $prefix.$digest.$suffix;
    }

    private function resolveAppKey(): string
    {
        $fromEnv = trim((string) config('xisti.app_key', ''));
        if ($fromEnv !== '') {
            return $fromEnv;
        }

        $generalSettings = request()->get('general_settings');
        if ($generalSettings !== null && ! empty($generalSettings->app_key)) {
            return (string) $generalSettings->app_key;
        }

        return '';
    }

    private function randomString(int $length): string
    {
        $chars = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz1234567890';
        $result = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[random_int(0, $max)];
        }

        return $result;
    }

    private function deny(string $message, int $messageCode): JsonResponse
    {
        return response()->json([
            'status' => 0,
            'message' => $message,
            'message_code' => $messageCode,
        ]);
    }
}
