@extends('layouts.app')

@section('title', 'Laporan Koperasi - Panel Admin')
@section('page-title', 'Laporan Koperasi')

@section('content')
@php
    $avg = $count > 0 ? (int) round($omzet / $count) : 0;
    $totalQty = (int) collect($perProduct)->sum('qty');
    $maxQty = (int) collect($perProduct)->max('qty') ?: 1;
    $maxDay = (float) collect($perDay)->max() ?: 1;
@endphp

<div class="kop-page">
    @include('koperasi._kop-style')

    {{-- Header --}}
    <div class="kop-header">
        <div>
            <p class="kop-kicker">Koperasi Sekolah</p>
            <h1 class="kop-title">Laporan Koperasi</h1>
            <p class="kop-subtitle">Rekap omzet, produk terlaris, dan penjualan harian.</p>
        </div>
    </div>

    @include('koperasi._tabs', ['active' => 'laporan'])

    {{-- Statistik --}}
    <div class="kop-stats">
        <div class="kop-stat">
            <div class="kop-stat-icon" style="background:#e8f0e6;color:#3a6450;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>
            </div>
            <div class="kop-stat-info">
                <span class="kop-stat-label">Total Omzet</span>
                <span class="kop-stat-value">Rp {{ number_format($omzet, 0, ',', '.') }}</span>
                <span class="kop-stat-foot">Dari pesanan dibayar</span>
            </div>
        </div>
        <div class="kop-stat">
            <div class="kop-stat-icon" style="background:#dbeafe;color:#1d4ed8;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="kop-stat-info">
                <span class="kop-stat-label">Pesanan Dibayar</span>
                <span class="kop-stat-value">{{ $count }}</span>
                <span class="kop-stat-foot">Transaksi berhasil</span>
            </div>
        </div>
        <div class="kop-stat">
            <div class="kop-stat-icon" style="background:#fef3c7;color:#b45309;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div class="kop-stat-info">
                <span class="kop-stat-label">Rata-rata / Transaksi</span>
                <span class="kop-stat-value">Rp {{ number_format($avg, 0, ',', '.') }}</span>
                <span class="kop-stat-foot">Omzet ÷ transaksi</span>
            </div>
        </div>
        <div class="kop-stat">
            <div class="kop-stat-icon" style="background:#dcfce7;color:#166534;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            </div>
            <div class="kop-stat-info">
                <span class="kop-stat-label">Item Terjual</span>
                <span class="kop-stat-value">{{ $totalQty }}</span>
                <span class="kop-stat-foot">Total qty semua produk</span>
            </div>
        </div>
    </div>

    {{-- Dua panel berdampingan di desktop --}}
    <div class="kop-two-col">
        {{-- Produk Terlaris --}}
        <div class="kop-panel">
            <div class="kop-panel-head">
                <div>
                    <h2 class="kop-panel-title">Produk Terlaris</h2>
                    <p class="kop-panel-sub">Diurutkan berdasarkan jumlah terjual</p>
                </div>
            </div>
            <div class="kop-table-wrap">
                <table class="kop-table">
                    <thead>
                        <tr>
                            <th scope="col">Produk</th>
                            <th scope="col" class="kop-nowrap">Terjual</th>
                            <th scope="col" class="kop-nowrap">Omzet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($perProduct as $row)
                            <tr>
                                <td style="min-width:200px;">
                                    <div class="kop-rank-row">
                                        <span class="kop-rank {{ $loop->iteration <= 3 ? 'top-' . $loop->iteration : '' }}">{{ $loop->iteration }}</span>
                                        <div style="min-width:0;flex:1;">
                                            <span class="kop-user-name">{{ $row['name'] }}</span>
                                            <div class="kop-bar"><i style="width: {{ round(($row['qty'] / $maxQty) * 100) }}%"></i></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="kop-nowrap"><strong>{{ $row['qty'] }}x</strong></td>
                                <td class="kop-nowrap"><strong class="kop-total">Rp {{ number_format($row['omzet'], 0, ',', '.') }}</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <div class="kop-empty">
                                        <div class="kop-empty-icon">
                                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        </div>
                                        <h3>Belum ada penjualan</h3>
                                        <p>Data produk terlaris muncul setelah ada transaksi.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Omzet Harian --}}
        <div class="kop-panel">
            <div class="kop-panel-head">
                <div>
                    <h2 class="kop-panel-title">Omzet Harian</h2>
                    <p class="kop-panel-sub">Total pembayaran per tanggal</p>
                </div>
            </div>
            <div class="kop-table-wrap">
                <table class="kop-table">
                    <thead>
                        <tr>
                            <th scope="col">Tanggal</th>
                            <th scope="col" class="kop-nowrap">Omzet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($perDay as $date => $total)
                            <tr>
                                <td class="kop-nowrap"><span class="kop-date">{{ \Illuminate\Support\Carbon::parse($date)->format('d M Y') }}</span></td>
                                <td style="min-width:140px;">
                                    <strong class="kop-total">Rp {{ number_format($total, 0, ',', '.') }}</strong>
                                    <div class="kop-bar bar-blue"><i style="width: {{ round(($total / $maxDay) * 100) }}%"></i></div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">
                                    <div class="kop-empty">
                                        <div class="kop-empty-icon">
                                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                        </div>
                                        <h3>Belum ada data</h3>
                                        <p>Omzet harian muncul setelah ada pembayaran.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection