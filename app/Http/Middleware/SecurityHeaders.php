<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        // HTMX uses inline handlers and evaluates hx-vals/js: expressions, so
        // unsafe-eval/unsafe-inline are required until those are refactored.
        if (! $response->headers->has('Content-Security-Policy')) {
            $response->headers->set(
                'Content-Security-Policy',
                "default-src 'self'; "
                . "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://mc.yandex.ru https://*.yandex.ru; "
                . "style-src 'self' 'unsafe-inline'; "
                . "img-src 'self' data: https:; "
                . "font-src 'self' data:; "
                . "connect-src 'self' https://mc.yandex.ru https://*.yandex.ru; "
                . "frame-src 'self' https://*.yandex.ru; "
                . "frame-ancestors 'none'; "
                . "base-uri 'self'; "
                . "object-src 'none'; "
                . "form-action 'self'"
            );
        }

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
