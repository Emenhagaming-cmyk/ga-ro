@extends('layouts.app')

@section('title', 'Kelola Berita — Panel Admin')

@section('page-title', 'Kelola Berita')

@section('content')
<div class="main-content">

  {{-- Flash messages --}}
  @if(session('success'))
  <div class="alert alert-success" style="margin-bottom:20px;">✓ {{ session('success') }}</div>
  @endif

  {{-- Header --}}
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
    <div>
      <h1 class="form-title" style="margin:0;">Berita & Pengumuman</h1>
      <p class="form-subtitle" style="margin:6px 0 0;">Kelola artikel berita yang tampil di website sekolah.</p>
    </div>
    <a href="{{ route('berita.create') }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:8px;white-space:nowrap;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Tambah Berita
    </a>
  </div>

  {{-- Stats bar --}}
  <div style="display:flex;gap:16px;margin-bottom:28px;flex-wrap:wrap;">
    <div class="stat-card mini" style="background:#fff;border:1px solid #dfe4dd;border-radius:14px;padding:14px 20px;display:flex;align-items:center;gap:12px;">
      <span style="font-size:22px;font-weight:800;color:#1c2a23;">{{ $beritas->total() }}</span>
      <span style="font-size:13px;color:#647067;">Total Berita</span>
    </div>
    <div class="stat-card mini" style="background:#fff;border:1px solid #dfe4dd;border-radius:14px;padding:14px 20px;display:flex;align-items:center;gap:12px;">
      <span style="font-size:22px;font-weight:800;color:#3a6450;">{{ $beritas->where('is_published', true)->count() }}</span>
      <span style="font-size:13px;color:#647067;">Dipublikasikan</span>
    </div>
    <div class="stat-card mini" style="background:#fff;border:1px solid #dfe4dd;border-radius:14px;padding:14px 20px;display:flex;align-items:center;gap:12px;">
      <span style="font-size:22px;font-weight:800;color:#f39c12;">{{ $beritas->where('featured', true)->count() }}</span>
      <span style="font-size:13px;color:#647067;">Unggulan</span>
    </div>
  </div>

  {{-- Tabel --}}
  <div class="form-section" style="padding:0;overflow:hidden;">
    <div class="table-wrap" style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
      <table style="width:100%;border-collapse:collapse;min-width:700px;">
        <thead>
          <tr style="background:#f6f9f6;border-bottom:1px solid #dfe4dd;">
            <th style="padding:14px 20px;text-align:left;font-size:12px;font-weight:700;color:#647067;text-transform:uppercase;letter-spacing:.06em;white-space:nowrap;">Judul</th>
            <th style="padding:14px 16px;text-align:left;font-size:12px;font-weight:700;color:#647067;text-transform:uppercase;letter-spacing:.06em;white-space:nowrap;">Kategori</th>
            <th style="padding:14px 16px;text-align:left;font-size:12px;font-weight:700;color:#647067;text-transform:uppercase;letter-spacing:.06em;white-space:nowrap;">Tanggal</th>
            <th style="padding:14px 16px;text-align:left;font-size:12px;font-weight:700;color:#647067;text-transform:uppercase;letter-spacing:.06em;white-space:nowrap;">Status</th>
            <th style="padding:14px 16px;text-align:right;font-size:12px;font-weight:700;color:#647067;text-transform:uppercase;letter-spacing:.06em;white-space:nowrap;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($beritas as $item)
          <tr style="border-bottom:1px solid #f0f3f0;">
            {{-- Judul + thumbnail --}}
            <td style="padding:14px 20px;">
              <div style="display:flex;align-items:center;gap:12px;">
                @if($item->image_path)
                  <img src="{{ '/storage/' . $item->image_path }}" alt="{{ $item->title }}"
                    style="width:48px;height:36px;object-fit:cover;border-radius:8px;flex-shrink:0;background:#e8f0e6;">
                @else
                  <div style="width:48px;height:36px;border-radius:8px;background:linear-gradient(135deg,#2f5b45,#7db88d);flex-shrink:0;display:flex;align-items:center;justify-content:center;">
                    <svg width="16" height="16" fill="none" stroke="rgba(255,255,255,.7)" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                  </div>
                @endif
                <div style="min-width:0;">
                  <div style="font-weight:700;font-size:14px;color:#1c2a23;line-height:1.3;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden;">
                    {{ $item->title }}
                    @if($item->featured)
                    <span style="display:inline-block;margin-left:6px;padding:1px 6px;border-radius:6px;background:#fef3e0;color:#f39c12;font-size:10px;font-weight:700;vertical-align:middle;">★ Unggulan</span>
                    @endif
                  </div>
                  <div style="font-size:11px;color:#8a9a8f;margin-top:2px;">{{ Str::limit($item->excerpt, 60) }}</div>
                </div>
              </div>
            </td>
            {{-- Kategori --}}
            <td style="padding:14px 16px;white-space:nowrap;">
              <span style="display:inline-block;padding:4px 10px;border-radius:8px;font-size:12px;font-weight:700;color:#fff;background:{{ $item->category_color }};">
                {{ $item->category }}
              </span>
            </td>
            {{-- Tanggal --}}
            <td style="padding:14px 16px;font-size:13px;color:#647067;white-space:nowrap;">
              {{ $item->published_at?->translatedFormat('d M Y') ?? '-' }}
            </td>
            {{-- Status --}}
            <td style="padding:14px 16px;white-space:nowrap;">
              @if($item->is_published)
                <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:8px;background:#e8f5e8;color:#2a7d2e;font-size:12px;font-weight:700;">
                  <span style="width:6px;height:6px;border-radius:50%;background:#2a7d2e;flex-shrink:0;"></span> Tayang
                </span>
              @else
                <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:8px;background:#f5f5f5;color:#888;font-size:12px;font-weight:700;">
                  <span style="width:6px;height:6px;border-radius:50%;background:#bbb;flex-shrink:0;"></span> Draft
                </span>
              @endif
            </td>
            {{-- Aksi --}}
            <td style="padding:14px 16px;text-align:right;white-space:nowrap;">
              <a href="{{ route('berita.edit', $item) }}" class="btn btn-sm"
                style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:8px;background:#e8f0e6;color:#2a5238;border:none;font-size:12px;font-weight:700;text-decoration:none;cursor:pointer;margin-right:6px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit
              </a>
              <form method="POST" action="{{ route('berita.destroy', $item) }}" style="display:inline;"
                onsubmit="return confirm('Hapus berita ini? Tindakan tidak bisa dibatalkan.');">
                @csrf
                @method('DELETE')
                <button type="submit" style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:8px;background:#fff0f0;color:#c0392b;border:none;font-size:12px;font-weight:700;cursor:pointer;">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                  Hapus
                </button>
              </form>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" style="padding:60px;text-align:center;color:#8a9a8f;">
              <div style="font-size:40px;margin-bottom:12px;">📰</div>
              <div style="font-weight:700;">Belum ada berita</div>
              <div style="font-size:13px;margin-top:4px;">Klik "Tambah Berita" untuk mulai.</div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    @if($beritas->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #f0f3f0;">
      {{ $beritas->links() }}
    </div>
    @endif
  </div>

</div>
@endsection
