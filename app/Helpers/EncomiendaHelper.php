<?php

namespace App\Helpers;

class EncomiendaHelper
{
    public const ERRAND_DELIVERY = 'delivery';
    public const ERRAND_ENCOMIENDA = 'encomienda';

    /**
     * @param  object|array<string, mixed>  $row
     */
    public static function isEncomiendaRide(object|array $row): bool
    {
        $errand = self::errandTypeFromRow($row);
        if ($errand === self::ERRAND_ENCOMIENDA) {
            return true;
        }

        $mode = self::serviceModeFromRow($row);

        return $mode === 'encomiendas';
    }

    /**
     * @param  object|array<string, mixed>  $row
     */
    public static function isEncomiendaFlag(object|array $row): int
    {
        return self::isEncomiendaRide($row) ? 1 : 0;
    }

    public static function shouldPersistCourierRow(int $vehicleServiceId, ?string $errandType): bool
    {
        if ($vehicleServiceId === 4) {
            return true;
        }

        return $errandType === self::ERRAND_ENCOMIENDA;
    }

    public static function normalizedErrandType(?string $errandType, int $vehicleServiceId): ?string
    {
        if ($errandType === self::ERRAND_ENCOMIENDA) {
            return self::ERRAND_ENCOMIENDA;
        }
        if ($vehicleServiceId === 4) {
            return self::ERRAND_DELIVERY;
        }

        return null;
    }

    /**
     * @param  object|array<string, mixed>  $row
     */
    private static function errandTypeFromRow(object|array $row): string
    {
        $value = is_array($row) ? ($row['errand_type'] ?? null) : ($row->errand_type ?? null);

        return is_string($value) ? trim($value) : '';
    }

    /**
     * @param  object|array<string, mixed>  $row
     */
    private static function serviceModeFromRow(object|array $row): string
    {
        $value = is_array($row) ? ($row['service_mode'] ?? null) : ($row->service_mode ?? null);

        return is_string($value) ? trim($value) : '';
    }
}
