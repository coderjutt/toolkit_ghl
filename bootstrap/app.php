<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Contracts\Http\Kernel as HttpKernelContract;
use App\Http\Kernel as HttpKernel;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // leave empty or configure if needed
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // leave empty or configure if needed
    })
    ->withBindings([
        HttpKernelContract::class => HttpKernel::class,
        ConsoleKernelContract::class => ConsoleKernel::class, // ✅ correct binding format
    ])

    ->create();
