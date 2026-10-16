@extends('layouts.app')

@section('title', 'Koperasi - Panel Admin')
@section('page-title', 'Kelola Koperasi')

@section('content')
<div class="dashboard-shell">
    <div class="dashboard-header">
        <div>
            <p class="dashboard-kicker">Koperasi Sekolah</p>
            <h1 class="form-title" fetchpriority="high">Pesanan Koperasi</h1>
            <p class="form-subtitle">Pantau pesanan siswa, pembayaran, dan pengambilan di ruang TU.</p>
        </div>
    </div>

    @include('koperasi._tabs', ['active' => 'orders'])

    @if (session('success'))
        <div class="alert alert-success">✓ {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="stats-grid">
        <div class="stat-card stat-card-success">
            <div class="stat-label">Total Omzet</div>
            <div class="stat-value">{{ number_format($stats['omzet'], 0, ',', '.') }}</div>
            <div class="stat-foot">Sudah dibayar</div>
        </div>
        <div class="stat-card stat-card-warning">
            <div class="stat-label">Menunggu Bayar</div>
            <div class="stat-value">{{ $stats['pending'] }}</div>
            <div class="stat-foot">Belum dibayar</div>
        </div>
        <div class="stat-card stat-card-info">
            <div class="stat-label">Siap Diambil</div>
            <div class="stat-value">{{ $stats['siap'] }}</div>
            <div class="stat-foot">Sudah dibayar, di ruang TU</div>
        </div>
        <div class="stat-card stat-card-primary">
            <div class="stat-label">Sudah Diambil</div>
            <div class="stat-value">{{ $stats['diambil'] }}</div>
            <div class="stat-foot">Selesai</div>
        </div>
    </div>

    <form class="filter-toolbar" method="GET" action="{{ route('koperasi.index') }}">
        <div class="filter-group filter-search">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" name="q" value="{{ $q }}" class="filter-input" placeholder="Cari kode / kode ambil / nama siswa..." autocomplete="off">
        </div>
        <div class="filter-group">
            <select class="filter-select" name="status" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="pending" @selected($status === 'pending')>Menunggu Bayar</option>
                <option value="dibayar" @selected($status === 'dibayar')>Siap Diambil</option>
                <option value="diambil" @selected($status === 'diambil')>Sudah Diambil</option>
                <option value="batal" @selected($status === 'batal')>Dibatalkan</option>
                <option value="expired" @selected($status === 'expired')>Kedaluwarsa</option>
            </select>
        </div>
        <button type="submit" class="btn btn-secondary">Cari</button>
        <a href="{{ route('koperasi.index') }}" class="btn btn-secondary" style="text-decoration:none;">Reset</a>
    </form>

    <div class="form-section" style="padding:0;overflow:hidden;">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th class="hide-sm">Kode Ambil</th>
                        <th class="hide-sm">Siswa</th>
                        <th>Total</th>
                        <th class="hide-sm">Metode</th>
                        <th>Status</th>
                        <th class="hide-sm">Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        @php
                            $map = [
                                'pending' => ['Menunggu Bayar', 'badge-warning'],
                                'dibayar' => ['Siap Diambil', 'badge-info'],
                                'diambil' => ['Sudah Diambil', 'badge-success'],
                                'batal' => ['Dibatalkan', 'badge-danger'],
                                'expired' => ['Kedaluwarsa', 'badge-danger'],
                                'lunas' => ['Lunas', 'badge-success'],
                            ];
                            [$label, $badge] = $map[$order->status] ?? [ucfirst($order->status), 'badge-neutral'];
                            $metodeLabel = match ($order->metode) {
                                'tabungan' => 'Saldo Tabungan',
                                'midtrans' => 'Midtrans',
                                'qris' => 'QRIS',
                                'transfer' => 'Transfer',
                                default => '-',
                            };
                        @endphp
                        <tr>
                            <td><strong>{{ $order->kode }}</strong></td>
                            <td class="hide-sm"><span style="font-family:monospace;font-weight:700;">{{ $order->pickup_code ?? '-' }}</span></td>
                            <td class="hide-sm">
                                <strong>{{ $order->user->name ?? 'Unknown' }}</strong>
                                <div style="font-size:12px;color:#8a9890;">{{ $order->user->username ?? '' }}</div>
                            </td>
                            <td><strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong></td>
                            <td class="hide-sm">{{ $metodeLabel }}</td>
                            <td><span class="badge {{ $badge }}">{{ $label }}</span></td>
                            <td class="hide-sm">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('koperasi.show', $order->id) }}" class="action-btn action-btn-view">Detail</a>
                                    @if ($order->status === 'dibayar')
                                        <form action="{{ route('koperasi.ambil', $order->id) }}" method="POST" onsubmit="return confirm('Tandai pesanan {{ $order->kode }} sudah diambil?')">
                                            @csrf
                                            <button type="submit" class="action-btn action-btn-edit">Sudah Diambil</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-state" style="padding:60px 20px;">
                                <div style="font-size:40px;margin-bottom:12px;">🛒</div>
                                <div style="font-weight:700;">Belum ada pesanan koperasi</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #f0f3f0;">
            {{ $orders->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
