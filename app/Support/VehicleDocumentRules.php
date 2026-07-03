<?php

namespace App\Support;

final class VehicleDocumentRules
{
    /** Document name substrings excluded for bicycle couriers. */
    private const BICYCLE_EXCLUDED = [
        'placa', 'plate', 'soat', 'tecnomecan', 'tecno', 'licencia del veh', 'vehicle license',
    ];

    /** Document types that accept front/back uploads. */
    private const NATIONAL_ID_MARKERS = [
        'cedula', 'cédula', 'national id', 'documento de identidad', 'dni',
    ];

    public static function isBicycleRegistration(?string $registrationKey, ?string $deliveryVariant): bool
    {
        $key = strtolower(trim((string) ($registrationKey ?? $deliveryVariant ?? '')));

        return $key === 'bicycle' || str_contains($key, 'bicicleta');
    }

    /**
     * @param  iterable<object|array<string, mixed>>  $documents
     * @return list<object|array<string, mixed>>
     */
    public static function filterForVehicle(iterable $documents, ?string $registrationKey, ?string $deliveryVariant): array
    {
        $bicycle = self::isBicycleRegistration($registrationKey, $deliveryVariant);
        $filtered = [];
        foreach ($documents as $doc) {
            $name = strtolower(is_array($doc) ? ($doc['name'] ?? $doc['document_name'] ?? '') : ($doc->name ?? $doc->document_name ?? ''));
            if ($bicycle && self::matchesAny($name, self::BICYCLE_EXCLUDED)) {
                continue;
            }
            $filtered[] = $doc;
        }

        return $filtered;
    }

    public static function supportsNationalIdSides(string $documentName): bool
    {
        return self::matchesAny(strtolower($documentName), self::NATIONAL_ID_MARKERS);
    }

    /** @param list<string> $needles */
    private static function matchesAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, strtolower($needle))) {
                return true;
            }
        }

        return false;
    }
}
