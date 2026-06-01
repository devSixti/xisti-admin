<?php

namespace App\Helpers;

class DestinationPaymentHelper
{
    public const CASH = 'cash';
    public const BANCOLOMBIA = 'bancolombia';
    public const DAVIPLATA = 'daviplata';
    public const NEQUI = 'nequi';

    public static function allowed(): array
    {
        return [self::CASH, self::BANCOLOMBIA, self::DAVIPLATA, self::NEQUI];
    }

    /**
     * @return list<array{code: string, label_es: string, label_en: string}>
     */
    public static function defaultCatalog(): array
    {
        return [
            ['code' => self::CASH, 'label_es' => 'Efectivo', 'label_en' => 'Cash'],
            ['code' => self::BANCOLOMBIA, 'label_es' => 'Transferencia Bancolombia', 'label_en' => 'Bancolombia transfer'],
            ['code' => self::DAVIPLATA, 'label_es' => 'Daviplata', 'label_en' => 'Daviplata'],
            ['code' => self::NEQUI, 'label_es' => 'Nequi', 'label_en' => 'Nequi'],
        ];
    }

    /**
     * @return list<array{code: string, label_es: string, label_en: string}>
     */
    public static function catalog(?object $generalSettings = null): array
    {
        $general = $generalSettings ?? request()->get('general_settings');
        $raw = $general->destination_payment_methods ?? null;
        if ($raw === null || trim((string) $raw) === '') {
            return self::defaultCatalog();
        }
        $decoded = json_decode((string) $raw, true);
        if (!is_array($decoded) || $decoded === []) {
            return self::defaultCatalog();
        }
        $items = [];
        foreach ($decoded as $row) {
            if (!is_array($row)) {
                continue;
            }
            $code = strtolower(trim((string) ($row['code'] ?? '')));
            if ($code === '' || !preg_match('/^[a-z][a-z0-9_]{0,31}$/', $code)) {
                continue;
            }
            $items[] = [
                'code' => $code,
                'label_es' => trim((string) ($row['label_es'] ?? $row['label'] ?? $code)),
                'label_en' => trim((string) ($row['label_en'] ?? $row['label'] ?? $code)),
            ];
        }

        return $items !== [] ? $items : self::defaultCatalog();
    }

    public static function allowedCodes(?object $generalSettings = null): array
    {
        return array_values(array_unique(array_map(
            static fn (array $item) => $item['code'],
            self::catalog($generalSettings)
        )));
    }

    public static function validationRule(?object $generalSettings = null): string
    {
        return 'nullable|in:' . implode(',', self::allowedCodes($generalSettings));
    }

    /**
     * @return list<array{code: string, label: string}>
     */
    public static function methodsForApi(string $language = 'en', ?object $generalSettings = null): array
    {
        $lang = str_starts_with($language, 'es') ? 'es' : 'en';
        $out = [];
        foreach (self::catalog($generalSettings) as $item) {
            $out[] = [
                'code' => $item['code'],
                'label' => $lang === 'es' ? $item['label_es'] : $item['label_en'],
            ];
        }

        return $out;
    }

    /**
     * Full catalog for mobile Hive (bilingual labels).
     *
     * @return list<array{code: string, label_es: string, label_en: string, label: string}>
     */
    public static function catalogForMobileApi(?object $generalSettings = null): array
    {
        $out = [];
        foreach (self::catalog($generalSettings) as $item) {
            $out[] = [
                'code' => $item['code'],
                'label_es' => $item['label_es'],
                'label_en' => $item['label_en'],
                'label' => $item['label_es'],
            ];
        }

        return $out;
    }

    /**
     * @param  array<int, string|null>  $codes
     * @param  array<int, string|null>  $labelsEs
     * @param  array<int, string|null>  $labelsEn
     * @return list<array{code: string, label_es: string, label_en: string}>
     */
    public static function buildCatalogFromAdminRows(array $codes, array $labelsEs, array $labelsEn): array
    {
        $items = [];
        foreach ($codes as $index => $codeRaw) {
            $code = strtolower(trim((string) $codeRaw));
            if ($code === '' || !preg_match('/^[a-z][a-z0-9_]{0,31}$/', $code)) {
                continue;
            }
            $labelEs = trim((string) ($labelsEs[$index] ?? $code));
            $labelEn = trim((string) ($labelsEn[$index] ?? $labelEs));
            $items[] = [
                'code' => $code,
                'label_es' => $labelEs !== '' ? $labelEs : $code,
                'label_en' => $labelEn !== '' ? $labelEn : $code,
            ];
        }

        return $items;
    }

    public static function label(?string $method, string $language = 'en'): string
    {
        $labels = [
            'en' => [
                self::CASH => 'Cash',
                self::BANCOLOMBIA => 'Bancolombia transfer',
                self::DAVIPLATA => 'Daviplata',
                self::NEQUI => 'Nequi',
            ],
            'es' => [
                self::CASH => 'Efectivo',
                self::BANCOLOMBIA => 'Transferencia Bancolombia',
                self::DAVIPLATA => 'Daviplata',
                self::NEQUI => 'Nequi',
            ],
        ];
        $lang = str_starts_with($language, 'es') ? 'es' : 'en';
        $key = strtolower((string) $method);
        if (isset($labels[$lang][$key])) {
            return $labels[$lang][$key];
        }
        foreach (self::catalog() as $item) {
            if ($item['code'] === $key) {
                return $lang === 'es' ? $item['label_es'] : $item['label_en'];
            }
        }

        return (string) $method;
    }

    public static function normalize(?string $method): ?string
    {
        if ($method === null || $method === '') {
            return null;
        }
        $key = strtolower(trim($method));
        return in_array($key, self::allowedCodes(), true) ? $key : null;
    }
}
