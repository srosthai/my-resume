<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | The site is served through Cloudflare, so the real client address
    | arrives in X-Forwarded-For. Only Cloudflare's published ranges are
    | trusted by default; override with a comma-separated TRUSTED_PROXIES
    | (or "*" when the origin is unreachable except via the proxy).
    |
    */

    'trusted_proxies' => env('TRUSTED_PROXIES') !== null
        ? array_map('trim', explode(',', (string) env('TRUSTED_PROXIES')))
        : [
            // Cloudflare IPv4 (https://www.cloudflare.com/ips-v4)
            '173.245.48.0/20', '103.21.244.0/22', '103.22.200.0/22', '103.31.4.0/22',
            '141.101.64.0/18', '108.162.192.0/18', '190.93.240.0/20', '188.114.96.0/20',
            '197.234.240.0/22', '198.41.128.0/17', '162.158.0.0/15', '104.16.0.0/13',
            '104.24.0.0/14', '172.64.0.0/13', '131.0.72.0/22',
            // Cloudflare IPv6 (https://www.cloudflare.com/ips-v6)
            '2400:cb00::/32', '2606:4700::/32', '2803:f800::/32', '2405:b500::/32',
            '2405:8100::/32', '2a06:98c0::/29', '2c0f:f248::/32',
        ],

    /*
    |--------------------------------------------------------------------------
    | Content Security Policy
    |--------------------------------------------------------------------------
    */

    'csp' => [
        'enabled' => (bool) env('CSP_ENABLED', true),
    ],
];
