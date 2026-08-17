<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
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
        try {
            $response = $next($request);
        } catch (TokenMismatchException $e) {
            $response = $this->handleMismatch($request);
        }

        if ($response->getStatusCode() === 419 && $request->getMethod() !== 'GET') {
            return $this->handleMismatch($request);
        }

        return $response;
    }

    private function handleMismatch(Request $request): Response
    {
        $session = $request->session();

        if ($request->is('pendaftaran')) {
            $draft = collect($request->except(['_token', '_method']))
                ->reject(fn ($v) => $v instanceof \Illuminate\Http\UploadedFile)
                ->all();

            $session->put('pending_pendaftaran', $draft);

            $session->flash('error', 'Session berakhir. Data Anda sudah tersimpan otomatis - periksa kembali lalu kirim ulang.');
            $session->save();

            return redirect()->route('pendaftaran.create');
        }

        if ($request->is('logout')) {
            $session->save();
            $frontend = env('FRONTEND_URL') ?: 'http://localhost:5174';
            return redirect($frontend . '/?no-intro=1');
        }

        $session->flash('error', 'Session berakhir. Silakan coba lagi.');
        $session->save();

        return redirect()->back()->withInput();
    }
}