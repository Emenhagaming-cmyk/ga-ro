<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleTokenMismatch
{
    /**
     * Handle an incoming request.
     *
     * Router Pipeline mengubah TokenMismatchException dari ValidateCsrfToken
     * menjadi response 419 di lapisan pipe (exception tidak pernah sampai ke
     * middleware luar), jadi penanganan dilakukan pada response yang keluar.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->getStatusCode() === 419 && $request->getMethod() !== 'GET') {
            return $this->handleMismatch($request);
        }

        return $response;
    }

    private function handleMismatch(Request $request): Response
    {
        $session = $request->session();

        // ponytail: cabang draft pendaftaran DIHAPUS di backend-admin — tidak ada route
        // pendaftaran.create di sini, jadi redirect()-nya cuma jadi RouteNotFoundException (500).
        if ($request->is('logout')) {
            $session->save();
            return redirect(frontendAuthUrl());
        }

        $session->flash('error', 'Session berakhir. Silakan coba lagi.');
        $session->save();

        return redirect()->back()->withInput();
    }
}