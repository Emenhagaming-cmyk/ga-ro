<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\Cors;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\PreventBrowserCache;
use App\Http\Middleware\HandleTokenMismatch;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(append: [
            Cors::class,
        ]);

        $middleware->web(append: [
            PreventBrowserCache::class,
        ]);

        $middleware->web(prepend: [
            HandleTokenMismatch::class,
        ]);

        $middleware->alias([
            'role' => CheckRole::class,
        ]);
    })
    ->withExceptions(function (Illuminate\Foundation\Configuration\Exceptions $exceptions): void {
        // Handler 419 ditangani middleware HandleTokenMismatch (lebih awal dari render default)
    })
    ->create();
