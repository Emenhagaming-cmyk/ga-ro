@extends('layouts.app')

@section('title', 'Detail Pesanan Koperasi - Panel Admin')
@section('page-title', 'Detail Pesanan Koperasi')

@section('content')
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
    $isFailed = in_array($order->status, ['batal', 'expired']);
    $currentStep = match ($order->status) {
        'pending' => 1,
        'dibayar' => 2,
        'diambil', 'lunas' => 3,
        default => null,
    };
@endphp

<div class="kop-page">
    @include('koperasi._kop-style')

    {{-- Header --}}
    <div class="kop-header">
        <div>
            <p class="kop-kicker">Koperasi Sekolah</p>
            <h1 class="kop-title">Pesanan <span class="kop-kode">{{ $order->kode }}</span></h1>
            <p class="kop-subtitle">
                Siswa: <strong>{{ $order->user->name ?? 'Unknown' }}</strong> ({{ $order->user->username ?? '' }}) —
                Total: <strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
            </p>
        </div>
        <div class="kop-header-actions">
            <a href="{{ route('koperasi.index') }}" class="kop-btn kop-btn-outline">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Kembali
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

    <div class="kop-panel">
        <div class="kop-panel-body">
            {{-- Alur pesanan --}}
            @if (! $isFailed)
                <div class="kop-steps">
                    <div class="kop-step {{ $currentStep >= 1 ? 'done' : '' }}"><i>{{ $currentStep > 1 ? '✓' : '1' }}</i> Pesan Diterima</div>
                    <span class="kop-step-sep {{ $currentStep >= 2 ? 'done' : '' }}"></span>
                    <div class="kop-step {{ $currentStep >= 2 ? 'done' : '' }}"><i>{{ $currentStep > 2 ? '✓' : '2' }}</i> Dibayar</div>
                    <span class="kop-step-sep {{ $currentStep >= 3 ? 'done' : '' }}"></span>
                    <div class="kop-step {{ $currentStep >= 3 ? 'done' : '' }}"><i>{{ $currentStep >= 3 ? '✓' : '3' }}</i> Diambil di TU</div>
                </div>
            @else
                <div class="kop-failed-note">
                    Pesanan ini berstatus <strong>{{ $label }}</strong> dan tidak dapat diproses lebih lanjut.
                </div>
            @endif

            {{-- Info ringkas --}}
            <div class="kop-info-grid">
                <div class="kop-info-item">
                    <span class="kop-info-label">Status</span>
                    <span class="kop-info-value"><span class="kop-badge kop-badge--{{ $tone }}"><i></i>{{ $label }}</span></span>
                </div>
                <div class="kop-info-item">
                    <span class="kop-info-label">Kode Ambil (ruang TU)</span>
                    <span class="kop-info-value mono">{{ $order->pickup_code ?? '—' }}</span>
                </div>
                <div class="kop-info-item">
                    <span class="kop-info-label">Metode Pembayaran</span>
                    <span class="kop-info-value">{{ $metodeLabel }}</span>
                </div>
                <div class="kop-info-item">
                    <span class="kop-info-label">Waktu Pesan</span>
                    <span class="kop-info-value">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                @if ($order->paid_at)
                    <div class="kop-info-item">
                        <span class="kop-info-label">Dibayar Pada</span>
                        <span class="kop-info-value">{{ $order->paid_at->format('d/m/Y H:i') }}</span>
                    </div>
                @endif
                @if ($order->picked_up_at)
                    <div class="kop-info-item">
                        <span class="kop-info-label">Diambil Pada</span>
                        <span class="kop-info-value">{{ $order->picked_up_at->format('d/m/Y H:i') }}</span>
                    </div>
                @endif
            </div>

            {{-- Item pesanan --}}
            <h2 class="kop-panel-title" style="margin-bottom:10px;">Item Pesanan</h2>
            <div class="kop-table-wrap" style="border:1px solid var(--kop-border);border-radius:10px;">
                <table class="kop-table">
                    <thead>
                        <tr>
                            <th scope="col">Produk</th>
                            <th scope="col" class="kop-nowrap">Harga Satuan</th>
                            <th scope="col" class="kop-nowrap">Qty</th>
                            <th scope="col" class="kop-nowrap">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td><span class="kop-user-name">{{ $item['name'] ?? $item['title'] ?? 'Produk' }}</span></td>
                                <td class="kop-nowrap">Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}</td>
                                <td class="kop-nowrap">
                                    <span class="kop-chip-count">{{ $item['qty'] ?? 0 }}x</span>
                                </td>
                                <td class="kop-nowrap"><strong class="kop-total">Rp {{ number_format(($item['price'] ?? 0) * ($item['qty'] ?? 0), 0, ',', '.') }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3">Total Pembayaran</td>
                            <td class="kop-nowrap" style="color:var(--kop-primary);">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Aksi --}}
            @if ($order->status === 'dibayar' || $order->status === 'pending')
                <div class="kop-detail-actions">
                    @if ($order->status === 'dibayar')
                        <form action="{{ route('koperasi.ambil', $order->id) }}" method="POST"
                              onsubmit="return confirm('Tandai pesanan {{ $order->kode }} sudah diambil siswa?')">
                            @csrf
                            <button type="submit" class="kop-btn kop-btn-primary">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                Tandai Sudah Diambil
                            </button>
                        </form>
                    @endif
                    @if ($order->status === 'pending')
                        <form action="{{ route('koperasi.batal', $order->id) }}" method="POST"
                              onsubmit="return confirm('Batalkan pesanan {{ $order->kode }}?')">
                            @csrf
                            <button type="submit" class="kop-btn kop-btn-danger">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                Batalkan Pesanan
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection