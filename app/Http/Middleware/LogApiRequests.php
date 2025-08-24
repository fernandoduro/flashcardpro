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

        // 2. Log the Request
        Log::info('API Request:', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user' => $request->user()?->id,
        ]);

        // 3. Pass the request to the next middleware
        return $next($request);
    }

    /**
     * Ensure the incoming request is not rate limited.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        $key = sha1($request->ip());
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