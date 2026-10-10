<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\PreventBrowserCache;
use App\Http\Middleware\HandleTokenMismatch;
use App\Http\Middleware\Cors;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            PreventBrowserCache::class,
            SecurityHeaders::class,
        ]);

        $middleware->prepend(Cors::class);

        $middleware->web(prepend: [
            HandleTokenMismatch::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            // CLOSED: lamaran/tabungan/spp pakai X-CSRF-TOKEN header (same-site *.vercel.app → wajib)
        ]);

        $middleware->alias([
            'role' => CheckRole::class,
        ]);
    })
    ->withExceptions(function (Illuminate\Foundation\Configuration\Exceptions $exceptions): void {
        // Handler 419 ditangani middleware HandleTokenMismatch (lebih awal dari render default)

        // 429 dari throttle:login → pesan ramah, bukan halaman error default.
        // Pesan sengaja umum (tidak membocorkan apakah username terdaftar).
        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            $message = 'Terlalu banyak percobaan login. Coba lagi sebentar lagi.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 429);
            }

            if (! $request->hasSession()) {
                return response($message, 429);
            }

            return back()
                ->withErrors(['username' => $message])
                ->withInput($request->only('username'));
        });
    })
    ->create();
