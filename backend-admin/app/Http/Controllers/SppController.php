<?php

namespace App\Http\Controllers;

use App\Models\User;

class SppController extends Controller
{
    public function rekapIndex()
    {
        $siswa = User::where('role', 'siswa')
            ->with(['sppBills' => fn ($q) => $q->with('payments')->orderBy('periode')])
            ->orderBy('name')
            ->get();

        return view('spp.rekap', ['siswa' => $siswa]);
    }
}