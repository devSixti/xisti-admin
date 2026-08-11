<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'revalidate' => \App\Http\Middleware\RevalidateBackHistory::class,
            'adminrole' =>\App\Http\Middleware\adminRole::class,
            'adminMfa' => \App\Http\Middleware\EnsureAdminMfaVerified::class,
            'VerifyUser' => \App\Http\Middleware\VerifyUser::class,
            'setLocaleLang' => \App\Http\Middleware\SetLocalLang::class,
            'setAdminLocale' => \App\Http\Middleware\SetAdminLocale::class,
            'mobile.app' => \App\Http\Middleware\EnsureMobileAppAuthorization::class,
            'mobile.user' => \App\Http\Middleware\EnsureMobileApiUser::class,
            'mobile.credentials' => \App\Http\Middleware\EnsureMobileApiCredentials::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            '/webhook/wompi',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function (\Illuminate\Http\Request $request, \Throwable $e) {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->renderable(function (NotFoundHttpException $e) {
            if ($e instanceof \Illuminate\Session\TokenMismatchException) {
                if (request()->is('admin') || request()->is('admin/*')) {
                    return redirect()->route("get:admin:login")->with('error', 'You page session expired. Please try again');
                }
                return redirect()->route('get:homepage')->with('error', 'You page session expired. Please try again');
            }
        });

        $exceptions->reportable(function (NotFoundHttpException $e) {
            if ($e instanceof \Illuminate\Session\TokenMismatchException) {
                if (request()->is('admin') || request()->is('admin/*')) {
                    return redirect()->route("get:admin:login")->with('error', 'You page session expired. Please try again');
                }
                return redirect()->route('get:homepage')->with('error', 'You page session expired. Please try again');
            }
        });
    })->create();
