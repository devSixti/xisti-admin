<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class GoogleMapsApiResponse
{
    public static function proxyJson(?array $payload, int $httpStatus = 200): JsonResponse
    {
        if (! is_array($payload) || $payload === []) {
            return response()->json([
                'status' => 0,
                'message' => __('user_messages.9'),
                'message_code' => 9,
            ], $httpStatus > 0 && $httpStatus < 600 ? $httpStatus : 502);
        }

        return response()->json($payload, $httpStatus > 0 ? $httpStatus : 200);
    }

    public static function curlFailure(string $curlError): JsonResponse
    {
        return response()->json([
            'status' => 0,
            'message' => __('user_messages.9'),
            'message_code' => 9,
        ], 502);
    }

    public static function missingMapKey(): JsonResponse
    {
        return response()->json([
            'status' => 0,
            'message' => __('user_messages.9'),
            'message_code' => 9,
        ], 503);
    }
}
