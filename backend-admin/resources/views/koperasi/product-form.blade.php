@extends('layouts.app')

@section('title', ($product->exists ? 'Edit' : 'Tambah') . ' Produk Koperasi - Panel Admin')
@section('page-title', ($product->exists ? 'Edit' : 'Tambah') . ' Produk')

@section('content')
<div class="dashboard-shell">
    <div class="dashboard-header">
        <div>
            <p class="dashboard-kicker">Koperasi Sekolah</p>
            <h1 class="form-title" fetchpriority="high">{{ $product->exists ? 'Edit Produk' : 'Tambah Produk' }}</h1>
            <p class="form-subtitle">Produk langsung tampil di katalog koperasi siswa.</p>
        </div>
    </div>

    @include('koperasi._tabs', ['active' => 'products'])

    @if ($errors->any())
        <div class="alert alert-error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="form-section">
        <form action="{{ $product->exists ? route('koperasi.product.update', $product->id) : route('koperasi.product.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if ($product->exists)
                @method('PUT')
            @endif

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                <div style="grid-column:1 / -1;">
                    <label style="display:block;font-size:13px;font-weight:700;margin-bottom:6px;">Nama Produk</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" class="filter-input" style="width:100%;" required>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:700;margin-bottom:6px;">Kategori</label>
                    <input type="text" name="category" value="{{ old('category', $product->category ?: 'Alat Tulis') }}" class="filter-input" style="width:100%;" list="kategoriList" required>
                    <datalist id="kategoriList">
                        <option value="Seragam"></option>
                        <option value="Alat Tulis"></option>
                        <option value="Aksesoris"></option>
                        <option value="Minuman"></option>
                        <option value="Makanan"></option>
                    </datalist>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:700;margin-bottom:6px;">Emoji (opsional)</label>
                    <input type="text" name="emoji" value="{{ old('emoji', $product->emoji) }}" class="filter-input" style="width:100%;" placeholder="👕" maxlength="8">
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:700;margin-bottom:6px;">Harga (Rp)</label>
                    <input type="number" name="price" value="{{ old('price', $product->price) }}" class="filter-input" style="width:100%;" min="0" required>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:700;margin-bottom:6px;">Stok</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" class="filter-input" style="width:100%;" min="0" required>
                </div>

                <div style="grid-column:1 / -1;">
                    <label style="display:block;font-size:13px;font-weight:700;margin-bottom:6px;">Deskripsi</label>
                    <textarea name="description" class="filter-input" style="width:100%;min-height:100px;" rows="4">{{ old('description', $product->description) }}</textarea>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:700;margin-bottom:6px;">Gambar (opsional)</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="filter-input" style="width:100%;">
                    @if ($product->image)
                        @php $url = str_starts_with($product->image, 'http') || str_starts_with($product->image, '/') ? $product->image : asset('storage/' . $product->image); @endphp
                        <img src="{{ $url }}" alt="" style="margin-top:10px;width:120px;height:120px;object-fit:cover;border-radius:12px;">
                    @endif
                </div>

                <div style="display:flex;align-items:center;gap:10px;">
                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:700;">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))>
                        Tampilkan di katalog
                    </label>
                </div>
            </div>

            <div style="margin-top:24px;display:flex;gap:12px;">
                <button type="submit" class="btn btn-primary">{{ $product->exists ? 'Simpan Perubahan' : 'Tambah Produk' }}</button>
                <a href="{{ route('koperasi.products') }}" class="btn btn-secondary" style="text-decoration:none;">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
