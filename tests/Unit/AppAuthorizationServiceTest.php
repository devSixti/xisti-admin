<?php

namespace Tests\Unit;

use App\Services\AppAuthorizationService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AppAuthorizationServiceTest extends TestCase
{
    #[Test]
    public function it_builds_and_validates_authorization_header_digest(): void
    {
        $service = new AppAuthorizationService;
        $appKey = 'XistiTestAppKey123';
        $header = $service->buildAuthorizationHeader($appKey);

        $this->assertSame(132, strlen($header));
        $this->assertSame(
            $service->buildExpectedDigest($appKey),
            $service->extractDigestFromAuthorization($header)
        );
    }

    #[Test]
    public function it_rejects_invalid_authorization_header(): void
    {
        $service = new AppAuthorizationService;

        $this->assertNull($service->extractDigestFromAuthorization('too-short'));
    }
}
