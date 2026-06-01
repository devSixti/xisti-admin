<?php

namespace App\Http\Middleware;

use App\Helpers\AdminUi;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetAdminLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        App::setLocale(AdminUi::resolveLocale($request));

        return $next($request);
    }
}
