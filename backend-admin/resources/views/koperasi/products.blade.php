@extends('layouts.app')

@section('title', 'Produk Koperasi - Panel Admin')
@section('page-title', 'Produk Koperasi')

@section('content')
<div class="dashboard-shell">
    <div class="dashboard-header">
        <div>
            <p class="dashboard-kicker">Koperasi Sekolah</p>
            <h1 class="form-title" fetchpriority="high">Produk Koperasi</h1>
            <p class="form-subtitle">Kelola katalog, harga, dan stok barang koperasi.</p>
        </div>
        <a href="{{ route('koperasi.product.create') }}" class="btn btn-primary" style="text-decoration:none;">+ Tambah Produk</a>
    </div>

    @include('koperasi._tabs', ['active' => 'products'])

    @if (session('success'))
        <div class="alert alert-success">✓ {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="form-section" style="padding:0;overflow:hidden;">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th class="hide-sm">Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        @php
                            $img = $product->image;
                            $url = $img ? (str_starts_with($img, 'http') || str_starts_with($img, '/') ? $img : asset('storage/' . $img)) : null;
                        @endphp
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:12px;">
                                    <div style="width:44px;height:44px;border-radius:10px;overflow:hidden;background:#eef2ef;display:grid;place-items:center;flex-shrink:0;font-size:22px;">
                                        @if ($url)
                                            <img src="{{ $url }}" alt="" style="width:100%;height:100%;object-fit:cover;">
                                        @else
                                            {{ $product->emoji ?: '📦' }}
                                        @endif
                                    </div>
                                    <div>
                                        <strong>{{ $product->name }}</strong>
                                        @if ($product->description)
                                            <div style="font-size:12px;color:#8a9890;">{{ \Illuminate\Support\Str::limit($product->description, 50) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="hide-sm">{{ $product->category }}</td>
                            <td><strong>Rp {{ number_format($product->price, 0, ',', '.') }}</strong></td>
                            <td>
                                <span class="badge {{ $product->stock > 0 ? 'badge-neutral' : 'badge-danger' }}">{{ $product->stock }}</span>
                            </td>
                            <td><span class="badge {{ $product->is_active ? 'badge-success' : 'badge-neutral' }}">{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('koperasi.product.edit', $product->id) }}" class="action-btn action-btn-edit">Edit</a>
                                    <form action="{{ route('koperasi.product.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Hapus produk {{ $product->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn action-btn-delete">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state" style="padding:60px 20px;">
                                <div style="font-size:40px;margin-bottom:12px;">📦</div>
                                <div style="font-weight:700;">Belum ada produk</div>
                                <div style="margin-top:12px;"><a href="{{ route('koperasi.product.create') }}" class="btn btn-primary" style="text-decoration:none;">+ Tambah Produk</a></div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($products->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #f0f3f0;">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
