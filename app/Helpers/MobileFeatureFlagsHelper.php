<?php

namespace App\Helpers;

use App\Models\GeneralSettings;

class MobileFeatureFlagsHelper
{
    public static function settings(?GeneralSettings $general = null): GeneralSettings
    {
        $general = $general ?? request()->get('general_settings');

        return $general instanceof GeneralSettings ? $general : new GeneralSettings();
    }

    public static function isExpresoEnabled(?GeneralSettings $general = null): bool
    {
        return (int) (self::settings($general)->enable_expreso_mobile ?? 0) === 1;
    }

    public static function isEncomiendasEnabled(?GeneralSettings $general = null): bool
    {
        return (int) (self::settings($general)->enable_encomiendas_mobile ?? 0) === 1;
    }

    public static function requireCourierPackageDimensions(?GeneralSettings $general = null): bool
    {
        return (int) (self::settings($general)->require_courier_package_dimensions_mobile ?? 0) === 1;
    }

    public static function isXistiNewHomeLayoutEnabled(?GeneralSettings $general = null): bool
    {
        return (int) (self::settings($general)->enable_xisti_new_home_layout ?? 1) === 1;
    }

    public static function isAcarreosEnabled(?GeneralSettings $general = null): bool
    {
        return (int) (self::settings($general)->enable_acarreos_mobile ?? 1) === 1;
    }

    /**
     * @return array<string, int>
     */
    public static function apiPayload(?GeneralSettings $general = null): array
    {
        return [
            'enable_expreso_mobile' => self::isExpresoEnabled($general) ? 1 : 0,
            'enable_encomiendas_mobile' => self::isEncomiendasEnabled($general) ? 1 : 0,
            'enable_acarreos_mobile' => self::isAcarreosEnabled($general) ? 1 : 0,
            'require_courier_package_dimensions' => self::requireCourierPackageDimensions($general) ? 1 : 0,
            'enable_xisti_new_home_layout' => self::isXistiNewHomeLayoutEnabled($general) ? 1 : 0,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $modes
     * @return array<int, array<string, mixed>>
     */
    public static function filterServiceModes(array $modes, ?GeneralSettings $general = null): array
    {
        return array_values(array_filter($modes, static function (array $mode) use ($general) {
            $key = $mode['mode'] ?? '';
            if (in_array($key, ['expreso', 'viajes_compartidos'], true) && ! self::isExpresoEnabled($general)) {
                return false;
            }
            if ($key === 'encomiendas' && ! self::isEncomiendasEnabled($general)) {
                return false;
            }
            if (in_array($key, ['acarreos', 'carga'], true) && ! self::isAcarreosEnabled($general)) {
                return false;
            }

            return true;
        }));
    }

    /**
     * @param  array<int, array<string, mixed>>  $services
     * @return array<int, array<string, mixed>>
     */
    public static function filterServiceRows(array $services, ?GeneralSettings $general = null): array
    {
        return array_values(array_filter($services, static function (array $row) use ($general) {
            $mode = $row['service_mode'] ?? 'transport';
            if ($mode === 'expreso' && ! self::isExpresoEnabled($general)) {
                return false;
            }
            if (in_array($mode, ['viajes_compartidos', 'expreso'], true) && ! self::isExpresoEnabled($general)) {
                return false;
            }
            if ($mode === 'encomiendas' && ! self::isEncomiendasEnabled($general)) {
                return false;
            }
            if (in_array($mode, ['acarreos', 'carga'], true) && ! self::isAcarreosEnabled($general)) {
                return false;
            }

            return true;
        }));
    }
}
