@extends('layouts.app')

@section('title', 'Status SPP - Orang Tua/Wali')

@section('content')
<div style="max-width: 620px; margin: 0 auto; padding-top: 8px;">
    <div class="ot-card">
        <div class="ot-head">
            <img src="{{ asset('logo.png') }}" alt="Logo Sekolah" style="height:40px;width:auto;border-radius:10px;background:#fff;padding:4px;border:1px solid #e3e8e3;" />
            <div>
                <h1 class="ot-title">Status SPP — {{ $siswa->name }}</h1>
                <p class="ot-sub">SMK Bahrul Ulum</p>
            </div>
        </div>

        @php
            $total = 0;
            $terbayar = 0;
            foreach ($bills as $b) {
                $total += $b->nominal;
                $terbayar += min($b->payments->sum('amount'), $b->nominal);
            }
            $sisa = max($total - $terbayar, 0);
            $statusKeseluruhan = $bills->isEmpty() ? null : ($sisa > 0 ? 'belum' : 'lunas');
        @endphp

        <div class="ot-summary {{ $statusKeseluruhan ?? '' }}">
            <span class="ot-sum-label">Sisa tagihan {{ $bills->count() }} bulan terakhir</span>
            <span class="ot-sum-value">Rp{{ number_format($sisa, 0, ',', '.') }}</span>
            @if ($statusKeseluruhan === 'lunas')
            <span class="ot-sum-badge lunas">Semua Lunas</span>
            @elseif ($statusKeseluruhan === 'belum')
            <span class="ot-sum-badge belum">Masih Ada Tagihan</span>
            @endif
        </div>

        @if ($bills->isEmpty())
        <div class="empty-state"><p>Belum ada tagihan SPP.</p></div>
        @else
        <div class="ot-list">
            @foreach ($bills as $b)
            @php
                $tb = $b->payments->sum('amount');
                $lunas = $tb >= $b->nominal;
            @endphp
            <div class="ot-item">
                <div class="ot-item-head">
                    <strong>{{ \Carbon\Carbon::parse($b->periode . '-01')->translatedFormat('F Y') }}</strong>
                    <span class="ot-badge {{ $lunas ? 'lunas' : 'belum' }}">{{ $lunas ? 'Lunas' : 'Belum' }}</span>
                </div>
                <div class="ot-item-rows">
                    <div class="ot-row"><span>Nominal</span><strong>Rp{{ number_format($b->nominal, 0, ',', '.') }}</strong></div>
                    @if (!$lunas && $tb > 0)
                    <div class="ot-row"><span>Terbayar</span><strong>Rp{{ number_format($tb, 0, ',', '.') }}</strong></div>
                    <div class="ot-row ot-sisa"><span>Sisa</span><strong>Rp{{ number_format($b->nominal - $tb, 0, ',', '.') }}</strong></div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <div class="ot-foot">
            Pembayaran dapat dilakukan di kasir PPDB/SMP atau transfer ke rekening sekolah.
            Hubungi admin jika ada pertanyaan.
        </div>
    </div>
</div>
@endsection

<style>
.ot-card { background: #fff; border: 1px solid #e5eae4; border-radius: 20px; padding: 24px; box-shadow: 0 12px 28px rgba(35,55,42,0.06); }
.ot-head { display: flex; align-items: center; gap: 14px; margin-bottom: 22px; }
.ot-title { font-size: 19px; font-weight: 800; color: #1c2a23; letter-spacing: -0.02em; margin: 0; }
.ot-sub { font-size: 13px; color: #8a9890; margin: 0; }
.ot-summary {
    position: relative; border-radius: 16px; padding: 18px 20px; margin-bottom: 20px;
    background: linear-gradient(135deg, #2d5a3d 0%, #3a6450 60%, #4a8a62 100%); color: #fff;
    display: flex; flex-direction: column; gap: 4px;
}
.ot-sum-label { font-size: 12px; color: #d6e8db; font-weight: 600; }
.ot-sum-value { font-size: 28px; font-weight: 800; letter-spacing: -0.02em; }
.ot-sum-badge {
    align-self: flex-start; margin-top: 6px; padding: 4px 12px; border-radius: 999px;
    font-size: 11px; font-weight: 800;
}
.ot-sum-badge.lunas { background: #d9f2c9; color: #166534; }
.ot-sum-badge.belum { background: #fff1df; color: #9a5b13; }
.ot-list { display: flex; flex-direction: column; gap: 12px; }
.ot-item { border: 1px solid #ecefec; border-radius: 14px; padding: 14px 16px; }
.ot-item-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
.ot-item-head strong { font-size: 14px; font-weight: 800; color: #1c2a23; }
.ot-badge { padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 800; }
.ot-badge.lunas { background: #dcfce7; color: #166534; }
.ot-badge.belum { background: #fff1df; color: #b06a1f; }
.ot-item-rows { display: flex; flex-direction: column; gap: 4px; }
.ot-row { display: flex; align-items: center; justify-content: space-between; font-size: 13px; color: #8a9890; }
.ot-row strong { color: #1c2a23; font-weight: 700; }
.ot-row.ot-sisa strong { color: #c0444f; }
.ot-foot { margin-top: 20px; padding-top: 14px; border-top: 1px dashed #e3e8e3; font-size: 12px; color: #8a9890; line-height: 1.7; }
</style>