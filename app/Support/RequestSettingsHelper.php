<?php

namespace App\Support;

use App\Models\GeneralSettings;

/**
 * Resolves general_settings from request attributes (AppServiceProvider) or input.
 */
class RequestSettingsHelper
{
    public static function generalSettings(): ?GeneralSettings
    {
        $request = request();
        if ($request === null) {
            return null;
        }

        $fromAttributes = $request->attributes->get('general_settings');
        if ($fromAttributes instanceof GeneralSettings) {
            return $fromAttributes;
        }

        $fromInput = $request->get('general_settings');
        if ($fromInput instanceof GeneralSettings) {
            return $fromInput;
        }

        return null;
    }
}
