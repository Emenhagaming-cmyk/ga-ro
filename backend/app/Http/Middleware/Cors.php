<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
// ponytail: allowlist = domain frontend produksi (Vercel) + VPS + dev lokal.
        // Default env() dipakai hanya kalau FRONTEND_URL belum di-set.
        $allowedOrigins = array_values(array_unique(array_filter([
            'https://smkbu-sby.vercel.app',
            env('FRONTEND_URL'),
            'https://smkbu-sby.my.id',
            'http://smkbu-sby.my.id',
            'http://localhost:5174',
        ])));

        $origin = $request->header('Origin');
        // Hanya kirim CORS header untuk origin yang dikenal; selain itu biarkan kosong.
        if (!in_array($origin, $allowedOrigins)) {
            return $next($request);
        }
        $allowOrigin = $origin;

        if ($request->isMethod('OPTIONS')) {
            return response('', 204)
                ->header('Access-Control-Allow-Origin', $allowOrigin)
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-XSRF-TOKEN')
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Access-Control-Max-Age', '86400');
        }

        return $next($request)
            ->header('Access-Control-Allow-Origin', $allowOrigin)
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-XSRF-TOKEN')
            ->header('Access-Control-Allow-Credentials', 'true');
    }
}
