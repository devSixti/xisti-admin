<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class SetLocalLang
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (session()->has('locale')) {
            App::setLocale(session()->get('locale'));
        }
        else {
            $language = $request->header('select-language');
            if ($language === null || trim((string) $language) === '') {
                $language = 'es';
            } else {
                $language = strtolower(trim((string) $language));
                if (str_contains($language, '-')) {
                    $language = explode('-', $language)[0];
                }
            }
            App::setLocale($language);
        }
        return $next($request);
    }
}
