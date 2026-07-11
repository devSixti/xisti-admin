<?php

namespace Tests\Unit;

use App\Helpers\MarketConfigHelper;
use Tests\TestCase;

class MarketConfigHelperTest extends TestCase
{
    public function test_catalog_includes_expanded_markets(): void
    {
        $ids = array_column(MarketConfigHelper::countries(), 'id');
        foreach (['co', 'us', 'br', 'ar', 'mx', 'es', 'cl', 'pe', 'ec'] as $id) {
            $this->assertContains($id, $ids);
        }
    }

    public function test_resolve_medellin_from_coordinates(): void
    {
        $resolved = MarketConfigHelper::resolveFromCoordinates(6.2476, -75.5658);
        $this->assertNotNull($resolved);
        $this->assertSame('co', $resolved['country_id']);
        $this->assertSame('medellin', $resolved['city_id']);
        $this->assertFalse($resolved['is_geocode_derived']);
    }

    public function test_resolve_mexico_city(): void
    {
        $resolved = MarketConfigHelper::resolveFromCoordinates(19.4326, -99.1332);
        $this->assertNotNull($resolved);
        $this->assertSame('mx', $resolved['country_id']);
        $this->assertSame('mexico_city', $resolved['city_id']);
    }

    public function test_outside_catalog_without_geocode_returns_null(): void
    {
        // Tokyo — no bbox hit; reverse geocode skipped without server key in unit test
        $resolved = MarketConfigHelper::resolveFromCoordinates(35.6762, 139.6503);
        $this->assertNull($resolved);
    }

    public function test_resolved_from_geocode_unknown_iso_uses_defaults(): void
    {
        $resolved = MarketConfigHelper::resolvedFromGeocode([
            'iso' => 'JP',
            'country_name' => 'Japan',
            'city_name' => 'Tokyo',
        ], 35.6762, 139.6503);

        $this->assertTrue($resolved['is_geocode_derived']);
        $this->assertSame('JP', $resolved['country']['iso_code']);
        $this->assertSame('$', $resolved['country']['currency_symbol']);
        $this->assertSame('Tokyo', $resolved['city']['display_name']);
    }

    public function test_city_ids_aligned_rio_and_cordoba(): void
    {
        $this->assertNotNull(MarketConfigHelper::cityById('rio_de_janeiro'));
        $this->assertNotNull(MarketConfigHelper::cityById('cordoba'));
        $this->assertNull(MarketConfigHelper::cityById('rio'));
        $this->assertNull(MarketConfigHelper::cityById('cordoba_ar'));
    }

    public function test_iso_defaults_for_known_and_default(): void
    {
        $mx = MarketConfigHelper::isoDefaults('MX');
        $this->assertSame('MXN', $mx['currency_code']);
        $fallback = MarketConfigHelper::isoDefaults('ZZ');
        $this->assertSame('USD', $fallback['currency_code']);
    }
}
