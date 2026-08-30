@extends('layouts.app')

@section('title', 'Edit Berita — Panel Admin')

@section('page-title', 'Edit Berita')

@section('content')
<div class="main-content">

  <div style="margin-bottom:20px;">
    <a href="{{ route('berita.index') }}" style="display:inline-flex;align-items:center;gap:8px;color:#3a6450;font-weight:700;text-decoration:none;font-size:14px;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
      Kembali ke Daftar Berita
    </a>
  </div>

  <div class="form-section">
    <h1 class="form-title">Edit Berita</h1>
    <p class="form-subtitle">Perbarui informasi berita. Slug tidak berubah agar URL tetap konsisten.</p>

    @if($errors->any())
    <div class="alert alert-danger" style="margin-bottom:20px;padding:14px 16px;background:#fff0f0;border:1px solid #f5c6cb;border-radius:12px;color:#c0392b;">
      <strong>Ada kesalahan:</strong>
      <ul style="margin:6px 0 0 16px;">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('berita.update', $berita) }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="form-group">
        <label class="form-label">Judul Berita *</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $berita->title) }}" required maxlength="255" style="font-size:16px;font-weight:700;">
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
        <div class="form-group">
          <label class="form-label">Kategori *</label>
          <select name="category" class="form-control" required>
            @foreach($categories as $cat)
              <option value="{{ $cat }}" {{ old('category', $berita->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Nama Penulis *</label>
          <input type="text" name="author" class="form-control" value="{{ old('author', $berita->author) }}" required maxlength="100">
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
        <div class="form-group">
          <label class="form-label">Tanggal Tayang *</label>
          <input type="date" name="published_at" class="form-control" value="{{ old('published_at', $berita->published_at?->format('Y-m-d')) }}" required>
        </div>
        <div class="form-group" style="display:flex;flex-direction:column;justify-content:flex-end;">
          <div style="display:flex;align-items:center;gap:20px;padding-bottom:4px;">
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:14px;font-weight:600;color:#1c2a23;">
              <input type="checkbox" name="featured" value="1" {{ old('featured', $berita->featured) ? 'checked' : '' }} style="width:18px;height:18px;accent-color:#f39c12;">
              ⭐ Unggulan
            </label>
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:14px;font-weight:600;color:#1c2a23;">
              <input type="checkbox" name="is_published" value="1" {{ old('is_published', $berita->is_published) ? 'checked' : '' }} style="width:18px;height:18px;accent-color:#3a6450;">
              🌐 Tayang
            </label>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Ringkasan (Excerpt) *</label>
        <textarea name="excerpt" class="form-control" rows="3" required maxlength="500" style="resize:vertical;">{{ old('excerpt', $berita->excerpt) }}</textarea>
      </div>

      <div class="form-group">
        <label class="form-label">Isi Berita *</label>
        <textarea name="content" class="form-control" rows="14" required style="resize:vertical;font-family:monospace;font-size:14px;line-height:1.7;">{{ old('content', $berita->content) }}</textarea>
      </div>

      {{-- Gambar --}}
      <div class="form-group">
        <label class="form-label">Gambar Berita</label>
        @if($berita->image_path)
        <div style="margin-bottom:12px;display:flex;align-items:center;gap:12px;">
          <img src="{{ '/storage/' . $berita->image_path }}" alt="Gambar saat ini"
            style="height:80px;width:120px;object-fit:cover;border-radius:10px;">
          <span style="font-size:13px;color:#647067;">Gambar saat ini. Upload baru untuk mengganti.</span>
        </div>
        @endif
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
          style="display:block;padding:10px;border:1.5px dashed #c8d8cc;border-radius:12px;width:100%;background:#fafdf9;cursor:pointer;">
        <small style="color:#8a9a8f;font-size:12px;">Opsional. Biarkan kosong jika tidak ingin mengubah gambar. Maks 2MB.</small>
      </div>

      <div style="display:flex;gap:12px;margin-top:8px;flex-wrap:wrap;">
        <button type="submit" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:8px;padding:14px 28px;font-size:15px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13"/><polyline points="7 3 7 8 15 8"/></svg>
          Simpan Perubahan
        </button>
        <a href="{{ route('berita.index') }}" class="btn"
          style="display:inline-flex;align-items:center;gap:8px;padding:14px 24px;font-size:15px;background:#f0f3f0;color:#3a6450;text-decoration:none;border-radius:12px;font-weight:700;">
          Batal
        </a>
      </div>
    </form>
  </div>
</div>

<style>
.form-group { margin-bottom: 22px; }
.form-label { display:block;margin-bottom:7px;font-weight:700;font-size:13px;color:#1c2a23;letter-spacing:.02em; }
.form-control {
  width: 100%;
  padding: 12px 14px;
  border: 1.5px solid #d4ddd8;
  border-radius: 12px;
  font-family: inherit;
  font-size: 14px;
  color: #1c2a23;
  background: #fff;
  transition: border-color .2s, box-shadow .2s;
  box-sizing: border-box;
}
.form-control:focus {
  outline: none;
  border-color: #3a6450;
  box-shadow: 0 0 0 3px rgba(58,100,80,.1);
}
@media (max-width: 680px) {
  div[style*="grid-template-columns:1fr 1fr"] {
    grid-template-columns: 1fr !important;
  }
}
</style>
@endsection
