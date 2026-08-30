@extends('layouts.app')

@section('title', 'Kelola Tabungan - Panel Admin')
@section('page-title', 'Kelola Tabungan')

@section('content')
<div class="dashboard-shell">
    <div class="dashboard-header">
        <div>
            <p class="dashboard-kicker">Tabungan Siswa</p>
            <h1 class="form-title" fetchpriority="high">Kelola Tabungan Siswa</h1>
            <p class="form-subtitle">Pantau saldo dan riwayat transaksi setor/tarik siswa.</p>
        </div>
    </div>

    <div class="stats-grid" style="grid-template-columns: repeat(3, minmax(0, 1fr)); margin-bottom: 20px;">
        <div class="stat-card stat-card-green">
            <div class="stat-label">Total Saldo</div>
            <div class="stat-value">{{ number_format($totalSaldo ?? 0, 0, ',', '.') }}</div>
            <div class="stat-foot">Semua siswa</div>
        </div>
        <div class="stat-card stat-card-blue">
            <div class="stat-label">Jumlah Siswa</div>
            <div class="stat-value">{{ $siswa->count() }}</div>
            <div class="stat-foot">Akun dengan tabungan</div>
        </div>
        <div class="stat-card stat-card-gold">
            <div class="stat-label">Total Transaksi</div>
            <div class="stat-value">{{ \App\Models\Tabungan::count() }}</div>
            <div class="stat-foot">Setor & tarik</div>
        </div>
    </div>

    <div class="form-section" style="padding:16px;overflow-x:auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:12px;">
            <h2 style="font-size:17px;font-weight:800;color:#1c2a23;">Daftar Siswa</h2>
        </div>

        @if ($siswa->isEmpty())
            <div class="empty-state">
                <p>Belum ada siswa dengan tabungan.</p>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th class="hide-sm">Username</th>
                            <th>Email</th>
                            <th>Saldo</th>
                            <th>Setor</th>
                            <th>Tarik</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($siswa as $s)
                            @php
                                $saldo = ($s->total_setor ?? 0) - ($s->total_tarik ?? 0);
                            @endphp
                            <tr>
                                <td><strong>{{ $s->name }}</strong></td>
                                <td class="hide-sm">{{ $s->username }}</td>
                                <td>{{ $s->email }}</td>
                                <td><strong style="color:#3a6450;">{{ number_format($saldo, 0, ',', '.') }}</strong></td>
                                <td>{{ number_format($s->total_setor ?? 0, 0, ',', '.') }}</td>
                                <td>{{ number_format($s->total_tarik ?? 0, 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('tabungan.show', $s->id) }}" class="action-btn action-btn-view">Lihat</a>
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
