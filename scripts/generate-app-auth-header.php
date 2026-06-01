#!/usr/bin/env php
<?php

use App\Services\AppAuthorizationService;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$appKey = $argv[1] ?? env('XISTI_APP_KEY', 'XistiChangeThisAppKeyBeforeProduction');
$rawOnly = in_array('--raw', $argv, true);
$service = app(AppAuthorizationService::class);
$header = $service->buildAuthorizationHeader($appKey);

if ($rawOnly) {
    echo $header;
    exit(0);
}

echo "app_key: {$appKey}\n";
echo "expected_digest: ".$service->buildExpectedDigest($appKey)."\n";
echo "authorization_header:\n{$header}\n";
