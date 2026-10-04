<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            return redirect()->route('login');
        }

        if (in_array($request->user()->role, $roles)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // ponytail: sudah login tapi role salah -> 403, JANGAN redirect ke /login.
        // Redirect itu bikin loop: /login (middleware guest) -> / (tidak ada route
        // 'dashboard'/'home' jadi defaultRedirectUri() jatuh ke '/') -> /admin -> /login -> ...
        abort(403, 'Akun Anda tidak memiliki akses ke panel admin ini.');
    }
}