<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\Cors;
use App\Http\Middleware\PreventBrowserCache;
use App\Http\Middleware\HandleTokenMismatch;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->redirectGuestsTo('/login');

        $middleware->web(append: [
            PreventBrowserCache::class,
        ]);

        $middleware->web(prepend: [
            HandleTokenMismatch::class,
            // Cors ikut dipasang di sini (dulu filenya ada tapi tidak pernah
            // didaftarkan = dead code). Prepend -> jalan sebelum middleware lain
            // supaya header CORS tetap terkirim pada respons redirect/403.
            Cors::class,
        ]);

        $middleware->alias([
            'role' => CheckRole::class,
        ]);
    })
    ->withExceptions(function (Illuminate\Foundation\Configuration\Exceptions $exceptions): void {
        // Handler 419 ditangani middleware HandleTokenMismatch (lebih awal dari render default)
    })
    ->create();
