<?php

use App\Models\Pendaftaran;

/**
 * URL landing page + payload status login (?auth=...) supaya frontend tahu
 * status auth TANPA cookie third-party (diblokir browser mobile).
 */
function frontendAuthUrl(): string
{
    $frontend = env('FRONTEND_URL', 'http://localhost:5174');
    $payload = ['logged_in' => false, 'role' => null, 'name' => null, 'has_pendaftaran' => false, 'status' => null];

    if (auth()->check()) {
        $user = auth()->user();
        $pendaftaran = Pendaftaran::where('user_id', $user->id)->first();
        $role = $user->role;
        if ($role === 'pendaftar' && $pendaftaran?->status === 'diterima') {
            $role = 'siswa';
        }
        $payload = [
            'logged_in' => true,
            'role' => $role,
            'name' => $user->name,
            'has_pendaftaran' => (bool) $pendaftaran,
            'status' => $pendaftaran?->status,
        ];
    }

    return $frontend . '/?auth=' . rawurlencode(base64_encode(json_encode($payload)));
}
