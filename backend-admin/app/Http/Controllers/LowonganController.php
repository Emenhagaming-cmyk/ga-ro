<?php

namespace App\Http\Controllers;

use App\Models\Lamaran;
use App\Models\Lowongan;
use Illuminate\Http\Request;

class LowonganController extends Controller
{
    public function index(Request $request)
    {
        $query = Lowongan::query();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                    ->orWhere('company', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $lowongans = $query->withCount('lamarans')->latest()->paginate(15)->withQueryString();

        return view('lowongan.index', compact('lowongans'));
    }

    public function create()
    {
        return view('lowongan.form', [
            'lowongan' => new Lowongan([
                'type' => 'Magang',
                'jurusan' => 'RPL',
                'work_arrangement' => 'onsite',
                'is_active' => true,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        Lowongan::create($this->validated($request));

        return redirect()->route('lowongan.index')->with('success', 'Lowongan berhasil ditambahkan.');
    }

    public function edit(Lowongan $lowongan)
    {
        return view('lowongan.form', compact('lowongan'));
    }

    public function update(Request $request, Lowongan $lowongan)
    {
        $lowongan->update($this->validated($request));

        return redirect()->route('lowongan.index')->with('success', 'Lowongan berhasil diperbarui.');
    }

    public function toggle(Lowongan $lowongan)
    {
        $lowongan->update(['is_active' => ! $lowongan->is_active]);

        return back()->with('success', $lowongan->is_active ? 'Lowongan diaktifkan.' : 'Lowongan dinonaktifkan.');
    }

    public function destroy(Lowongan $lowongan)
    {
        // lamarans.lowongan_id punya FK cascade → menghapus lowongan ikut menghapus
        // riwayat pelamar. Arahkan ke nonaktifkan saja kalau sudah ada pelamar.
        if ($lowongan->lamarans()->exists()) {
            return back()->with('error', 'Lowongan ini sudah punya pelamar. Nonaktifkan saja, jangan dihapus.');
        }

        $lowongan->delete();

        return back()->with('success', 'Lowongan dihapus.');
    }

    public function statistik()
    {
        $perStatus = Lamaran::selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status');

        $perTipe = Lowongan::selectRaw('type, COUNT(*) as cnt')
            ->groupBy('type')
            ->pluck('cnt', 'type');

        $perJurusan = Lowongan::selectRaw('jurusan, COUNT(*) as cnt')
            ->groupBy('jurusan')
            ->orderByDesc('cnt')
            ->pluck('cnt', 'jurusan');

        $topLowongan = Lowongan::withCount('lamarans')
            ->orderByDesc('lamarans_count')
            ->limit(10)
            ->get();

        // Tren 6 bulan terakhir; dihitung di PHP supaya jalan juga di sqlite.
        $tren = [];
        for ($i = 5; $i >= 0; $i--) {
            $tren[now()->subMonths($i)->format('Y-m')] = 0;
        }
        $createdAts = Lamaran::where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->pluck('created_at');
        foreach ($createdAts as $createdAt) {
            $bulan = $createdAt->format('Y-m');
            if (array_key_exists($bulan, $tren)) {
                $tren[$bulan]++;
            }
        }

        $trenList = [];
        foreach ($tren as $bulan => $total) {
            $trenList[] = ['bulan' => $bulan, 'total' => (int) $total];
        }

        return view('lowongan.statistik', [
            'totalLowongan' => Lowongan::count(),
            'lowonganAktif' => Lowongan::where('is_active', true)->count(),
            'totalLamaran' => Lamaran::count(),
            'perStatus' => $perStatus,
            'perTipe' => $perTipe,
            'perJurusan' => $perJurusan,
            'topLowongan' => $topLowongan,
            'tren' => $trenList,
        ]);
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'jurusan' => 'required|string|max:50',
            'type' => 'required|in:Magang,Kerja,BKK',
            'work_arrangement' => 'nullable|in:onsite,hybrid,remote',
            'description' => 'required|string',
            'deadline' => 'nullable|date',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
