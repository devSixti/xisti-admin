<?php

namespace App\Classes;

use App\Services\AppAuthorizationService;
use App\Services\FcmAccessTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * XISTI replacement for the legacy SourceGuardian-protected AuthAlertClass.
 * Keeps the same public API used by auth controllers and notification helpers.
 */
class AuthAlertClass
{
    public function checkAuthorizationApp(Request $request): JsonResponse
    {
        return app(AppAuthorizationService::class)->checkAuthorizationApp($request);
    }

    public function fetchFCMBearerToken(): string
    {
        return app(FcmAccessTokenService::class)->fetchFCMBearerToken();
    }
}
