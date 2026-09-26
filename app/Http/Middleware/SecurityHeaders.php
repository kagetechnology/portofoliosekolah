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
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // CSP: allow Tailwind CDN + Alpine.js + Gravatar + same-origin storage
        $response->headers->set('Content-Security-Policy',
            "default-src 'self'; ".
            "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdn.ckeditor.com; ".
            "style-src 'self' 'unsafe-inline'; ".
            "font-src 'self' data:; ".
            "img-src 'self' data: blob: https:; ".
            "connect-src 'self'; ".
            'frame-src https://www.youtube.com https://www.youtube-nocookie.com https://player.vimeo.com; '.
            "frame-ancestors 'self'"
        );

        return $response;
    }
}
