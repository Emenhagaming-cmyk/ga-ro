@extends('layouts.app')

@section('title', 'Rekap SPP - Panel Admin')
@section('page-title', 'Rekap SPP')

@section('content')
@php
    $totalNominal = 0;
    $totalTerbayar = 0;
    $lunas = 0;
    $blm = 0;
    foreach ($siswa as $s) {
        foreach ($s->sppBills as $b) {
            $tb = $b->payments->sum('amount');
            $totalNominal += $b->nominal;
            $totalTerbayar += min($tb, $b->nominal);
            $b->payments->sum('amount') >= $b->nominal ? $lunas++ : $blm++;
        }
    }
@endphp

<div class="rk-head">
    <h1 class="form-title">Rekapitulasi SPP</h1>
    <p class="form-subtitle" style="margin-bottom:0;">Status tagihan SPP semua siswa. ({{ $siswa->count() }} siswa)</p>
</div>

<div class="rk-stats">
    <div class="rk-stat">
        <span>Total tagihan</span>
        <strong>{{ number_format($totalNominal, 0, ',', '.') }}</strong>
    </div>
    <div class="rk-stat ok">
        <span>Terkumpul</span>
        <strong>{{ number_format($totalTerbayar, 0, ',', '.') }}</strong>
    </div>
    <div class="rk-stat">
        <span>Sisa tagihan</span>
        <strong>{{ number_format(max($totalNominal - $totalTerbayar, 0), 0, ',', '.') }}</strong>
    </div>
    <div class="rk-stat">
        <span>Bulan tagihan</span>
        <strong>{{ $lunas }} lunas / {{ $blm }} belum</strong>
    </div>
</div>

<div class="form-section" style="padding:16px;overflow-x:auto;">
    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="rk-table">
        <thead>
            <tr>
                <th>Siswa</th>
                @foreach ($periodeBulan = $siswa->flatMap(fn ($s) => $s->sppBills->pluck('periode'))->unique()->sort()->values() as $p)
                <th class="rk-period">{{ formatPeriodeShort($p) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($siswa as $s)
            <tr>
                <td>
                    <strong>{{ $s->name }}</strong>
                    <div style="font-size:12px;color:#8a9890;">{{ $s->username }}</div>
                </td>
                @foreach ($periodeBulan as $p)
                @php
                    $bill = $s->sppBills->firstWhere('periode', $p);
                @endphp
                <td class="rk-cell">
                    @if ($bill)
                        @if ($bill->payments->sum('amount') >= $bill->nominal)
                            <span class="rk-badge lunas">Lunas</span>
                        @else
                            <span class="rk-badge belum">Rp{{ number_format($bill->nominal - $bill->payments->sum('amount'), 0, ',', '.') }}</span>
                        @endif
                    @else
                        <span class="rk-dash">–</span>
                    @endif
                </td>
                @endforeach
            </tr>
            @empty
            <tr><td colspan="{{ 1 + $periodeBulan->count() }}" class="empty-state"><p>Belum ada siswa.</p></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<style>
.rk-head { margin-bottom: 20px; }
.rk-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-bottom: 20px; }
.rk-stat { background: #fff; border: 1px solid #e5eae4; border-radius: 14px; padding: 16px 18px; }
.rk-stat.ok { background: #f0fdf4; border-color: #bbf7d0; }
.rk-stat span { display: block; font-size: 12px; color: #8a9890; font-weight: 600; margin-bottom: 4px; }
.rk-stat strong { font-size: 18px; font-weight: 800; color: #1c2a23; }
.rk-table { margin-top: 0; }
.rk-period { text-align: center; }
.rk-cell { text-align: center; }
.rk-badge { display: inline-flex; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 800; }
.rk-badge.lunas { background: #dcfce7; color: #166534; }
.rk-badge.belum { background: #fff1df; color: #b06a1f; }
.rk-dash { color: #c3ccc5; }
@media (max-width: 768px) {
    .rk-period { min-width: 84px; }
}
</style>
@endsection