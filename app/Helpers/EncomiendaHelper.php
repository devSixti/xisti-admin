<?php

namespace App\Helpers;

class EncomiendaHelper
{
    public const ERRAND_DELIVERY = 'delivery';
    public const ERRAND_ENCOMIENDA = 'encomienda';

    public const ERRAND_ACARREO = 'acarreo';

    public const ENCOMIENDA_COMPRAS = 'compras';
    public const ENCOMIENDA_RECOGIDAS = 'recogidas';

    public static function normalizedEncomiendaKind(?string $kind): ?string
    {
        $kind = is_string($kind) ? trim($kind) : '';
        if ($kind === self::ENCOMIENDA_RECOGIDAS) {
            return self::ENCOMIENDA_RECOGIDAS;
        }
        if ($kind === self::ENCOMIENDA_COMPRAS) {
            return self::ENCOMIENDA_COMPRAS;
        }

        return null;
    }

    public static function isEncomiendaCompras(?string $kind): bool
    {
        return self::normalizedEncomiendaKind($kind) === self::ENCOMIENDA_COMPRAS;
    }

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

    public static function shouldPersistCourierRow(
        int $vehicleServiceId,
        ?string $errandType,
        ?int $requestedVehicleServiceId = null,
    ): bool {
        if ($vehicleServiceId === 4) {
            return true;
        }

        return in_array($errandType, [self::ERRAND_ENCOMIENDA, self::ERRAND_DELIVERY, self::ERRAND_ACARREO], true);
    }

    public static function normalizedErrandType(
        ?string $errandType,
        int $vehicleServiceId,
        ?int $requestedVehicleServiceId = null,
    ): ?string {
        $errandType = is_string($errandType) ? trim($errandType) : '';

        if ($errandType === self::ERRAND_ENCOMIENDA) {
            return self::ERRAND_ENCOMIENDA;
        }
        if ($errandType === self::ERRAND_DELIVERY) {
            return self::ERRAND_DELIVERY;
        }
        if ($errandType === self::ERRAND_ACARREO) {
            return self::ERRAND_ACARREO;
        }
        if ($vehicleServiceId === 4) {
            return self::ERRAND_DELIVERY;
        }

        // service_id 1/3/5 is shared between Viajes and Envíos — never infer delivery without errand_type.
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
