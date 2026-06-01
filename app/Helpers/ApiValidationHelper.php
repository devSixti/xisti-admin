<?php

namespace App\Helpers;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;

class ApiValidationHelper
{
    /**
     * JSON error for mobile clients using message_code + localized message.
     */
    public static function jsonFromValidator(Validator $validator): JsonResponse
    {
        $mapped = self::mapBookRideFailure($validator->failed());
        if ($mapped !== null) {
            return response()->json([
                'status' => 0,
                'message' => $mapped['message'],
                'message_code' => $mapped['message_code'],
            ]);
        }

        return response()->json([
            'status' => 0,
            'message' => $validator->errors()->first(),
            'message_code' => 9,
        ]);
    }

    /**
     * @param  array<string, array<string, mixed>>  $failedRules
     * @return array{message: string, message_code: int}|null
     */
    public static function mapBookRideFailure(array $failedRules): ?array
    {
        if (isset($failedRules['package_weight_kg'])) {
            return [
                'message' => __('user_messages.389'),
                'message_code' => 389,
            ];
        }
        if (isset($failedRules['package_height_cm']) || isset($failedRules['package_width_cm']) || isset($failedRules['package_length_cm'])) {
            return [
                'message' => __('user_messages.390'),
                'message_code' => 390,
            ];
        }
        if (isset($failedRules['recipient_name'])) {
            return [
                'message' => __('user_messages.391'),
                'message_code' => 391,
            ];
        }
        if (isset($failedRules['recipient_contact_number']) || isset($failedRules['other_user_contact_number'])) {
            return [
                'message' => __('user_messages.385'),
                'message_code' => 385,
            ];
        }
        if (isset($failedRules['item_description'])) {
            return [
                'message' => __('user_messages.392'),
                'message_code' => 392,
            ];
        }

        return null;
    }

    public static function ensureApiLocale(?string $language = null): void
    {
        $lang = $language ?? request()->header('select-language', 'es');
        $lang = strtolower(trim((string) $lang));
        if (str_contains($lang, '-')) {
            $lang = explode('-', $lang)[0];
        }
        if (!in_array($lang, ['en', 'es', 'fr', 'it', 'pt'], true)) {
            $lang = 'es';
        }
        App::setLocale($lang);
    }
}
