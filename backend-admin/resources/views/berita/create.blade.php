@extends('layouts.app')

@section('title', 'Tambah Berita — Panel Admin')

@section('page-title', 'Tambah Berita')

@section('content')
<div class="main-content">

  {{-- Back link --}}
  <div style="margin-bottom:20px;">
    <a href="{{ route('berita.index') }}" style="display:inline-flex;align-items:center;gap:8px;color:#3a6450;font-weight:700;text-decoration:none;font-size:14px;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
      Kembali ke Daftar Berita
    </a>
  </div>

  <div class="form-section">
    <h1 class="form-title">Tambah Berita Baru</h1>
    <p class="form-subtitle">Isi informasi berita di bawah ini. Semua field bertanda * wajib diisi.</p>

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

    <form method="POST" action="{{ route('berita.store') }}" enctype="multipart/form-data" id="beritaForm">
      @csrf

      {{-- Judul --}}
      <div class="form-group">
        <label class="form-label">Judul Berita *</label>
        <input type="text" name="title" class="form-control" value="{{ old('title') }}"
          placeholder="Contoh: SMK Bahrul Ulum Juara 1 Lomba Robotik..."
          required maxlength="255" style="font-size:16px;font-weight:700;">
      </div>

      {{-- Kategori + Penulis (row) --}}
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
        <div class="form-group">
          <label class="form-label">Kategori *</label>
          <select name="category" class="form-control" required onchange="updateCategoryColor(this)">
            <option value="">-- Pilih Kategori --</option>
            @foreach($categories as $cat)
              <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Nama Penulis *</label>
          <input type="text" name="author" class="form-control" value="{{ old('author', 'Humas SMK Bahrul Ulum') }}"
            placeholder="Humas SMK Bahrul Ulum" required maxlength="100">
        </div>
      </div>

      {{-- Tanggal + Featured (row) --}}
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
        <div class="form-group">
          <label class="form-label">Tanggal Tayang *</label>
          <input type="date" name="published_at" class="form-control"
            value="{{ old('published_at', date('Y-m-d')) }}" required>
        </div>
        <div class="form-group" style="display:flex;flex-direction:column;justify-content:flex-end;">
          <div style="display:flex;align-items:center;gap:20px;padding-bottom:4px;">
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:14px;font-weight:600;color:#1c2a23;">
              <input type="checkbox" name="featured" value="1" {{ old('featured') ? 'checked' : '' }}
                style="width:18px;height:18px;accent-color:#f39c12;cursor:pointer;">
              ⭐ Jadikan Unggulan
            </label>
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:14px;font-weight:600;color:#1c2a23;">
              <input type="checkbox" name="is_published" value="1" checked
                style="width:18px;height:18px;accent-color:#3a6450;cursor:pointer;">
              🌐 Langsung Tayang
            </label>
          </div>
        </div>
      </div>

      {{-- Ringkasan --}}
      <div class="form-group">
        <label class="form-label">Ringkasan (Excerpt) *
          <span style="color:#8a9a8f;font-weight:500;font-size:12px;">— Ditampilkan di daftar berita, maks 500 karakter</span>
        </label>
        <textarea name="excerpt" class="form-control" rows="3" required maxlength="500"
          placeholder="Tulis ringkasan singkat yang menarik perhatian pembaca..."
          style="resize:vertical;">{{ old('excerpt') }}</textarea>
        <small style="color:#8a9a8f;font-size:12px;"><span id="excerptCount">0</span>/500</small>
      </div>

      {{-- Isi Berita --}}
      <div class="form-group">
        <label class="form-label">Isi Berita *</label>
        <textarea name="content" class="form-control" id="contentArea" rows="14" required
          placeholder="Tulis isi berita secara lengkap di sini. Anda bisa menggunakan Markdown atau teks biasa..."
          style="resize:vertical;font-family:monospace;font-size:14px;line-height:1.7;">{{ old('content') }}</textarea>
        <small style="color:#8a9a8f;font-size:12px;">Estimasi waktu baca dihitung otomatis dari panjang konten.</small>
      </div>

      {{-- Upload Gambar --}}
      <div class="form-group">
        <label class="form-label">Gambar Berita
          <span style="color:#8a9a8f;font-weight:500;font-size:12px;">— Opsional, maks 2MB, format JPG/PNG/WebP</span>
        </label>
        <div id="dropzone"
          style="border:2px dashed #c8d8cc;border-radius:14px;padding:32px;text-align:center;cursor:pointer;transition:all .2s;background:#fafdf9;"
          onclick="document.getElementById('imageInput').click()"
          ondragover="event.preventDefault();this.style.borderColor='#3a6450';this.style.background='#f0f7f2';"
          ondragleave="this.style.borderColor='#c8d8cc';this.style.background='#fafdf9';"
          ondrop="handleDrop(event)">
          <div id="dropzoneDefault">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#a8c4b0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 12px;display:block;"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            <p style="font-weight:700;color:#3a6450;margin:0 0 4px;">Klik atau seret gambar ke sini</p>
            <p style="font-size:12px;color:#8a9a8f;margin:0;">JPG, PNG, WebP — maks 2MB</p>
          </div>
          <div id="dropzonePreview" style="display:none;">
            <img id="previewImg" src="" alt="Preview" style="max-height:200px;max-width:100%;border-radius:10px;object-fit:contain;">
            <p id="previewName" style="font-size:12px;color:#3a6450;font-weight:700;margin:8px 0 0;"></p>
            <button type="button" onclick="clearImage(event)"
              style="margin-top:8px;padding:4px 12px;border-radius:8px;background:#fff0f0;color:#c0392b;border:none;font-size:12px;font-weight:700;cursor:pointer;">✕ Hapus gambar</button>
          </div>
        </div>
        <input type="file" name="image" id="imageInput" accept="image/jpeg,image/png,image/webp"
          style="display:none;" onchange="previewImage(this)">
      </div>

      {{-- Submit --}}
      <div style="display:flex;gap:12px;margin-top:8px;flex-wrap:wrap;">
        <button type="submit" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:8px;padding:14px 28px;font-size:15px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13"/><polyline points="7 3 7 8 15 8"/></svg>
          Simpan Berita
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

