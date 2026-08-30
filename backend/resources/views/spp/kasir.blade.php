@extends('layouts.app')

@section('title', 'Input SPP - Kasir')

@section('sidebar')
<a href="{{ route('spp.kasir') }}" class="active">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
    Input Pembayaran SPP
</a>
<a href="#">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    Riwayat
</a>
@endsection

@section('content')
<div style="max-width: 980px; margin: 0 auto;">
    <div class="sk-head">
        <div>
            <h1 class="sk-title">Input Pembayaran SPP</h1>
            <p class="sk-sub">Catat pembayaran SPP siswa (tunai / transfer). Status langsung terlihat siswa & guru.</p>
        </div>
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-top').submit();" class="sk-logout">Keluar</a>
        <form id="logout-form-top" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
    </div>

    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
    <div class="alert alert-error">
        <ul style="margin:0;padding-left:18px;">
            @foreach ($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="GET" class="sk-search">
        <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama / username siswa..." autocomplete="off" />
        <button type="submit" class="btn btn-primary">Cari</button>
        @if ($q !== '')
        <a href="{{ route('spp.kasir') }}" class="btn btn-secondary">Reset</a>
        @endif
    </form>

    @if ($bills->isEmpty())
    <div class="empty-state">
        <p>Tidak ada tagihan yang belum lunas.</p>
    </div>
    @else
    <div class="sk-stats">
        <div class="sk-stat">
            <span>Belum lunas</span>
            <strong>{{ $bills->count() }} bulan</strong>
        </div>
        <div class="sk-stat">
            <span>Total nominal</span>
            <strong>{{ number_format($bills->sum('nominal'), 0, ',', '.') }}</strong>
        </div>
    </div>

    <div class="sk-table-wrap">
        <table class="sk-table">
            <thead>
                <tr>
                    <th>Siswa</th>
                    <th>Periode</th>
                    <th>Nominal</th>
                    <th>Sisa</th>
                    <th>Catat Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bills as $bill)
                @php
                    $terbayar = $bill->payments->sum('amount');
                    $sisa = max($bill->nominal - $terbayar, 0);
                @endphp
                <tr>
                    <td>
                        <strong style="color:#1c2a23;">{{ $bill->user->name }}</strong>
                        <div style="font-size:12px;color:#8a9890;">{{ $bill->user->username }}</div>
                    </td>
                    <td>{{ formatPeriode($bill->periode) }}</td>
                    <td>{{ number_format($bill->nominal, 0, ',', '.') }}</td>
                    <td><strong style="color:#c0444f;">{{ number_format($sisa, 0, ',', '.') }}</strong></td>
                    <td>
                        <form method="POST" action="{{ route('spp.pay') }}" class="sk-pay-form">
                            @csrf
                            <input type="hidden" name="bill_id" value="{{ $bill->id }}" />
                            <select name="metode" required>
                                <option value="tunai">Tunai</option>
                                <option value="transfer">Transfer</option>
                            </select>
                            <input type="number" name="amount" min="1" max="{{ $sisa }}" value="{{ $sisa }}" inputmode="numeric" required />
                            <button type="submit" class="btn btn-primary" style="white-space:nowrap;">Bayar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection

<style>
.sk-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 24px; }
.sk-title { font-size: 24px; font-weight: 800; color: #1c2a23; letter-spacing: -0.03em; margin-bottom: 4px; }
.sk-sub { font-size: 14px; color: #647067; }
.sk-logout {
    display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 10px;
    background: #fef2f2; color: #dc2626; font-size: 13px; font-weight: 700; text-decoration: none; flex-shrink: 0;
}
.sk-search { display: flex; gap: 10px; margin-bottom: 20px; }
.sk-search input { max-width: 360px; }
.sk-stats { display: flex; gap: 14px; margin-bottom: 18px; }
.sk-stat {
    flex: 1; background: #fff; border: 1px solid #e5eae4; border-radius: 14px; padding: 16px 18px;
    display: flex; flex-direction: column; gap: 2px;
}
.sk-stat span { font-size: 12px; color: #8a9890; font-weight: 600; }
.sk-stat strong { font-size: 20px; font-weight: 800; color: #1c2a23; }
.sk-table-wrap { background: #fff; border: 1px solid #e5eae4; border-radius: 16px; overflow-x: auto; }
.sk-table { padding: 0 16px; }
.sk-pay-form { display: flex; gap: 8px; align-items: center; }
.sk-pay-form select { width: auto; min-width: 110px; }
.sk-pay-form input[type="number"] { width: 120px; }
@media (max-width: 768px) {
    .sk-head { flex-direction: column; }
    .sk-table-wrap { overflow-x: auto; }
    .sk-pay-form { flex-direction: column; align-items: stretch; }
    .sk-pay-form select, .sk-pay-form input[type="number"] { width: 100%; }
}
</style>