<?php

namespace App\Helpers;

use App\Models\AdminAreaList;
use Illuminate\Support\Facades\Schema;

class MunicipioResolveHelper
{
    public static function catalogVersion(): string
    {
        return 'divipola-2026-07-11';
    }

    public static function catalogAvailable(): bool
    {
        return class_exists(\App\Models\Municipality::class)
            && Schema::hasTable('municipalities');
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function resolve(float $lat, float $lng): ?array
    {
        if (! self::catalogAvailable()) {
            return null;
        }

        $hit = \App\Models\Municipality::query()
            ->where('status', 1)
            ->where('min_lat', '<=', $lat)
            ->where('max_lat', '>=', $lat)
            ->where('min_lng', '<=', $lng)
            ->where('max_lng', '>=', $lng)
            ->orderByRaw(
                '(POW(center_lat - ?, 2) + POW(center_lng - ?, 2)) ASC',
                [$lat, $lng]
            )
            ->first();

        if ($hit === null) {
            $hit = self::nearestCentroid($lat, $lng);
        }

        if ($hit === null) {
            return null;
        }

        return self::payload($hit);
    }

    /**
     * @param  object  $m
     * @return array<string, mixed>
     */
    public static function payload(object $m): array
    {
        $areaId = $m->admin_area_list_id ?? null;
        if ($areaId === null) {
            $areaId = self::suggestAdminAreaId($m);
        }

        return [
            'dane_code' => $m->dane_code,
            'name' => $m->name,
            'department_code' => $m->department_code,
            'department_name' => $m->department_name,
            'center_lat' => (float) $m->center_lat,
            'center_lng' => (float) $m->center_lng,
            'min_lat' => (float) $m->min_lat,
            'max_lat' => (float) $m->max_lat,
            'min_lng' => (float) $m->min_lng,
            'max_lng' => (float) $m->max_lng,
            'admin_area_list_id' => $areaId,
            'display_label' => $m->name.', '.$m->department_name,
        ];
    }

    private static function nearestCentroid(float $lat, float $lng): ?object
    {
        if (! self::catalogAvailable()) {
            return null;
        }

        return \App\Models\Municipality::query()
            ->where('status', 1)
            ->whereRaw(
                '(6371 * acos( cos( radians(center_lat) ) * cos( radians(?) ) * cos( radians(?) - radians(center_lng) ) + sin( radians(center_lat) ) * sin(radians(?)) )) <= ?',
                [$lat, $lng, $lat, 35]
            )
            ->orderByRaw(
                '(POW(center_lat - ?, 2) + POW(center_lng - ?, 2)) ASC',
                [$lat, $lng]
            )
            ->first();
    }

    private static function suggestAdminAreaId(object $m): ?int
    {
        if (! class_exists(AdminAreaList::class)) {
            return null;
        }

        $name = mb_strtolower(trim((string) $m->name));
        $area = AdminAreaList::query()
            ->where('status', 1)
            ->whereRaw('LOWER(name) = ?', [$name])
            ->first();

        return $area?->id;
    }

    /**
     * Match free-text town name to DIVIPOLA (shared rides).
     *
     * @return array<string, mixed>|null
     */
    public static function matchByName(?string $town, ?string $department = null): ?array
    {
        if (! self::catalogAvailable()) {
            return null;
        }

        $town = trim((string) $town);
        if ($town === '') {
            return null;
        }

        $q = \App\Models\Municipality::query()->where('status', 1)->whereRaw('LOWER(name) = ?', [mb_strtolower($town)]);
        if ($department !== null && trim($department) !== '') {
            $q->whereRaw('LOWER(department_name) = ?', [mb_strtolower(trim($department))]);
        }
        $hit = $q->first();
        if ($hit === null) {
            $hit = \App\Models\Municipality::query()
                ->where('status', 1)
                ->whereRaw('LOWER(name) LIKE ?', [mb_strtolower($town).'%'])
                ->orderBy('name')
                ->first();
        }

        return $hit ? self::payload($hit) : null;
    }

    public static function linkAdminAreasByName(): int
    {
        if (! self::catalogAvailable() || ! class_exists(AdminAreaList::class)) {
            return 0;
        }

        $updated = 0;
        $areas = AdminAreaList::query()->where('status', 1)->get(['id', 'name']);
        foreach ($areas as $area) {
            $n = mb_strtolower(trim((string) $area->name));
            $count = \App\Models\Municipality::query()
                ->whereNull('admin_area_list_id')
                ->whereRaw('LOWER(name) = ?', [$n])
                ->update(['admin_area_list_id' => $area->id]);
            $updated += (int) $count;
        }

        return $updated;
    }
}
