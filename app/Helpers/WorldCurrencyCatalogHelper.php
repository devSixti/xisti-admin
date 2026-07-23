<?php

namespace App\Helpers;

class WorldCurrencyCatalogHelper
{
    /** Currencies exposed in mobile country/currency pickers for now. */
    public const MOBILE_VISIBLE_CODES = ['COP', 'USD', 'EUR', 'BRL', 'ARS'];

    /**
     * @return list<string>
     */
    public static function mobileVisibleCodes(): array
    {
        return self::MOBILE_VISIBLE_CODES;
    }
}
