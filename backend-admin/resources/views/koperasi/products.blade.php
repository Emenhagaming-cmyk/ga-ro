@extends('layouts.app')

@section('title', 'Produk Koperasi - Panel Admin')
@section('page-title', 'Produk Koperasi')

@section('content')
<div class="kop-page">
    @include('koperasi._kop-style')

    {{-- Header --}}
    <div class="kop-header">
        <div>
            <p class="kop-kicker">Koperasi Sekolah</p>
            <h1 class="kop-title">Produk Koperasi</h1>
            <p class="kop-subtitle">Kelola katalog, harga, dan stok barang koperasi.</p>
        </div>
        <div class="kop-header-actions">
            <a href="{{ route('koperasi.product.create') }}" class="kop-btn kop-btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Produk
            </a>
        </div>
    </div>

    @include('koperasi._tabs', ['active' => 'products'])

    @if (session('success'))
        <div class="alert alert-success">✓ {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    {{-- Tabel produk --}}
    <div class="kop-panel">
        <div class="kop-panel-head">
            <div>
                <h2 class="kop-panel-title">Daftar Produk</h2>
                <p class="kop-panel-sub">
                    Menampilkan {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} produk
                </p>
            </div>
        </div>

        <div class="kop-table-wrap">
            <table class="kop-table">
                <thead>
                    <tr>
                        <th scope="col">Produk</th>
                        <th scope="col" class="kop-hide-sm">Kategori</th>
                        <th scope="col" class="kop-nowrap">Harga</th>
                        <th scope="col" class="kop-nowrap">Stok</th>
                        <th scope="col" class="kop-nowrap">Status</th>
                        <th scope="col" class="kop-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        @php
                            $img = $product->image;
                            $url = $img ? (str_starts_with($img, 'http') || str_starts_with($img, '/') ? $img : asset('storage/' . $img)) : null;
                            $stockTone = $product->stock <= 0 ? 'expired' : ($product->stock <= 5 ? 'pending' : 'batal');
                            $stockTitle = $product->stock <= 0 ? 'Stok habis' : ($product->stock <= 5 ? 'Stok menipis' : 'Stok tersedia');
                        @endphp
                        <tr>
                            <td style="min-width:220px;">
                                <div class="kop-user">
                                    <div class="kop-thumb">
                                        @if ($url)
                                            <img src="{{ $url }}" alt="">
                                        @else
                                            {{ $product->emoji ?: '📦' }}
                                        @endif
                                    </div>
                                    <div class="kop-user-text">
                                        <span class="kop-user-name">{{ $product->name }}</span>
                                        @if ($product->description)
                                            <span class="kop-user-sub">{{ \Illuminate\Support\Str::limit($product->description, 50) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="kop-hide-sm"><span class="kop-cat">{{ $product->category }}</span></td>
                            <td><strong class="kop-total">Rp {{ number_format($product->price, 0, ',', '.') }}</strong></td>
                            <td>
                                <span class="kop-badge kop-badge--{{ $stockTone }}" title="{{ $stockTitle }}"><i></i>{{ $product->stock }}</span>
                            </td>
                            <td>
                                <span class="kop-badge {{ $product->is_active ? 'kop-badge--diambil' : 'kop-badge--batal' }}">
                                    <i></i>{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="kop-act">
                                    <a href="{{ route('koperasi.product.edit', $product->id) }}" class="kop-act-btn kop-act--edit">Edit</a>
                                    <form action="{{ route('koperasi.product.destroy', $product->id) }}" method="POST"
                                          onsubmit="return confirm('Hapus produk {{ $product->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="kop-act-btn kop-act--danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="kop-empty">
                                    <div class="kop-empty-icon">
                                        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                                    </div>
                                    <h3>Belum ada produk</h3>
                                    <p>Tambahkan produk pertama agar bisa dibeli siswa di katalog.</p>
                                    <a href="{{ route('koperasi.product.create') }}" class="kop-btn kop-btn-primary">Tambah Produk</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($products->hasPages())
            <div class="kop-panel-foot">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection