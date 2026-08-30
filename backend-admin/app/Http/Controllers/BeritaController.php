<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BeritaController extends Controller
{
    // Warna per kategori
    private const CATEGORY_COLORS = [
        'Pengumuman' => '#3a6450',
        'Prestasi'   => '#e67e22',
        'Kerjasama'  => '#2980b9',
        'Kegiatan'   => '#8e44ad',
        'Acara'      => '#27ae60',
    ];

    public function index()
    {
        $beritas = Berita::orderByDesc('published_at')->orderByDesc('created_at')->paginate(20);
        return view('berita.index', compact('beritas'));
    }

    public function create()
    {
        $categories = array_keys(self::CATEGORY_COLORS);
        return view('berita.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'category'     => 'required|string|in:Pengumuman,Prestasi,Kerjasama,Kegiatan,Acara',
            'excerpt'      => 'required|string|max:500',
            'content'      => 'required|string',
            'author'       => 'required|string|max:100',
            'published_at' => 'required|date',
            'featured'     => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
            'image'        => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imagePath = $request->file('image')->store('berita', 'public');
        }

        // Estimasi waktu baca: ~200 kata/menit
        $wordCount = str_word_count(strip_tags($validated['content']));
        $minutes   = max(1, (int) round($wordCount / 200));
        $readTime  = $minutes . ' menit';

        Berita::create([
            'title'          => $validated['title'],
            'slug'           => Berita::generateSlug($validated['title']),
            'category'       => $validated['category'],
            'category_color' => self::CATEGORY_COLORS[$validated['category']] ?? '#3a6450',
            'excerpt'        => $validated['excerpt'],
            'content'        => $validated['content'],
            'image_path'     => $imagePath,
            'author'         => $validated['author'],
            'published_at'   => $validated['published_at'],
            'featured'       => $request->boolean('featured'),
            'read_time'      => $readTime,
            'is_published'   => $request->boolean('is_published', true),
            'user_id'        => auth()->id(),
        ]);

        return redirect()->route('berita.index')->with('success', 'Berita berhasil ditambahkan!');
    }

    public function edit(Berita $berita)
    {
        $categories = array_keys(self::CATEGORY_COLORS);
        return view('berita.edit', compact('berita', 'categories'));
    }

    public function update(Request $request, Berita $berita)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'category'     => 'required|string|in:Pengumuman,Prestasi,Kerjasama,Kegiatan,Acara',
            'excerpt'      => 'required|string|max:500',
            'content'      => 'required|string',
            'author'       => 'required|string|max:100',
            'published_at' => 'required|date',
            'featured'     => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
            'image'        => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Hapus gambar lama
            if ($berita->image_path && !str_starts_with($berita->image_path, 'http')) {
                Storage::disk('public')->delete($berita->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('berita', 'public');
        }

        $wordCount = str_word_count(strip_tags($validated['content']));
        $minutes   = max(1, (int) round($wordCount / 200));

        $berita->update([
            'title'          => $validated['title'],
            'category'       => $validated['category'],
            'category_color' => self::CATEGORY_COLORS[$validated['category']] ?? '#3a6450',
            'excerpt'        => $validated['excerpt'],
            'content'        => $validated['content'],
            'image_path'     => $validated['image_path'] ?? $berita->image_path,
            'author'         => $validated['author'],
            'published_at'   => $validated['published_at'],
            'featured'       => $request->boolean('featured'),
            'read_time'      => $minutes . ' menit',
            'is_published'   => $request->boolean('is_published', true),
        ]);

        return redirect()->route('berita.index')->with('success', 'Berita berhasil diperbarui!');
    }

    public function destroy(Berita $berita)
    {
        if ($berita->image_path && !str_starts_with($berita->image_path, 'http')) {
            Storage::disk('public')->delete($berita->image_path);
        }
        $berita->delete();
        return redirect()->route('berita.index')->with('success', 'Berita berhasil dihapus.');
    }
}
