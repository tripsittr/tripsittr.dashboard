<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        // global middleware stack (trimmed)
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    protected $middlewareGroups = [
        'web' => [
            // default Laravel web group pieces (simplified)
            'middleware.set-permission-team',
            \App\Http\Middleware\EnforceAppMaintenance::class,
        ],
        'api' => [
            'throttle:api',
            'middleware.set-permission-team',
            \App\Http\Middleware\EnforceAppMaintenance::class,
        ],
    ];

    protected $routeMiddleware = [
        'middleware.set-permission-team' => \App\Http\Middleware\SetPermissionTeamContext::class,
    ];
}
