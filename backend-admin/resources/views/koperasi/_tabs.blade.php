<div class="kop-subnav">
    <a href="{{ route('koperasi.index') }}" class="{{ $active === 'orders' ? 'active' : '' }}">Pesanan</a>
    <a href="{{ route('koperasi.products') }}" class="{{ $active === 'products' ? 'active' : '' }}">Produk</a>
    <a href="{{ route('koperasi.laporan') }}" class="{{ $active === 'laporan' ? 'active' : '' }}">Laporan</a>
</div>
