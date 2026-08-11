<?php

namespace App\Http\Middleware;

use App\Services\AdminMfaService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminMfaVerified
{
    public function __construct(private readonly AdminMfaService $mfaService)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (!config('admin.mfa_required')) {
            return $next($request);
        }

        $admin = Auth::guard('admin')->user();
        if ($admin === null) {
            return redirect()->route('get:admin:login');
        }

        if ((int) ($admin->status ?? 1) === 0) {
            Auth::guard('admin')->logout();

            return redirect()->route('get:admin:login')->with('error', 'Your account is suspended.');
        }

        if ((int) ($admin->must_change_password ?? 0) === 1) {
            if (!$request->routeIs('get:admin:change_password', 'post:admin:change_password', 'admin:logout')) {
                return redirect()->route('get:admin:change_password')
                    ->with('error', 'You must change your password before continuing.');
            }

            return $next($request);
        }

        if ($request->routeIs(
            'get:admin:mfa.*',
            'post:admin:mfa.*',
            'get:admin:security',
            'admin:logout',
            'get:admin:change_password',
            'post:admin:change_password',
        )) {
            return $next($request);
        }

        if (!$this->mfaService->isEnrolled($admin)) {
            return redirect()->route('get:admin:mfa.enroll')
                ->with('error', 'Configure two-factor authentication to continue.');
        }

        if (!$this->mfaService->sessionIsVerified()) {
            return redirect()->route('get:admin:mfa.verify')
                ->with('error', 'Enter your authenticator code to continue.');
        }

        return $next($request);
    }
}
