<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Throttle khusus POST /login, dua lapis:
        //  - per-IP: hentikan satu host yang menghajar endpoint
        //  - per-kredensial: hentikan tebak-tebakan terdistribusi ke satu akun
        // Dua Limit dikembalikan sebagai array → keduanya harus lolos.
        // Kredensial di-lowercase supaya "Admin"/"ADMIN" tidak menembus limit.
        RateLimiter::for('login', function (Request $request) {
            $identifier = mb_strtolower(trim((string) $request->input('username')));

            return [
                Limit::perMinute(5)->by('login:ip:'.$request->ip()),
                Limit::perMinute(10)->by('login:user:'.$identifier),
            ];
        });
    }
}
