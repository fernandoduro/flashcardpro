<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class ApiVersionHeader
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('X-API-Version', '1.0.0'); // Set current version
        return $response;
    }
}