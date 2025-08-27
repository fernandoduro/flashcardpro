<?php

use App\Http\Middleware\LogApiRequests;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use \App\Http\Middleware\ApiVersionHeader;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);
        if (! env('REMOVE_SECURITY_HEADERS', false)) {
            $middleware->web(append: [
                SecurityHeaders::class,
            ]);
        }
        $middleware->alias([
            'log.api' => LogApiRequests::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);
        $middleware->api(append: [
            ApiVersionHeader::class,
        ]);
 
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
