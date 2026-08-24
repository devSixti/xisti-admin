<?php

namespace App\Support;

final class PublicLocale
{
    public const ALLOWED = ['es', 'en', 'pt', 'fr', 'it'];

    public static function resolve(mixed $lang = null, string $fallback = 'es'): string
    {
        if (is_array($lang)) {
            $lang = reset($lang);
        }

        $lang = strtolower(substr(trim((string) ($lang ?? '')), 0, 2));

        return in_array($lang, self::ALLOWED, true) ? $lang : $fallback;
    }

    public static function fromRequest(?\Illuminate\Http\Request $request = null, string $fallback = 'es'): string
    {
        $request ??= request();
        $queryLang = $request->query('lang');
        if ($queryLang !== null && $queryLang !== '') {
            return self::resolve($queryLang, $fallback);
        }

        $accept = strtolower(substr((string) $request->header('Accept-Language', $fallback), 0, 2));

        return self::resolve($accept, $fallback);
    }
}
