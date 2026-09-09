<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

/**
 * Reads the trusted proxy list from config at request time (bootstrap/app.php
 * runs before configuration is loaded, so it cannot read config there).
 */
class TrustProxies extends Middleware
{
    protected $headers = Request::HEADER_X_FORWARDED_FOR
        | Request::HEADER_X_FORWARDED_HOST
        | Request::HEADER_X_FORWARDED_PORT
        | Request::HEADER_X_FORWARDED_PROTO;

    /**
     * @return array<int, string>|string|null
     */
    protected function proxies()
    {
        $proxies = config('security.trusted_proxies');

        return $proxies === ['*'] ? '*' : $proxies;
    }
}
