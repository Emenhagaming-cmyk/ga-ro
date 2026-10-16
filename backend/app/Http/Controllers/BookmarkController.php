<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Lowongan;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function index(Request $request)
    {
        $bookmarks = Bookmark::where('user_id', $request->user()->id)
            ->with('lowongan')
            ->latest()
            ->get();

        return response()->json($bookmarks);
    }

    public function store(Request $request)
    {
        $request->validate([
            'lowongan_id' => 'required|exists:lowongans,id',
        ]);

        $user = $request->user();

        $exists = Bookmark::where('user_id', $user->id)
            ->where('lowongan_id', $request->lowongan_id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Lowongan sudah disimpan.'], 422);
        }

        Bookmark::create([
            'user_id' => $user->id,
            'lowongan_id' => $request->lowongan_id,
        ]);

        return response()->json([
            'message' => 'Lowongan berhasil disimpan.',
        ], 201);
    }

    public function destroy(Request $request, Lowongan $lowongan)
    {
        $user = $request->user();

        $deleted = Bookmark::where('user_id', $user->id)
            ->where('lowongan_id', $lowongan->id)
            ->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Bookmark tidak ditemukan.'], 404);
        }

        return response()->json(['message' => 'Bookmark dihapus.']);
    }
}