<script>
function previewImage(input) {
  if (input.files && input.files[0]) {
    const file = input.files[0];
    if (file.size > 2 * 1024 * 1024) {
      alert('Ukuran gambar terlalu besar. Maks 2MB.');
      input.value = '';
      return;
    }
    const reader = new FileReader();
    reader.onload = (e) => {
      document.getElementById('previewImg').src = e.target.result;
      document.getElementById('previewName').textContent = file.name;
      document.getElementById('dropzoneDefault').style.display = 'none';
      document.getElementById('dropzonePreview').style.display = 'block';
    };
    reader.readAsDataURL(file);
  }
}

function handleDrop(e) {
  e.preventDefault();
  const dt = e.dataTransfer;
  if (dt.files && dt.files[0]) {
    const input = document.getElementById('imageInput');
    // Transfer files ke input
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(dt.files[0]);
    input.files = dataTransfer.files;
    previewImage(input);
  }
  e.currentTarget.style.borderColor = '#c8d8cc';
  e.currentTarget.style.background = '#fafdf9';
}

function clearImage(e) {
  e.stopPropagation();
  document.getElementById('imageInput').value = '';
  document.getElementById('dropzoneDefault').style.display = 'block';
  document.getElementById('dropzonePreview').style.display = 'none';
}

// Kategori warna auto
const categoryColors = {
  'Pengumuman': '#3a6450',
  'Prestasi': '#e67e22',
  'Kerjasama': '#2980b9',
  'Kegiatan': '#8e44ad',
  'Acara': '#27ae60',
};
function updateCategoryColor(sel) {
  // Visual feedback (opsional)
  sel.style.borderLeftColor = categoryColors[sel.value] || '#d4ddd8';
  sel.style.borderLeftWidth = sel.value ? '4px' : '1.5px';
}

// Excerpt counter
const excerptTA = document.querySelector('textarea[name="excerpt"]');
const excerptCount = document.getElementById('excerptCount');
function updateCount() {
  excerptCount.textContent = excerptTA.value.length;
  excerptCount.style.color = excerptTA.value.length > 450 ? '#e74c3c' : '#8a9a8f';
}
if (excerptTA) {
  excerptTA.addEventListener('input', updateCount);
  updateCount();
}
</script>
@endsection
