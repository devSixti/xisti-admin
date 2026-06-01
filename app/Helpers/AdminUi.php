<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminUi
{
    /** @var list<string> */
    public const LOCALES = ['es', 'en', 'pt', 'fr', 'it'];

    public const DEFAULT_LOCALE = 'es';

    public const SESSION_KEY = 'admin_locale';

    public static function resolveLocale(?Request $request = null): string
    {
        if (session()->has(self::SESSION_KEY)) {
            return self::normalizeLocale((string) session()->get(self::SESSION_KEY));
        }

        $request ??= request();
        if ($request !== null) {
            $query = $request->query('lang');
            if (is_string($query) && $query !== '') {
                return self::normalizeLocale($query);
            }
        }

        $preferred = $request?->getPreferredLanguage(self::LOCALES);

        return self::normalizeLocale($preferred ?? self::DEFAULT_LOCALE);
    }

    public static function normalizeLocale(string $locale): string
    {
        $locale = strtolower(trim($locale));
        if (str_contains($locale, '-')) {
            $locale = explode('-', $locale)[0];
        }
        if (str_contains($locale, '_')) {
            $locale = explode('_', $locale)[0];
        }

        return in_array($locale, self::LOCALES, true) ? $locale : self::DEFAULT_LOCALE;
    }

    public static function label(string $key, ?string $fallback = null, array $replace = []): string
    {
        $full = 'admin.'.$key;
        $translated = __($full, $replace);

        if ($translated !== $full) {
            return $translated;
        }

        return $fallback ?? $key;
    }

    public static function moduleName(?string $moduleName, string $fallback): string
    {
        $slug = self::moduleSlug($moduleName);

        return self::label('modules.'.$slug, $fallback);
    }

    public static function moduleSlug(?string $moduleName): string
    {
        if ($moduleName === null || trim($moduleName) === '') {
            return 'misc';
        }

        $slug = Str::slug(str_replace(['/', '\\'], '-', trim($moduleName)), '_');

        return $slug !== '' ? $slug : 'misc';
    }

    /** @return array<string, string> */
    public static function localeLabels(): array
    {
        return [
            'es' => self::label('locale.es', 'Español'),
            'en' => self::label('locale.en', 'English'),
            'pt' => self::label('locale.pt', 'Português'),
            'fr' => self::label('locale.fr', 'Français'),
            'it' => self::label('locale.it', 'Italiano'),
        ];
    }
}
