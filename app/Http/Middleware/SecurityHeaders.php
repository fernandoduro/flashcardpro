<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Prevent clickjacking attacks
        $response->headers->set('X-Frame-Options', 'DENY');

        // Enable XSS protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer policy for better privacy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions policy to restrict potentially harmful APIs
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=()');

        // Content Security Policy for additional protection
        // Allow necessary external resources while maintaining security
        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://code.highcharts.com http://localhost:5173",
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://fonts.googleapis.com http://localhost:5173",
            "img-src 'self' data: https:",
            "font-src 'self' data: https: http:",
            "connect-src 'self' ws://localhost:5173 http://localhost:5173",
            "media-src 'self'",
            "object-src 'none'",
            "worker-src 'self' blob: http://localhost:5173",
            "child-src 'self' blob:",
            "frame-ancestors 'none'",
            'upgrade-insecure-requests',
            'block-all-mixed-content',
        ];

        $response->headers->set('Content-Security-Policy', implode('; ', $csp));

        return $response;
    }
}
