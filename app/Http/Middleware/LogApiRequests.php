<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LogApiRequests
{
    /**
     * The rate limiter instance.
     */
    protected RateLimiter $limiter;

    /**
     * Create a new middleware instance.
     */
    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle an incoming request.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Apply Rate Limiting
        $this->ensureIsNotRateLimited($request);

        $startTime = microtime(true);

        // 2. Log the Request with enhanced context
        Log::info('API Request Started:', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user' => $request->user()?->id,
            'route' => $request->route()?->getName(),
            'headers' => [
                'accept' => $request->header('Accept'),
                'content_type' => $request->header('Content-Type'),
                'authorization_type' => $request->bearerToken() ? 'bearer' : 'none',
            ],
            'request_size' => strlen($request->getContent()),
            'timestamp' => now()->toISOString(),
        ]);

        // 3. Pass the request to the next middleware and capture response
        $response = $next($request);

        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2); // Convert to milliseconds

        // 4. Log the response
        Log::info('API Request Completed:', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'status_code' => $response->getStatusCode(),
            'duration_ms' => $duration,
            'response_size' => strlen($response->getContent()),
            'user' => $request->user()?->id,
            'route' => $request->route()?->getName(),
            'timestamp' => now()->toISOString(),
        ]);

        return $response;
    }

    /**
     * Ensure the incoming request is not rate limited.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        $key = $request->user() ? $request->user()->id : $request->ip();
        $maxAttempts = 60; // 60 requests...
        $decayMinutes = 1; // ...per minute

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = $this->limiter->availableIn($key);

            throw new HttpException(
                429,
                'Too Many Attempts.',
                null,
                ['Retry-After' => $retryAfter]
            );
        }

        $this->limiter->hit($key, $decayMinutes * 60);
    }
}