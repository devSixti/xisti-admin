<?php

namespace App\Providers;

use App\Models\GeneralSettings;
use App\Models\LanguageConstant;
use App\Models\WorldCurrency;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    protected $general_settings;
    public $currency = "COP";
    protected $chat_replace_domain;
    public function boot()
    {
        Schema::defaultStringLength(191);
//        URL::forceScheme('https');

        Paginator::useBootstrap();
        view()->composer('*',function ($view){
            if (Cookie::get('current_address')){
                $current_address =  Cookie::get('current_address');
            } else {
                $current_address = "";
            }
            $data = array(
                'current_address'=>$current_address,
            );
            $view->with($data);
        });
        if (Schema::hasTable('language_constant')) {
            $lang_constant = LanguageConstant::query()->select()->groupBy('constant_name')->get()->keyBy('constant_name')->toArray();
            config(['global.lang_constant' => $lang_constant]);
        } else {
            config(['global.lang_constant' => []]);
        }

        $get_host = request()->getHost();
        $this->chat_replace_domain = preg_replace("/[\s_\-\.]/", "-", $get_host);

        if (Schema::hasTable('general_settings')) {
            $this->general_settings = GeneralSettings::query()->first();
        }

        try {
            if (Schema::hasTable('world_currency')) {
                $user_default_currency = WorldCurrency::query()->where('default_currency', 1)->first();
                if ($user_default_currency != null) {
                    $this->currency = $user_default_currency->symbol;
                }
            }
        } catch (\Exception $e) {
        }

        view()->composer('*', function ($view) {
            $view->with('general_settings', $this->general_settings);
            $view->with('currency_symbol', $this->currency);
            $view->with('chat_replace_domain', $this->chat_replace_domain);
        });

        if ($this->general_settings !== null) {
            config(['session.lifetime' => $this->general_settings->login_timeout_time]);
        }

        request()->attributes->add([
            'general_settings' => $this->general_settings,
            'currency_symbol' => $this->currency,
            'chat_replace_domain' => $this->chat_replace_domain,
        ]);

//        $this->rateLimit('daily-map-call-limit', 30);
    }
    private function rateLimit($key,$count): void
    {
        RateLimiter::for($key, function (Request $request) use ($count) {
            // Hard validation
            if (! $request->header('session-id')) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'Session ID is required.'
                ], 400);
            }

            // per day limit for uniqueid + session combination (session_id = 'uid:' . $uniqueId . '|sess:' . $session)
            return Limit::perDay($count)
                ->by($request->header('session-id'))
                ->response(fn () => response()->json([
                    'status'  => 0,
                    'message' => 'You’ve reached your daily Google Maps usage limit. Please try again tomorrow.'
                ], 429));
        });
    }
    public function register()
    {
        //
    }
}
