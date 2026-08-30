<?php

use App\Models\Pendaftaran;

/**
 * Format periode SPP "Y-m" → "Agustus 2026" (locale independen).
 */
function formatPeriode(string $periode): string
{
    $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    [$y, $m] = array_pad(explode('-', $periode), 2, '01');

    return ($bulan[((int) $m) - 1] ?? '') . ' ' . $y;
}

/**
 * Format periode SPP "Y-m" → "Agu 26" (locale independen, utk header tabel).
 */
function formatPeriodeShort(string $periode): string
{
    $pendek = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    [$y, $m] = array_pad(explode('-', $periode), 2, '01');

    return ($pendek[((int) $m) - 1] ?? '') . ' ' . substr((string) $y, 2, 2);
}

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
