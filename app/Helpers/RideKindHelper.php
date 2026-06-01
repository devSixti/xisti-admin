<?php

namespace App\Helpers;

class RideKindHelper
{
    /**
     * @param  object|array<string, mixed>  $row
     */
    public static function isDeliveryRide(object|array $row): bool
    {
        if (EncomiendaHelper::isEncomiendaRide($row)) {
            return false;
        }

        $serviceId = self::serviceIdFromRow($row);
        if ($serviceId === 4) {
            return true;
        }

        $item = self::stringField($row, 'item_description');
        $recipient = self::stringField($row, 'recipient_name');

        return $item !== '' || $recipient !== '';
    }

    /**
     * @param  object|array<string, mixed>  $row
     */
    public static function isDeliveryFlag(object|array $row): int
    {
        return self::isDeliveryRide($row) ? 1 : 0;
    }

    /**
     * @param  object|array<string, mixed>  $row
     */
    private static function serviceIdFromRow(object|array $row): int
    {
        if (is_array($row)) {
            return (int) ($row['service_id'] ?? $row['vehicle_service_id'] ?? 0);
        }

        return (int) ($row->service_id ?? $row->vehicle_service_id ?? 0);
    }

    /**
     * @param  object|array<string, mixed>  $row
     */
    private static function stringField(object|array $row, string $key): string
    {
        $value = is_array($row) ? ($row[$key] ?? null) : ($row->{$key} ?? null);

        return is_string($value) ? trim($value) : '';
    }
}
