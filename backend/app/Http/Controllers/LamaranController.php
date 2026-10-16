<?php

namespace App\Http\Controllers;

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Bookmark;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LamaranController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();

        $totalLamaran = Lamaran::where('user_id', $user->id)->count();
        $lamaranPending = Lamaran::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();
        $bookmarkCount = Bookmark::where('user_id', $user->id)->count();

        // Deadline terdekat = lowongan tersimpan dengan tanggal paling dekat,
        // bukan yang paling baru disimpan.
        $upcomingBookmark = Bookmark::where('user_id', $user->id)
            ->whereHas('lowongan', function ($q) {
                $q->where('deadline', '>=', now()->toDateString())
                  ->where('is_active', true);
            })
            ->with('lowongan')
            ->get()
            ->sortBy(fn ($bookmark) => $bookmark->lowongan->deadline)
            ->first();

        $nearestDeadline = null;
        if ($upcomingBookmark) {
            $nearestDeadline = [
                'title' => $upcomingBookmark->lowongan->title,
                'company' => $upcomingBookmark->lowongan->company,
                'type' => $upcomingBookmark->lowongan->type,
                'deadline' => $upcomingBookmark->lowongan->deadline,
            ];
        }

        $lamaranDiterima = Lamaran::where('user_id', $user->id)
            ->where('status', 'diterima')
            ->count();

        $lamaranDitolak = Lamaran::where('user_id', $user->id)
            ->where('status', 'ditolak')
            ->count();

        return response()->json([
            'total_lamaran' => $totalLamaran,
            'lamaran_pending' => $lamaranPending,
            'lamaran_diterima' => $lamaranDiterima,
            'lamaran_ditolak' => $lamaranDitolak,
            'bookmark_count' => $bookmarkCount,
            'upcoming_deadline' => $nearestDeadline,
        ]);
    }

    /**
     * Statistik career center: sebaran lowongan aktif + ringkasan aktivitas siswa.
     */
    public function statistik(Request $request)
    {
        $user = $request->user();
        $aktif = Lowongan::where('is_active', true);

        $perTipe = (clone $aktif)->selectRaw('type, COUNT(*) as cnt')
            ->groupBy('type')->pluck('cnt', 'type');

        $perJurusan = (clone $aktif)->whereNotNull('jurusan')->where('jurusan', '!=', '')
            ->selectRaw('jurusan, COUNT(*) as cnt')
            ->groupBy('jurusan')->orderByDesc('cnt')->pluck('cnt', 'jurusan');

        $perLokasi = (clone $aktif)->whereNotNull('location')->where('location', '!=', '')
            ->selectRaw('location, COUNT(*) as cnt')
            ->groupBy('location')->orderByDesc('cnt')->limit(6)->pluck('cnt', 'location');

        $perArrangement = (clone $aktif)->whereNotNull('work_arrangement')
            ->selectRaw('work_arrangement, COUNT(*) as cnt')
            ->groupBy('work_arrangement')->pluck('cnt', 'work_arrangement');

        $saya = Lamaran::where('user_id', $user->id)
            ->selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')->pluck('cnt', 'status');

        // Tren 6 bulan terakhir; bulan tanpa lamaran tetap muncul dengan angka 0.
        // Dihitung di PHP (bukan DATE_FORMAT) supaya jalan juga di sqlite.
        $tren = [];
        for ($i = 5; $i >= 0; $i--) {
            $tren[now()->subMonths($i)->format('Y-m')] = 0;
        }
        $createdAts = Lamaran::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
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

        return response()->json([
            'lowongan' => [
                'total' => Lowongan::count(),
                'aktif' => Lowongan::where('is_active', true)->count(),
                'per_tipe' => $perTipe,
                'per_jurusan' => $perJurusan,
                'per_lokasi' => $perLokasi,
                'per_arrangement' => $perArrangement,
            ],
            'saya' => [
                'total' => (int) $saya->sum(),
                'pending' => (int) ($saya['pending'] ?? 0),
                'diterima' => (int) ($saya['diterima'] ?? 0),
                'ditolak' => (int) ($saya['ditolak'] ?? 0),
                'dibatalkan' => (int) ($saya['dibatalkan'] ?? 0),
                'bookmark' => Bookmark::where('user_id', $user->id)->count(),
            ],
            'tren' => $trenList,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'lowongan_id' => 'required|exists:lowongans,id',
            'cover_letter' => 'required|string|min:50|max:2000',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'nama_lengkap' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'nisn' => 'nullable|string|max:20',
            'jurusan_pilihan' => 'nullable|string|max:50',
        ]);

        $user = $request->user();

        $exists = Lamaran::where('user_id', $user->id)
            ->where('lowongan_id', $request->lowongan_id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Anda sudah melamar di lowongan ini.'], 422);
        }

        $cvPath = null;
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('cv', 'public');
        }

        $lamaran = Lamaran::create([
            'user_id' => $user->id,
            'lowongan_id' => $request->lowongan_id,
            'cv_path' => $cvPath,
            'cover_letter' => $request->cover_letter,
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'nisn' => $request->nisn,
            'jurusan_pilihan' => $request->jurusan_pilihan,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Lamaran berhasil dikirim!',
        ], 201);
    }

    public function myApplications(Request $request)
    {
        $lamarans = Lamaran::where('user_id', $request->user()->id)
            ->with('lowongan', 'statusHistories')
            ->latest()
            ->get();

        return response()->json($lamarans);
    }

    public function show(Request $request, Lamaran $lamaran)
    {
        if ($lamaran->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $lamaran->load(['lowongan', 'statusHistories.actionBy']);

        return response()->json($lamaran);
    }

    public function cancel(Request $request, Lamaran $lamaran)
    {
        if ($lamaran->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($lamaran->status !== 'pending') {
            return response()->json(['message' => 'Hanya lamaran dengan status pending yang bisa dibatalkan.'], 422);
        }

        $lamaran->update(['status' => 'dibatalkan']);

        return response()->json(['message' => 'Lamaran berhasil dibatalkan.']);
    }
}
