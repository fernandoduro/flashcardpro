<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiVersionHeader
{
    /**
     * Handle an incoming request and add API version header to response.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);
        $response->headers->set('X-API-Version', '1.0.0');

        return $response;
    }
}
