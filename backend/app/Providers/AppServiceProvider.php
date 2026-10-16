<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use App\Models\Lamaran;
use App\Observers\LamaranObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $identifier = mb_strtolower(trim((string) $request->input('username')));

            return [
                Limit::perMinute(5)->by('login:ip:'.$request->ip()),
                Limit::perMinute(10)->by('login:user:'.$identifier),
            ];
        });

        Lamaran::observe(LamaranObserver::class);
    }
}
