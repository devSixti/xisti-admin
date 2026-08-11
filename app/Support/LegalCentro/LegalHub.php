<?php

namespace App\Support\LegalCentro;

use App\Support\LegalConfig;

class LegalHub
{
    public const LANGS = ['es', 'en', 'pt', 'fr', 'it'];

    /** @return array<string, array{label: string, items: list<array{slug: string, route: string, label_key: string, icon?: string}>}> */
    public static function sections(): array
    {
        return [
            'core' => [
                'label' => 'legal.nav.section_core',
                'items' => [
                    ['slug' => 'terminos', 'route' => 'get:legal:page', 'label_key' => 'legal.terms', 'icon' => 'bi-file-text'],
                    ['slug' => 'privacidad', 'route' => 'get:legal:page', 'label_key' => 'legal.privacy', 'icon' => 'bi-shield-lock'],
                    ['slug' => 'tratamiento-datos', 'route' => 'get:legal:doc', 'label_key' => 'legal.data_processing', 'icon' => 'bi-database-lock'],
                    ['slug' => 'aviso-legal', 'route' => 'get:legal:page', 'label_key' => 'legal.legal_notice', 'icon' => 'bi-building'],
                ],
            ],
            'users' => [
                'label' => 'legal.nav.section_users',
                'items' => [
                    ['slug' => 'condiciones-usuario', 'route' => 'get:legal:doc', 'label_key' => 'legal.user_conditions', 'icon' => 'bi-person'],
                    ['slug' => 'condiciones-conductor', 'route' => 'get:legal:doc', 'label_key' => 'legal.driver_conditions', 'icon' => 'bi-car-front'],
                    ['slug' => 'seguridad', 'route' => 'get:legal:page', 'label_key' => 'legal.security', 'icon' => 'bi-shield-check'],
                ],
            ],
            'support' => [
                'label' => 'legal.nav.section_support',
                'items' => [
                    ['slug' => 'faq', 'route' => 'get:legal:page', 'label_key' => 'legal.faq', 'icon' => 'bi-question-circle'],
                    ['slug' => 'pqr', 'route' => 'get:legal:doc', 'label_key' => 'legal.pqr', 'icon' => 'bi-chat-left-text'],
                    ['slug' => 'contacto', 'route' => 'get:legal:page', 'label_key' => 'legal.contact_link', 'icon' => 'bi-envelope'],
                    ['slug' => 'cookies', 'route' => 'get:legal:cookies', 'label_key' => 'legal.cookies', 'icon' => 'bi-cookie'],
                    ['slug' => 'eliminar-cuenta', 'route' => 'get:legal:delete-account', 'label_key' => 'legal.delete_account', 'icon' => 'bi-person-x'],
                ],
            ],
        ];
    }

    public static function brandName(): string
    {
        return (string) config('legal_centro.brand_name', 'XISTI');
    }

    public static function tagline(): string
    {
        return (string) config('legal_centro.tagline', 'Fácil y Seguro');
    }

    public static function consentVersion(): string
    {
        return (string) config('legal_centro.consent_version', config('xisti.legal.consent_version', '2026-06-legal-v1'));
    }

    public static function lastUpdated(): string
    {
        return (string) config('legal_centro.last_updated', '2026-08-11');
    }

    /** @return array{name: string, nit: string, address: string, city: string, country: string} */
    public static function entity(): array
    {
        return [
            'name' => (string) config('legal_centro.entity.name', self::brandName().' Tecnología S.A.S.'),
            'nit' => (string) config('legal_centro.entity.nit', ''),
            'address' => (string) config('legal_centro.entity.address', 'Colombia'),
            'city' => (string) config('legal_centro.entity.city', 'Colombia'),
            'country' => (string) config('legal_centro.entity.country', 'Colombia'),
        ];
    }

    /** @return array<string, string> */
    public static function tokens(string $lang): array
    {
        $entity = self::entity();
        $emails = LegalConfig::emails();

        return [
            ':brand_name' => self::brandName(),
            ':tagline' => self::tagline(),
            ':public_url' => (string) config('xisti.public_site_url', 'https://admin.xistiapp.com'),
            ':centro_legal_url' => LegalConfig::centroLegalUrl($lang),
            ':support_email' => $emails['support'] ?? 'soporte@xistiapp.com',
            ':privacy_email' => $emails['privacy'] ?? 'privacidad@xistiapp.com',
            ':legal_email' => $emails['legal'] ?? 'legal@xistiapp.com',
            ':pqr_email' => $emails['pqr'] ?? 'pqr@xistiapp.com',
            ':hello_email' => $emails['hello'] ?? 'hola@xistiapp.com',
            ':entity_name' => $entity['name'],
            ':entity_nit' => $entity['nit'] !== '' ? $entity['nit'] : '—',
            ':entity_address' => $entity['address'],
            ':entity_city' => $entity['city'],
            ':entity_country' => $entity['country'],
            ':consent_version' => self::consentVersion(),
            ':last_updated' => self::lastUpdated(),
        ];
    }

    public static function pageIdForSlug(string $slug): ?int
    {
        return match ($slug) {
            'contacto' => 1,
            'faq' => 2,
            'aviso-legal' => 3,
            'privacidad' => 4,
            'terminos' => 5,
            'seguridad' => 6,
            default => null,
        };
    }

    public static function isActive(string $activeSlug, string $itemSlug): bool
    {
        return $activeSlug === $itemSlug;
    }
}
