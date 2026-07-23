<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('document_expiry')->dailyAt('00:01')->timezone('UTC');

Schedule::command('currency:sync-live-rates')
    ->dailyAt('06:00')
    ->timezone('America/Bogota')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/exchange-rates-sync.log'));

Schedule::call(function () {
    \App\Helpers\RideLifecycleHelper::expireStalePendingRides();
    \App\Helpers\RideLifecycleHelper::purgeOrphanRunningRides();
})->everyFiveMinutes()->name('ride-lifecycle-prune')->withoutOverlapping();

