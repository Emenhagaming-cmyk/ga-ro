@extends('layouts.app')

@section('title', ($product->exists ? 'Edit' : 'Tambah') . ' Produk Koperasi - Panel Admin')
@section('page-title', ($product->exists ? 'Edit' : 'Tambah') . ' Produk')

@section('content')
<div class="kop-page">
    @include('koperasi._kop-style')

    {{-- Header --}}
    <div class="kop-header">
        <div>
            <p class="kop-kicker">Koperasi Sekolah</p>
            <h1 class="kop-title">{{ $product->exists ? 'Edit Produk' : 'Tambah Produk' }}</h1>
            <p class="kop-subtitle">Produk langsung tampil di katalog koperasi siswa.</p>
        </div>
        <div class="kop-header-actions">
            <a href="{{ route('koperasi.products') }}" class="kop-btn kop-btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Kembali ke Daftar
            </a>
        </div>
    </div>

    @include('koperasi._tabs', ['active' => 'products'])

    @if ($errors->any())
        <div class="alert alert-error">
            <strong>Periksa kembali isian berikut:</strong>
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ $product->exists ? route('koperasi.product.update', $product->id) : route('koperasi.product.store') }}"
          method="POST" enctype="multipart/form-data">
        @csrf
        @if ($product->exists)
            @method('PUT')
        @endif

        <div class="kop-form-layout">
            {{-- ===== Kolom utama: informasi produk ===== --}}
            <div class="kop-panel">
                <div class="kop-panel-head">
                    <div>
                        <h2 class="kop-panel-title">Informasi Produk</h2>
                        <p class="kop-panel-sub">Data dasar yang tampil di katalog siswa</p>
                    </div>
                </div>
                <div class="kop-panel-body">
                    <div class="kop-field">
                        <label class="kop-label" for="prodName">Nama Produk</label>
                        <input id="prodName" type="text" name="name" value="{{ old('name', $product->name) }}"
                               class="kop-input {{ $errors->has('name') ? 'has-error' : '' }}" required
                               placeholder="cth: Kaos Olahraga Ukuran M">
                        @error('name')<span class="kop-field-error">{{ $message }}</span>@enderror
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div class="kop-field">
                            <label class="kop-label" for="prodCategory">Kategori</label>
                            <input id="prodCategory" type="text" name="category"
                                   value="{{ old('category', $product->category ?: 'Alat Tulis') }}"
                                   class="kop-input {{ $errors->has('category') ? 'has-error' : '' }}"
                                   list="kategoriList" required>
                            <datalist id="kategoriList">
                                <option value="Seragam"></option>
                                <option value="Alat Tulis"></option>
                                <option value="Aksesoris"></option>
                                <option value="Minuman"></option>
                                <option value="Makanan"></option>
                            </datalist>
                            @error('category')<span class="kop-field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="kop-field">
                            <label class="kop-label" for="prodEmoji">Emoji <span class="opt">(opsional)</span></label>
                            <input id="prodEmoji" type="text" name="emoji" value="{{ old('emoji', $product->emoji) }}"
                                   class="kop-input" placeholder="👕" maxlength="8"
                                   oninput="document.getElementById('emojiPrev').textContent = this.value || '📦'">
                            <span class="kop-emoji-preview" id="emojiPrev">{{ old('emoji', $product->emoji) ?: '📦' }}</span>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div class="kop-field">
                            <label class="kop-label" for="prodPrice">Harga</label>
                            <div class="kop-affix">
                                <span>Rp</span>
                                <input id="prodPrice" type="number" name="price" value="{{ old('price', $product->price) }}"
                                       min="0" required placeholder="0">
                            </div>
                            @error('price')<span class="kop-field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="kop-field">
                            <label class="kop-label" for="prodStock">Stok</label>
                            <input id="prodStock" type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}"
                                   class="kop-input {{ $errors->has('stock') ? 'has-error' : '' }}" min="0" required>
                            @error('stock')<span class="kop-field-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="kop-field">
                        <label class="kop-label" for="prodDesc">Deskripsi <span class="opt">(opsional)</span></label>
                        <textarea id="prodDesc" name="description" rows="4"
                                  class="kop-textarea {{ $errors->has('description') ? 'has-error' : '' }}"
                                  placeholder="Deskripsi singkat produk…">{{ old('description', $product->description) }}</textarea>
                        @error('description')<span class="kop-field-error">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            {{-- ===== Sidebar: media & publikasi ===== --}}
            <div class="kop-form-side">
                <div class="kop-panel">
                    <div class="kop-panel-head">
                        <h2 class="kop-panel-title">Gambar</h2>
                    </div>
                    <div class="kop-panel-body">
                        <div class="kop-field">
                            <label class="kop-label" for="prodImage">Unggah Gambar <span class="opt">(JPG/PNG/WebP)</span></label>
                            <input id="prodImage" type="file" name="image" accept="image/jpeg,image/png,image/webp" class="kop-input">
                            @if ($product->image)
                                @php $url = str_starts_with($product->image, 'http') || str_starts_with($product->image, '/') ? $product->image : asset('storage/' . $product->image); @endphp
                                <img src="{{ $url }}" alt="Gambar saat ini" class="kop-upload-preview">
                            @endif
                        </div>
                    </div>
                </div>

                <div class="kop-panel">
                    <div class="kop-panel-head">
                        <h2 class="kop-panel-title">Publikasi</h2>
                    </div>
                    <div class="kop-panel-body">
                        <div class="kop-field">
                            <label class="kop-check">
                                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))>
                                <span>Tampilkan di katalog siswa.<br>
                                    <small style="color:var(--kop-muted);font-weight:600;">Nonaktif = hanya terlihat oleh admin.</small>
                                </span>
                            </label>
                        </div>
                        <div class="kop-form-actions" style="flex-direction:column;">
                            <button type="submit" class="kop-btn kop-btn-primary">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                {{ $product->exists ? 'Simpan Perubahan' : 'Tambah Produk' }}
                            </button>
                            <a href="{{ route('koperasi.products') }}" class="kop-btn kop-btn-ghost">Batal</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection