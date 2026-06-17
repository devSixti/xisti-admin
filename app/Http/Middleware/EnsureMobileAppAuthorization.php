<?php

namespace App\Http\Middleware;

use App\Services\AppAuthorizationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMobileAppAuthorization
{
    public function handle(Request $request, Closure $next): Response
    {
        $service = app(AppAuthorizationService::class);
        $response = $service->checkAuthorizationApp($request);
        $data = $response->getData(true);
        if (($data['status'] ?? 0) !== 1) {
            return $response;
        }

        return $next($request);
    }
}
