<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\JsonResponse;

class BeritaApiController extends Controller
{
    /**
     * GET /berita
     * List berita yang dipublikasikan. Fallback array kosong jika tabel belum ada.
     */
    public function index(): JsonResponse
    {
        try {
            $beritas = Berita::where('is_published', true)
                ->orderByDesc('published_at')
                ->orderByDesc('created_at')
                ->get()
                ->map(fn ($b) => $b->toApiArray());

            return response()->json($beritas)
                ->header('Cache-Control', 'public, max-age=60');
        } catch (\Exception $e) {
            // Tabel belum ada / belum migrasi → frontend fallback ke news.json
            return response()->json([]);
        }
    }

    /**
     * GET /berita/{slug}
     * Detail satu berita.
     */
    public function show(string $slug): JsonResponse
    {
        try {
            $berita = Berita::where('slug', $slug)
                ->where('is_published', true)
                ->firstOrFail();

            return response()->json($berita->toApiArray())
                ->header('Cache-Control', 'public, max-age=60');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json(['message' => 'Berita tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan.'], 500);
        }
    }
}
