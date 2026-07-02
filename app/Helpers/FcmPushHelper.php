<?php

namespace App\Helpers;

use App\Classes\AuthAlertClass;

/**
 * FCM HTTP v1 with visible notification + APNs sound (iOS App Store needs production entitlements in the app).
 */
class FcmPushHelper
{
    /**
     * Android: data-only FCM so Flutter onBackgroundMessage always shows a local heads-up.
     * iOS: full notification + APNs alert.
     *
     * @param  array<string, string>  $data
     */
    public static function sendToTokenForLoginDevice(
        string $deviceToken,
        string $title,
        string $body,
        array $data = [],
        ?string $iosSound = 'default',
        ?int $loginDevice = null,
    ): mixed {
        return self::sendToToken(
            $deviceToken,
            $title,
            $body,
            $data,
            $iosSound,
            self::shouldUseAndroidDataOnly($loginDevice, $data),
        );
    }

    /**
     * @param  array<string, string>  $data
     */
    public static function shouldUseAndroidDataOnly(?int $loginDevice, array $data): bool
    {
        if ($loginDevice === 1) {
            return true;
        }
        if ($loginDevice === 2) {
            return false;
        }

        $notificationType = (string) ($data['notification_type'] ?? '');

        return in_array($notificationType, ['1', '6', '7', '8', '14'], true);
    }

    /**
     * @param  array<string, string>  $data
     */
    public static function sendToToken(
        string $deviceToken,
        string $title,
        string $body,
        array $data = [],
        ?string $iosSound = 'default',
        bool $androidDataOnly = false,
    ): mixed {
        if (trim($deviceToken) === '' || (trim($title) === '' && trim($body) === '')) {
            return null;
        }

        return self::sendFcmMessage($deviceToken, $title, $body, $data, $iosSound, $androidDataOnly);
    }

    /**
     * @param  array<int, string>  $tokens
     * @param  array<string, string>  $data
     */
    public static function sendToTokens(
        array $tokens,
        string $title,
        string $body,
        array $data = [],
        ?string $iosSound = 'default',
        bool $androidDataOnly = false,
    ): void {
        $tokens = array_values(array_filter(array_unique($tokens), static fn ($token) => trim((string) $token) !== ''));

        foreach ($tokens as $token) {
            self::sendToToken($token, $title, $body, $data, $iosSound, $androidDataOnly);
        }
    }

    /**
     * @param  array<string, string>  $data
     */
    private static function sendFcmMessage(
        string $token,
        string $title,
        string $body,
        array $data,
        ?string $iosSound,
        bool $androidDataOnly = false,
    ): mixed {
        $fcmUrl = 'https://fcm.googleapis.com/v1/projects/'
            . config('firebase-cloud-messaging.configurations.project_id')
            . '/messages:send';

        $fcmBearerToken = (new AuthAlertClass())->fetchFCMBearerToken();

        $dataPayload = array_map(
            static fn ($value) => is_scalar($value) ? (string) $value : json_encode($value),
            array_merge([
                'title' => $title,
                'message' => $body,
                'body' => $body,
                'sound' => 'true',
            ], $data)
        );

        $notificationType = (string) ($data['notification_type'] ?? '');
        $isRideAlert = in_array($notificationType, ['1', '6', '7', '8', '14'], true);
        $androidChannelId = match (true) {
            $notificationType === '7' => 'new_request_channel',
            $isRideAlert => 'ride_alert_channel',
            default => 'high_importance_channel',
        };
        $androidSound = $isRideAlert ? 'new_request' : 'default';
        $sound = $isRideAlert ? 'new_request.wav' : ($iosSound ?? 'default');

        $fcmMessage = [
            'token' => $token,
            'data' => $dataPayload,
        ];

        if ($androidDataOnly) {
            $fcmMessage['android'] = [
                'priority' => 'HIGH',
            ];
        } else {
            $fcmMessage['notification'] = [
                'title' => $title,
                'body' => $body,
            ];
            $fcmMessage['android'] = [
                'priority' => 'HIGH',
                'notification' => [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'sound' => $androidSound,
                    'channel_id' => $androidChannelId,
                ],
            ];
            $fcmMessage['apns'] = [
                'headers' => [
                    'apns-priority' => '10',
                ],
                'payload' => [
                    'aps' => [
                        'alert' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'sound' => $sound,
                        'badge' => 1,
                        'content-available' => 1,
                    ],
                ],
            ];
        }

        $message = [
            'validate_only' => false,
            'message' => $fcmMessage,
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $fcmUrl);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $fcmBearerToken,
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($message));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}
