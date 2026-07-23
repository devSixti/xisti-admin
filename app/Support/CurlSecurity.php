<?php

namespace App\Support;

class CurlSecurity
{
    public static function applyToCurlHandle($ch): void
    {
        $verify = filter_var(config('xisti.curl.ssl_verify_peer', true), FILTER_VALIDATE_BOOLEAN);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verify);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $verify ? 2 : 0);
    }
}
