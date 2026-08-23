<?php

namespace App\Http\Controllers;

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LamaranController extends Controller
{
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
            ->with('lowongan')
            ->latest()
            ->get();

        return response()->json($lamarans);
    }

    public function show(Request $request, Lamaran $lamaran)
    {
        if ($lamaran->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $lamaran->load('lowongan');

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
