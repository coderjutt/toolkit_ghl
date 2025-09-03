<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        // Register custom commands here
        \App\Console\Commands\GenerateModuleControllers::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Define scheduled tasks here
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
    }

    protected $routeMiddleware = [
        // ...
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
    ];

}
