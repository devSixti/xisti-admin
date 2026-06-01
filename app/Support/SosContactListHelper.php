<?php

namespace App\Support;

use App\Models\Sos;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SosContactListHelper
{
    public static function forUser(?User $user, string $userLangPrefix = ''): Collection
    {
        $adminContacts = Sos::query()
            ->select(
                'id',
                $userLangPrefix . 'name as name',
                'country_code',
                DB::raw("CONCAT('',contact_number) as contact_number")
            )
            ->where('status', 1)
            ->get();

        if ($user === null) {
            return $adminContacts;
        }

        $emergencyContact = trim((string) ($user->emergency_contact ?? ''));
        if ($emergencyContact === '') {
            return $adminContacts;
        }

        $countryCode = $user->emergency_country_code ?: $user->country_code ?: '+57';
        $displayName = trim((string) ($user->emergency_contact_name ?? ''));
        if ($displayName === '') {
            $displayName = 'Contacto de emergencia';
        }

        $emergencyEntry = (object) [
            'id' => 0,
            'name' => $displayName,
            'country_code' => $countryCode,
            'contact_number' => $emergencyContact,
        ];

        $normalizedEmergency = self::normalizePhoneKey($countryCode, $emergencyContact);
        $filtered = $adminContacts->filter(function ($contact) use ($normalizedEmergency) {
            return self::normalizePhoneKey(
                (string) ($contact->country_code ?? ''),
                (string) ($contact->contact_number ?? '')
            ) !== $normalizedEmergency;
        })->values();

        return collect([$emergencyEntry])->concat($filtered);
    }

    private static function normalizePhoneKey(string $countryCode, string $contactNumber): string
    {
        $digits = preg_replace('/\D+/', '', $countryCode . $contactNumber);

        return $digits ?? '';
    }
}
