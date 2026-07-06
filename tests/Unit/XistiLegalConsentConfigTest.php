<?php

namespace Tests\Unit;

use Tests\TestCase;

class XistiLegalConsentConfigTest extends TestCase
{
    public function test_legal_consent_version_default_matches_zimo_baseline(): void
    {
        $this->assertSame('2026-06-legal-v1', config('xisti.legal.consent_version'));
    }

    public function test_legal_urls_point_to_admin_host(): void
    {
        $this->assertStringStartsWith('https://admin.xistiapp.com', config('xisti.legal.terms_url'));
        $this->assertStringStartsWith('https://admin.xistiapp.com', config('xisti.legal.privacy_url'));
    }
}
