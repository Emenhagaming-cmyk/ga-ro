@extends('layouts.app')

@section('title', 'Laporan Koperasi - Panel Admin')
@section('page-title', 'Laporan Koperasi')

@section('content')
<div class="dashboard-shell">
    <div class="dashboard-header">
        <div>
            <p class="dashboard-kicker">Koperasi Sekolah</p>
            <h1 class="form-title" fetchpriority="high">Laporan Koperasi</h1>
            <p class="form-subtitle">Rekap omzet, produk terlaris, dan penjualan harian.</p>
        </div>
    </div>

    @include('koperasi._tabs', ['active' => 'laporan'])

    <div class="stats-grid">
        <div class="stat-card stat-card-success">
            <div class="stat-label">Total Omzet</div>
            <div class="stat-value">{{ number_format($omzet, 0, ',', '.') }}</div>
            <div class="stat-foot">Rp dari pesanan dibayar</div>
        </div>
        <div class="stat-card stat-card-info">
            <div class="stat-label">Pesanan Dibayar</div>
            <div class="stat-value">{{ $count }}</div>
            <div class="stat-foot">Transaksi berhasil</div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1.4fr 1fr;gap:24px;align-items:start;">
        <div class="form-section" style="padding:20px;">
            <h3 style="font-size:15px;font-weight:800;color:#1c2a23;margin-bottom:12px;">Produk Terlaris</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Produk</th><th>Terjual</th><th>Omzet</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($perProduct as $row)
                            <tr>
                                <td><strong>{{ $row['name'] }}</strong></td>
                                <td>{{ $row['qty'] }}</td>
                                <td>Rp {{ number_format($row['omzet'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="empty-state" style="padding:40px;">Belum ada penjualan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="form-section" style="padding:20px;">
            <h3 style="font-size:15px;font-weight:800;color:#1c2a23;margin-bottom:12px;">Omzet Harian</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Tanggal</th><th>Omzet</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($perDay as $date => $total)
                            <tr>
                                <td>{{ \Illuminate\Support\Carbon::parse($date)->format('d/m/Y') }}</td>
                                <td><strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="empty-state" style="padding:40px;">Belum ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
