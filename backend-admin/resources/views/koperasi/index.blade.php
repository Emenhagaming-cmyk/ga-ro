@extends('layouts.app')

@section('title', 'Koperasi - Panel Admin')
@section('page-title', 'Kelola Koperasi')

@section('content')
<div class="kop-page">
    @include('koperasi._kop-style')

    {{-- ===== Header ===== --}}
    <div class="kop-header">
        <div>
            <p class="kop-kicker">Koperasi Sekolah</p>
            <h1 class="kop-title">Pesanan Koperasi</h1>
            <p class="kop-subtitle">Pantau pesanan siswa, pembayaran, dan pengambilan di ruang TU.</p>
        </div>
        <div class="kop-header-actions">
            <a href="{{ route('koperasi.export', request()->query()) }}" class="kop-btn kop-btn-outline">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export CSV
            </a>
        </div>
    </div>

    @include('koperasi._tabs', ['active' => 'orders'])

    @if (session('success'))
        <div class="alert alert-success">✓ {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    {{-- ===== Statistik ===== --}}
    <div class="kop-stats">
        <div class="kop-stat">
            <div class="kop-stat-icon" style="background:#e8f0e6;color:#3a6450;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>
            </div>
            <div class="kop-stat-info">
                <span class="kop-stat-label">Total Omzet</span>
                <span class="kop-stat-value">Rp {{ number_format($stats['omzet'], 0, ',', '.') }}</span>
                <span class="kop-stat-foot">Sudah dibayar</span>
            </div>
        </div>
        <div class="kop-stat">
            <div class="kop-stat-icon" style="background:#fef3c7;color:#b45309;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="kop-stat-info">
                <span class="kop-stat-label">Menunggu Bayar</span>
                <span class="kop-stat-value">{{ $stats['pending'] }}</span>
                <span class="kop-stat-foot">Belum dibayar</span>
            </div>
        </div>
        <div class="kop-stat">
            <div class="kop-stat-icon" style="background:#dbeafe;color:#1d4ed8;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"/><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
            </div>
            <div class="kop-stat-info">
                <span class="kop-stat-label">Siap Diambil</span>
                <span class="kop-stat-value">{{ $stats['siap'] }}</span>
                <span class="kop-stat-foot">Sudah dibayar, di ruang TU</span>
            </div>
        </div>
        <div class="kop-stat">
            <div class="kop-stat-icon" style="background:#dcfce7;color:#166534;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="kop-stat-info">
                <span class="kop-stat-label">Sudah Diambil</span>
                <span class="kop-stat-value">{{ $stats['diambil'] }}</span>
                <span class="kop-stat-foot">Selesai</span>
            </div>
        </div>
    </div>

    {{-- ===== Filter ===== --}}
    <form class="kop-filter" method="GET" action="{{ route('koperasi.index') }}">
        <input type="hidden" name="status" value="{{ $status }}">
        <div class="kop-filter-row">
            <label class="kop-search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="q" value="{{ $q }}" placeholder="Cari kode pesanan, kode ambil, atau nama siswa…" autocomplete="off">
            </label>
            <div class="kop-filter-btns">
                <button type="submit" class="kop-btn kop-btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Cari
                </button>
                <a href="{{ route('koperasi.index') }}" class="kop-btn kop-btn-ghost">Reset</a>
            </div>
        </div>
    </form>

    {{-- Chip filter status (mempertahankan query lain seperti ?q=) --}}
    <div class="kop-chips">
        <a href="{{ route('koperasi.index', array_merge(request()->query(), ['status' => null])) }}"
           class="kop-chip {{ empty($status) ? 'active' : '' }}">Semua</a>
        <a href="{{ route('koperasi.index', array_merge(request()->query(), ['status' => 'pending'])) }}"
           class="kop-chip {{ $status === 'pending' ? 'active' : '' }}">
            Menunggu Bayar <span class="kop-chip-count">{{ $stats['pending'] }}</span>
        </a>
        <a href="{{ route('koperasi.index', array_merge(request()->query(), ['status' => 'dibayar'])) }}"
           class="kop-chip {{ $status === 'dibayar' ? 'active' : '' }}">
            Siap Diambil <span class="kop-chip-count">{{ $stats['siap'] }}</span>
        </a>
        <a href="{{ route('koperasi.index', array_merge(request()->query(), ['status' => 'diambil'])) }}"
           class="kop-chip {{ $status === 'diambil' ? 'active' : '' }}">
            Sudah Diambil <span class="kop-chip-count">{{ $stats['diambil'] }}</span>
        </a>
        <a href="{{ route('koperasi.index', array_merge(request()->query(), ['status' => 'batal'])) }}"
           class="kop-chip {{ $status === 'batal' ? 'active' : '' }}">Dibatalkan</a>
        <a href="{{ route('koperasi.index', array_merge(request()->query(), ['status' => 'expired'])) }}"
           class="kop-chip {{ $status === 'expired' ? 'active' : '' }}">Kedaluwarsa</a>
    </div>

    {{-- ===== Tabel ===== --}}
    <div class="kop-panel">
        <div class="kop-panel-head">
            <div>
                <h2 class="kop-panel-title">Daftar Pesanan</h2>
                <p class="kop-panel-sub">
                    Menampilkan {{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }} dari {{ $orders->total() }} pesanan
                </p>
            </div>
        </div>

        <div class="kop-table-wrap">
            <table class="kop-table">
                <thead>
                    <tr>
                        <th scope="col">Kode</th>
                        <th scope="col" class="kop-hide-sm">Kode Ambil</th>
                        <th scope="col" class="kop-hide-sm">Siswa</th>
                        <th scope="col">Total</th>
                        <th scope="col" class="kop-hide-sm">Metode</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="kop-hide-sm">Waktu</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        @php
                            $map = [
                                'pending' => ['Menunggu Bayar', 'pending'],
                                'dibayar' => ['Siap Diambil', 'dibayar'],
                                'diambil' => ['Sudah Diambil', 'diambil'],
                                'batal'   => ['Dibatalkan', 'batal'],
                                'expired' => ['Kedaluwarsa', 'expired'],
                                'lunas'   => ['Lunas', 'diambil'],
                            ];
                            [$label, $tone] = $map[$order->status] ?? [ucfirst($order->status), 'batal'];
                            $metodeLabel = match ($order->metode) {
                                'tabungan' => 'Saldo Tabungan',
                                'midtrans' => 'Midtrans',
                                'qris'     => 'QRIS',
                                'transfer' => 'Transfer',
                                default    => '-',
                            };
                        @endphp
                        <tr>
                            <td><span class="kop-kode">{{ $order->kode }}</span></td>
                            <td class="kop-hide-sm">
                                <span class="kop-pickup">{{ $order->pickup_code ?? '—' }}</span>
                            </td>
                            <td class="kop-hide-sm">
                                <div class="kop-user">
                                    <span class="kop-avatar">{{ mb_strtoupper(mb_substr($order->user->name ?? 'U', 0, 1)) }}</span>
                                    <span class="kop-user-text">
                                        <span class="kop-user-name">{{ $order->user->name ?? 'Unknown' }}</span>
                                        <span class="kop-user-sub">{{ $order->user->username ?? '' }}</span>
                                    </span>
                                </div>
                            </td>
                            <td><strong class="kop-total">Rp {{ number_format($order->total, 0, ',', '.') }}</strong></td>
                            <td class="kop-hide-sm">{{ $metodeLabel }}</td>
                            <td><span class="kop-badge kop-badge--{{ $tone }}"><i></i>{{ $label }}</span></td>
                            <td class="kop-hide-sm kop-nowrap">
                                <span class="kop-date">{{ $order->created_at->format('d M Y') }}</span>
                                <span class="kop-time">{{ $order->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td class="kop-nowrap">
                                <div class="kop-act">
                                    <a href="{{ route('koperasi.show', $order->id) }}" class="kop-act-btn kop-act--view">Detail</a>
                                    @if ($order->status === 'dibayar')
                                        <form action="{{ route('koperasi.ambil', $order->id) }}" method="POST"
                                              onsubmit="return confirm('Tandai pesanan {{ $order->kode }} sudah diambil?')">
                                            @csrf
                                            <button type="submit" class="kop-act-btn kop-act--ok">Diambil</button>
                                        </form>
                                    @endif
                                    @if ($order->status === 'pending')
                                        <form action="{{ route('koperasi.batal', $order->id) }}" method="POST"
                                              onsubmit="return confirm('Batalkan pesanan {{ $order->kode }}?')">
                                            @csrf
                                            <button type="submit" class="kop-act-btn kop-act--danger">Batalkan</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="kop-empty">
                                    <div class="kop-empty-icon">
                                        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                                    </div>
                                    <h3>Belum ada pesanan koperasi</h3>
                                    <p>Pesanan siswa akan muncul di sini setelah ada transaksi.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="kop-panel-foot">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection