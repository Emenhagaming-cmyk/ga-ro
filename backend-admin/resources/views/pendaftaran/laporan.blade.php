@extends('layouts.app')

@section('title', 'Laporan Mingguan - Panel Admin')
@section('page-title', 'Laporan Mingguan')

@section('content')
<div class="laporan-shell">
    <div class="dashboard-header">
        <div>
            <p class="dashboard-kicker">Laporan</p>
            <h1 class="form-title">Laporan Pendaftaran Mingguan</h1>
            <p class="form-subtitle">Periode {{ $start->format('d M Y') }} — {{ $end->format('d M Y') }}</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="window.print()">🖨 Cetak / Simpan PDF</button>
    </div>

    <div class="report-paper">
        <div class="report-head">
            <img src="{{ asset('logo.png') }}" alt="Logo" style="height:56px;width:auto;" />
            <div>
                <h2>LAPORAN PENERIMAAN PESERTA DIDIK BARU</h2>
                <p>SMK Bahrul Ulum — Tahun Ajaran {{ now()->year }}/{{ now()->year + 1 }}</p>
                <p style="font-size:12px;color:#647067;">Periode: {{ $start->format('d M Y') }} — {{ $end->format('d M Y') }}</p>
            </div>
        </div>

        <div class="table-wrap">
            <table class="report-table">
                <thead>
                    <tr><th>Status</th><th>Jumlah</th></tr>
                </thead>
                <tbody>
                    <tr><td>Baru</td><td>{{ $stats['baru'] }}</td></tr>
                    <tr><td>Diproses</td><td>{{ $stats['diproses'] }}</td></tr>
                    <tr><td>Diterima</td><td>{{ $stats['diterima'] }}</td></tr>
                    <tr><td>Ditolak</td><td>{{ $stats['ditolak'] }}</td></tr>
                </tbody>
            </table>
        </div>

        <div class="table-wrap">
            <table class="report-table">
                <thead>
                    <tr><th>Jurusan</th><th>Jumlah</th></tr>
                </thead>
                <tbody>
                    <tr><td>RPL</td><td>{{ $stats['jurusan']['RPL'] }}</td></tr>
                    <tr><td>TKJ</td><td>{{ $stats['jurusan']['TKJ'] }}</td></tr>
                    <tr><td>AKL</td><td>{{ $stats['jurusan']['AKL'] }}</td></tr>
                </tbody>
            </table>
        </div>

        <h3 class="report-subtitle">Pendaftar Masuk Minggu Ini ({{ $mingguIni->count() }})</h3>
        <div class="table-wrap">
            <table class="report-table report-table-wide">
                <thead>
                    <tr><th>No</th><th>Nama</th><th>Jurusan</th><th>Status</th><th>Tanggal Daftar</th></tr>
                </thead>
                <tbody>
                    @forelse ($mingguIni as $i => $p)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $p->nama_lengkap }}</td>
                        <td>{{ $p->jurusan_pilihan }}</td>
                        <td>{{ ucfirst($p->status) }}</td>
                        <td>{{ $p->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;color:#9ba8a0;">Belum ada pendaftar masuk pada minggu ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="report-sign">
            <div>
                <p>Mengetahui,<br>Kepala Sekolah</p>
                <div style="height:56px;"></div>
                <p style="border-top:1px solid #1c2a23;padding-top:4px;">( ...................................... )</p>
            </div>
            <div>
                <p>{{ now()->format('d M Y') }},<br>Admin Pendaftaran</p>
                <div style="height:56px;"></div>
                <p style="border-top:1px solid #1c2a23;padding-top:4px;">( ...................................... )</p>
            </div>
        </div>
    </div>
</div>

<style>
    .laporan-shell {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .dashboard-kicker {
        display: inline-block;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.16em;
        color: #3a6450;
        margin-bottom: 6px;
    }

    .report-paper {
        background: #ffffff;
        border: 1px solid #dfe4dd;
        border-radius: 18px;
        padding: 36px 40px;
        box-shadow: 0 12px 24px rgba(35, 55, 42, 0.06);
    }

    .report-head {
        display: flex;
        align-items: center;
        gap: 16px;
        border-bottom: 3px double #3a6450;
        padding-bottom: 18px;
        margin-bottom: 22px;
    }

    .report-head h2 {
        font-size: 17px;
        font-weight: 800;
        letter-spacing: 0.02em;
        color: #1c2a23;
    }

    .report-head p {
        font-size: 13px;
        color: #475449;
        margin: 2px 0;
    }

    .report-table {
        margin: 0 0 22px;
        width: 60%;
        min-width: 0;
    }

    .report-table-wide {
        min-width: 520px;
    }

    .report-subtitle {
        font-size: 14px;
        font-weight: 800;
        color: #3a6450;
        margin: 18px 0 4px;
    }

    .report-sign {
        display: flex;
        justify-content: space-between;
        gap: 40px;
        margin-top: 36px;
        max-width: 480px;
        margin-left: auto;
    }

    .report-sign p {
        font-size: 13px;
        color: #1c2a23;
        line-height: 1.8;
    }

    @media (max-width: 600px) {
        .report-paper { padding: 22px 18px; }
        .report-table { width: 100%; }
        .report-table-wide { min-width: 480px; }
        .report-head { gap: 10px; }
        .report-head img { height: 44px; }
        .report-head h2 { font-size: 14px; }
        .report-sign { flex-direction: column; }
    }

    @media print {
        body { background: #fff; }
        .sidebar, .topbar, .sidebar-backdrop { display: none !important; }
        .main { margin-left: 0; }
        .main-content { max-width: none; padding: 0; }
        .dashboard-header .btn { display: none; }
        .report-paper { border: none; box-shadow: none; border-radius: 0; padding: 0; }
        .table-wrap { overflow: visible !important; }
        .table-wrap table { min-width: 0 !important; }
        .report-table { width: 100%; }
        .report-table th { background: #f0f4ee; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
@endsection