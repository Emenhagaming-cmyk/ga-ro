@extends('layouts.app')

@section('title', 'Rekap SPP - Guru/Admin')

@section('sidebar')
<a href="{{ route('spp.rekap') }}" class="active">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
    Rekap SPP
</a>
@endsection

@section('content')
<div style="max-width: 1080px; margin: 0 auto;">
    <div class="rk-head">
        <div>
            <h1 class="rk-title">Rekapitulasi SPP</h1>
            <p class="rk-sub">Status tagihan SPP semua siswa. <span style="color:#8a9890;">({{ $siswa->count() }} siswa)</span></p>
        </div>
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-top').submit();" class="rk-logout">Keluar</a>
        <form id="logout-form-top" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
    </div>

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

    <div class="rk-wrap">
        <table class="rk-table">
            <thead>
                <tr>
                    <th>Siswa</th>
                    @foreach ($periodeBulan = $siswa->flatMap(fn ($s) => $s->sppBills->pluck('periode'))->unique()->sort()->values() as $p)
                    <th class="rk-period">{{ \Carbon\Carbon::parse($p . '-01')->translatedFormat('M y') }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($siswa as $s)
                <tr>
                    <td>
                        <strong style="color:#1c2a23;">{{ $s->name }}</strong>
                        <div style="font-size:12px;color:#8a9890;">{{ $s->username }}</div>
                    </td>
                    @foreach ($periodeBulan as $p)
                    @php
                        $bill = $s->sppBills->firstWhere('periode', $p);
                    @endphp
                    <td>
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
</div>
@endsection

<style>
.rk-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 24px; }
.rk-title { font-size: 24px; font-weight: 800; color: #1c2a23; letter-spacing: -0.03em; margin-bottom: 4px; }
.rk-sub { font-size: 14px; color: #647067; }
.rk-logout {
    display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 10px;
    background: #fef2f2; color: #dc2626; font-size: 13px; font-weight: 700; text-decoration: none; flex-shrink: 0;
}
.rk-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-bottom: 20px; }
.rk-stat { background: #fff; border: 1px solid #e5eae4; border-radius: 14px; padding: 16px 18px; }
.rk-stat.ok { background: #f0fdf4; border-color: #bbf7d0; }
.rk-stat span { display: block; font-size: 12px; color: #8a9890; font-weight: 600; margin-bottom: 4px; }
.rk-stat strong { font-size: 18px; font-weight: 800; color: #1c2a23; }
.rk-wrap { background: #fff; border: 1px solid #e5eae4; border-radius: 16px; overflow-x: auto; }
.rk-table { padding: 0 16px; }
.rk-period { text-align: center; }
.rk-badge {
    display: inline-flex; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 800;
}
.rk-badge.lunas { background: #dcfce7; color: #166534; }
.rk-badge.belum { background: #fff1df; color: #b06a1f; }
.rk-dash { color: #c3ccc5; }
@media (max-width: 768px) {
    .rk-head { flex-direction: column; }
    .rk-table th, .rk-table td { padding: 8px 8px; }
    .rk-period { min-width: 84px; }
}
</style>