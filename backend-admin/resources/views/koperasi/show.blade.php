@extends('layouts.app')

@section('title', 'Detail Pesanan Koperasi - Panel Admin')
@section('page-title', 'Detail Pesanan Koperasi')

@section('content')
<div class="dashboard-shell">
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

    <div class="dashboard-header">
        <div>
            <p class="dashboard-kicker">Koperasi Sekolah</p>
            <h1 class="form-title" fetchpriority="high">Pesanan {{ $order->kode }}</h1>
            <p class="form-subtitle">
                Siswa: <strong>{{ $order->user->name ?? 'Unknown' }}</strong> ({{ $order->user->username ?? '' }}) —
                Total: <strong style="color:#3a6450;">Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
            </p>
        </div>
        <a href="{{ route('koperasi.index') }}" class="btn btn-secondary" style="text-decoration:none;padding:8px 18px;font-size:13px;">Kembali</a>
    </div>

    @include('koperasi._tabs', ['active' => 'orders'])

    @if (session('success'))
        <div class="alert alert-success">✓ {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="form-section" style="padding:20px;">
        <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:20px;">
            <div style="flex:1;min-width:140px;">
                <span style="font-size:12px;color:#8a9890;font-weight:600;">Status</span>
                <div style="margin-top:4px;"><span class="badge {{ $badge }}">{{ $label }}</span></div>
            </div>
            <div style="flex:1;min-width:140px;">
                <span style="font-size:12px;color:#8a9890;font-weight:600;">Kode Ambil (ruang TU)</span>
                <div style="margin-top:4px;font-weight:800;font-family:monospace;font-size:16px;">{{ $order->pickup_code ?? '-' }}</div>
            </div>
            <div style="flex:1;min-width:140px;">
                <span style="font-size:12px;color:#8a9890;font-weight:600;">Metode Pembayaran</span>
                <div style="margin-top:4px;font-weight:700;">{{ $metodeLabel }}</div>
            </div>
            <div style="flex:1;min-width:140px;">
                <span style="font-size:12px;color:#8a9890;font-weight:600;">Waktu Pesan</span>
                <div style="margin-top:4px;font-weight:700;">{{ $order->created_at->format('d/m/Y H:i') }}</div>
            </div>
            @if ($order->paid_at)
            <div style="flex:1;min-width:140px;">
                <span style="font-size:12px;color:#8a9890;font-weight:600;">Dibayar Pada</span>
                <div style="margin-top:4px;font-weight:700;">{{ $order->paid_at->format('d/m/Y H:i') }}</div>
            </div>
            @endif
            @if ($order->picked_up_at)
            <div style="flex:1;min-width:140px;">
                <span style="font-size:12px;color:#8a9890;font-weight:600;">Diambil Pada</span>
                <div style="margin-top:4px;font-weight:700;">{{ $order->picked_up_at->format('d/m/Y H:i') }}</div>
            </div>
            @endif
        </div>

        <h3 style="font-size:15px;font-weight:800;color:#1c2a23;margin-bottom:12px;">Item Pesanan</h3>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Harga Satuan</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td><strong>{{ $item['name'] ?? $item['title'] ?? 'Produk' }}</strong></td>
                            <td>{{ number_format($item['price'] ?? 0, 0, ',', '.') }}</td>
                            <td>{{ $item['qty'] ?? 0 }}</td>
                            <td><strong>{{ number_format(($item['price'] ?? 0) * ($item['qty'] ?? 0), 0, ',', '.') }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($order->status === 'dibayar')
            <form action="{{ route('koperasi.ambil', $order->id) }}" method="POST" style="margin-top:20px;" onsubmit="return confirm('Tandai pesanan {{ $order->kode }} sudah diambil siswa?')">
                @csrf
                <button type="submit" class="btn btn-primary">Tandai Sudah Diambil</button>
            </form>
        @endif
    </div>
</div>
@endsection
