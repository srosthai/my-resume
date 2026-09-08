<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds defence-in-depth response headers, including a nonce-based CSP.
 *
 * The CSP is skipped while the Vite dev server is running (HMR needs
 * eval and a websocket) so local development is unaffected.
 */
class SecurityHeaders
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = Vite::useCspNonce();

        $response = $next($request);

        $headers = $response->headers;
        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('X-Frame-Options', 'DENY');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=()');

        if ($request->isSecure()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        if (config('security.csp.enabled') && ! (app()->isLocal() && Vite::isRunningHot())) {
            $headers->set('Content-Security-Policy', $this->contentSecurityPolicy($nonce));
        }

        return $response;
    }

    private function contentSecurityPolicy(string $nonce): string
    {
        $directives = [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'none'",
            "form-action 'self'",
            // Inline scripts (Ziggy routes, Vite tags) carry the request nonce.
            "script-src 'self' 'nonce-{$nonce}' https://www.youtube.com https://s.ytimg.com",
            // Vue/Reka set inline styles; the theme bootstrap style is inline too.
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://fonts.googleapis.com",
            "font-src 'self' https://fonts.bunny.net https://fonts.gstatic.com data:",
            // Tech-stack logos and YouTube thumbnails are external images.
            "img-src 'self' data: blob: https:",
            "media-src 'self' https:",
            'frame-src https://www.youtube.com https://www.youtube-nocookie.com',
            "connect-src 'self'",
            'upgrade-insecure-requests',
        ];

        return implode('; ', $directives);
    }
}
