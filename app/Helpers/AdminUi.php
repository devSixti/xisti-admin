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

    /**
     * Sidebar label: prefer the entry's distinct name (e.g. "All Rides") over shared module_name.
     */
    public static function menuEntryLabel(string $name, ?string $moduleName = null): string
    {
        $name = trim($name);
        if ($name === '') {
            return self::moduleName($moduleName, 'Module');
        }

        $nameSlug = self::moduleSlug($name);
        $nameKey = 'admin.modules.'.$nameSlug;
        $fromName = __($nameKey);
        if ($fromName !== $nameKey) {
            return $fromName;
        }

        if ($moduleName !== null && strcasecmp($name, trim($moduleName)) === 0) {
            return self::moduleName($moduleName, $name);
        }

        return $name;
    }

    public static function moduleSlug(?string $moduleName): string
    {
        if ($moduleName === null || trim($moduleName) === '') {
            return 'misc';
        }

        $slug = Str::slug(str_replace(['/', '\\'], '-', trim($moduleName)), '_');

        return $slug !== '' ? $slug : 'misc';
    }

    /** @return array<string, mixed> */
    public static function datatablesLanguage(): array
    {
        return [
            'search' => __('admin.datatables.search'),
            'lengthMenu' => __('admin.datatables.length_menu'),
            'info' => __('admin.datatables.info'),
            'infoEmpty' => __('admin.datatables.info_empty'),
            'infoFiltered' => '',
            'zeroRecords' => __('admin.datatables.zero_records'),
            'emptyTable' => __('admin.datatables.empty_table'),
            'processing' => __('admin.datatables.processing'),
            'paginate' => [
                'first' => __('admin.datatables.first'),
                'last' => __('admin.datatables.last'),
                'next' => __('admin.datatables.next'),
                'previous' => __('admin.datatables.previous'),
            ],
        ];
    }

    public static function activeStatusLabel(int|string $status): string
    {
        return ((string) $status === '1')
            ? __('admin.status.active')
            : __('admin.status.inactive');
    }

    public static function pageSubtitle(string $pageKey): string
    {
        return __('admin.pages.all_list', ['name' => __('admin.pages.'.$pageKey)]);
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
