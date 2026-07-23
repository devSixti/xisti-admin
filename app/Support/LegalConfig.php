<?php

namespace App\Support;

class LegalConfig
{
    public static function centroLegalUrl(?string $lang = null): string
    {
        $url = (string) config('xisti.legal.centro_legal_url', 'https://admin.xistiapp.com/terms-and-conditions');
        if ($lang === null || $lang === '') {
            return $url;
        }

        $separator = str_contains($url, '?') ? '&' : '?';

        return $url.$separator.'lang='.urlencode(strtolower(substr($lang, 0, 2)));
    }

    public static function email(string $role): string
    {
        return (string) config("xisti.legal.emails.{$role}", '');
    }

    public static function storeLink(string $platform): string
    {
        return (string) config("xisti.legal.store_links.{$platform}", '');
    }

    /** @return array<string, string> */
    public static function emails(): array
    {
        $emails = config('xisti.legal.emails', []);

        return is_array($emails) ? $emails : [];
    }

    /** Payload for mobile splash / settings APIs. */
    public static function mobilePayload(?string $lang = null): array
    {
        return [
            'centro_legal_url' => self::centroLegalUrl($lang),
            'legal_emails' => self::emails(),
            'store_links' => [
                'android' => self::storeLink('android'),
                'ios' => self::storeLink('ios'),
            ],
        ];
    }
}
