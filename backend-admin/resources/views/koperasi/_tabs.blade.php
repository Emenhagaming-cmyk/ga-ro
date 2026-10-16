<div class="koperasi-tabs" style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
    <a href="{{ route('koperasi.index') }}" class="btn {{ $active === 'orders' ? 'btn-primary' : 'btn-secondary' }}" style="text-decoration:none;">Pesanan</a>
    <a href="{{ route('koperasi.products') }}" class="btn {{ $active === 'products' ? 'btn-primary' : 'btn-secondary' }}" style="text-decoration:none;">Produk</a>
    <a href="{{ route('koperasi.laporan') }}" class="btn {{ $active === 'laporan' ? 'btn-primary' : 'btn-secondary' }}" style="text-decoration:none;">Laporan</a>
</div>
