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
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

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
            if ($this->general_settings === null) {
                // Keep admin/pages usable even before first settings save.
                $fallback = new GeneralSettings();
                $fallback->website_name = config('xisti.product_name', 'XISTI');
                $fallback->copy_right = config('xisti.product_name', 'XISTI') . ' - ' . config('xisti.tagline', 'Fácil y Seguro');
                $fallback->login_timeout_time = 120;
                $fallback->report_chat_history_delete = 0;
                $fallback->chat_deletion_days_after_issue_resolution = 7;
                $fallback->min_report_issue_image_upload = 1;
                $fallback->max_report_issue_image_upload = 5;
                $fallback->auto_settle_wallet = 0;
                $this->general_settings = $fallback;
            }
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

        $this->configureMapsDailyRateLimit();
    }

    private function configureMapsDailyRateLimit(): void
    {
        $dailyLimit = max(500, (int) config('xisti.maps_daily_limit', 10000));
        RateLimiter::for('daily-map-call-limit', function (Request $request) use ($dailyLimit) {
            $sessionId = trim((string) $request->header('session-id', ''));
            $userId = $request->get('user_id');
            $deviceIp = trim((string) $request->header('ip-address', ''));

            $bucket = $sessionId !== ''
                ? 'sess:'.$sessionId
                : ($userId ? 'uid:'.$userId : ($deviceIp !== '' ? 'dev:'.$deviceIp : 'ip:'.$request->ip()));

            return Limit::perDay($dailyLimit)
                ->by($bucket)
                ->response(function () use ($request) {
                    $lang = strtolower((string) $request->header('select-language', 'es'));
                    $message = str_starts_with($lang, 'es')
                        ? 'Alcanzaste el límite diario de uso de mapas. Inténtalo mañana.'
                        : 'You’ve reached your daily Google Maps usage limit. Please try again tomorrow.';

                    return response()->json([
                        'status' => 0,
                        'message' => $message,
                        'message_code' => 429,
                    ], 429);
                });
        });
    }

    /** @deprecated use configureMapsDailyRateLimit */
    private function rateLimit($key, $count): void
    {
        RateLimiter::for($key, function (Request $request) use ($count) {
            $bucket = $request->header('session-id')
                ?: ('uid:'.($request->get('user_id') ?? $request->ip()));

            return Limit::perDay($count)
                ->by($bucket)
                ->response(fn () => response()->json([
                    'status'  => 0,
                    'message' => 'You’ve reached your daily Google Maps usage limit. Please try again tomorrow.',
                    'message_code' => 429,
                ], 429));
        });
    }
    public function register()
    {
        //
    }
}
