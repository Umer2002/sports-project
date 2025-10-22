<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // …

    protected $middlewareGroups = [
        'web' => [
            // existing web middleware…
        ],

        'api' => [
            // existing api middleware…
        ],
    ];

    /**
     * Route-specific middleware.
     *
     * Keys here are the short names you use in your routes.
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'role' => \App\Http\Middleware\CheckRole::class,
        'payment' => \App\Http\Middleware\CheckPayment::class,
    ];

}
