<?php

namespace App\Http\Controllers;

use App\Models\User;

class SppController extends Controller
{
    public function rekapIndex()
    {
        // ponytail: safety limit 200 — mencegah hydrate ALL siswa+bills+payments saat data membesar
        $siswa = User::where('role', 'siswa')
            ->with(['sppBills' => fn ($q) => $q->with('payments')->orderBy('periode')])
            ->orderBy('name')
            ->take(200)
            ->get();

        return view('spp.rekap', ['siswa' => $siswa]);
    }
}