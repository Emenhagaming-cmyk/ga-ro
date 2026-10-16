<?php

namespace App\Http\Controllers;

use App\Models\Lamaran;
use App\Models\LamaranStatusHistory;
use App\Models\Lowongan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LamaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Lamaran::with('lowongan', 'user');

        if ($request->filled('lowongan')) {
            $query->where('lowongan_id', $request->lowongan);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($w) use ($q) {
                $w->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('nisn', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%"));
            });
        }

        $lamarans = $query->latest()->paginate(20)->withQueryString();

        $lowongans = Lowongan::orderBy('title')->get(['id', 'title', 'company']);

        return view('lamaran.index', compact('lamarans', 'lowongans'));
    }

    public function show(Lamaran $lamaran)
    {
        $lamaran->load(['lowongan', 'user', 'statusHistories.actionBy']);

        return view('lamaran.show', compact('lamaran'));
    }

    public function updateStatus(Request $request, Lamaran $lamaran)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,diterima,ditolak,dibatalkan',
            'catatan_admin' => 'nullable|string|max:1000',
        ]);

        $from = $lamaran->status;

        $lamaran->update([
            'status' => $validated['status'],
            'catatan_admin' => $validated['catatan_admin'] ?? $lamaran->catatan_admin,
        ]);

        if ($from !== $validated['status']) {
            LamaranStatusHistory::create([
                'lamaran_id' => $lamaran->id,
                'status_from' => $from,
                'status_to' => $validated['status'],
                'action_by' => $request->user()->id,
                'catatan' => $validated['catatan_admin'] ?? null,
            ]);
        }

        return back()->with('success', 'Status lamaran diperbarui.');
    }

    public function downloadCv(Lamaran $lamaran)
    {
        if (! $lamaran->cv_path || ! Storage::disk('public')->exists($lamaran->cv_path)) {
            return back()->with('error', 'Berkas CV tidak ditemukan (belum diunggah atau hilang).');
        }

        return Storage::disk('public')->download($lamaran->cv_path);
    }
}
