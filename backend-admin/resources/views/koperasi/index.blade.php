@extends('layouts.app')

@section('title', 'Koperasi - Panel Admin')
@section('page-title', 'Kelola Koperasi')

@section('content')
<div class="dashboard-shell">
    <div class="dashboard-header">
        <div>
            <p class="dashboard-kicker">Koperasi Sekolah</p>
            <h1 class="form-title" fetchpriority="high">Pesanan Koperasi</h1>
            <p class="form-subtitle">Pantau pembelian siswa di koperasi sekolah.</p>
        </div>
    </div>

    <div class="stats-grid" style="grid-template-columns: repeat(3, minmax(0, 1fr)); margin-bottom: 20px;">
        <div class="stat-card stat-card-green">
            <div class="stat-label">Total Pendapatan</div>
            <div class="stat-value">{{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</div>
            <div class="stat-foot">Status lunas</div>
        </div>
        <div class="stat-card stat-card-blue">
            <div class="stat-label">Pending</div>
            <div class="stat-value">{{ number_format($totalPending ?? 0, 0, ',', '.') }}</div>
            <div class="stat-foot">Menunggu pembayaran</div>
        </div>
        <div class="stat-card stat-card-gold">
            <div class="stat-label">Total Pesanan</div>
            <div class="stat-value">{{ $orders->count() }}</div>
            <div class="stat-foot">Semua transaksi</div>
        </div>
    </div>

    <div class="form-section" style="padding:16px;overflow-x:auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:12px;">
            <h2 style="font-size:17px;font-weight:800;color:#1c2a23;">Daftar Pesanan</h2>
        </div>

        @if ($orders->isEmpty())
            <div class="empty-state">
                <p>Belum ada pesanan koperasi.</p>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No. Pesanan</th>
                            <th class="hide-sm">Siswa</th>
                            <th>Total</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th>Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td><strong>#{{ $order->id }}</strong></td>
                                <td class="hide-sm">
                                    <strong>{{ $order->user->name ?? 'Unknown' }}</strong>
                                    <div style="font-size:12px;color:#8a9890;">{{ $order->user->username ?? '' }}</div>
                                </td>
                                <td><strong>{{ number_format($order->total, 0, ',', '.') }}</strong></td>
                                <td>{{ $order->metode ? ucfirst($order->metode) : '-' }}</td>
                                <td>
                                    @php
                                        $statusClass = match($order->status) {
                                            'lunas' => 'badge-success',
                                            'batal' => 'badge-danger',
                                            default => 'badge-warning'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                                </td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('koperasi.show', $order->id) }}" class="action-btn action-btn-view">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
