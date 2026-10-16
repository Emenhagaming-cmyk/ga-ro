@extends('layouts.app')

@section('title', 'Statistik Career Center - Panel Admin')
@section('page-title', 'Statistik Career Center')

@section('content')
<div class="kop-page">
    @include('koperasi._kop-style')

    <div class="kop-header">
        <div>
            <p class="kop-kicker">Career Center</p>
            <h1 class="kop-title">Statistik</h1>
            <p class="kop-subtitle">Rekap lowongan dan lamaran seluruh siswa.</p>
        </div>
        <div class="kop-header-actions">
            <a href="{{ route('lowongan.index') }}" class="kop-btn kop-btn-outline">Kelola Lowongan</a>
            <a href="{{ route('lamaran.index') }}" class="kop-btn kop-btn-primary">Daftar Pelamar</a>
        </div>
    </div>

    @php
        $maxStatus = max(array_merge([1], $perStatus->values()->all()));
        $maxTipe = max(array_merge([1], $perTipe->values()->all()));
        $maxJurusan = max(array_merge([1], $perJurusan->values()->all()));
        $maxTop = max(array_merge([1], $topLowongan->pluck('lamarans_count')->all()));
        $maxTren = max(array_merge([1], array_column($tren, 'total')));
        $statusLabels = ['pending' => 'Diproses', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak', 'dibatalkan' => 'Dibatalkan'];
        $bulanShort = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
    @endphp

    <div class="kop-stats">
        <div class="kop-stat">
            <div class="kop-stat-icon" style="background:#e8f0e6;color:#3a6450;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
            </div>
            <div class="kop-stat-info">
                <span class="kop-stat-label">Total Lowongan</span>
                <span class="kop-stat-value">{{ $totalLowongan }}</span>
                <span class="kop-stat-foot">{{ $lowonganAktif }} aktif</span>
            </div>
        </div>
        <div class="kop-stat">
            <div class="kop-stat-icon" style="background:#dcfce7;color:#166534;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
            </div>
            <div class="kop-stat-info">
                <span class="kop-stat-label">Lowongan Aktif</span>
                <span class="kop-stat-value">{{ $lowonganAktif }}</span>
                <span class="kop-stat-foot">Tampil ke siswa</span>
            </div>
        </div>
        <div class="kop-stat">
            <div class="kop-stat-icon" style="background:#dbeafe;color:#1d4ed8;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <div class="kop-stat-info">
                <span class="kop-stat-label">Total Lamaran</span>
                <span class="kop-stat-value">{{ $totalLamaran }}</span>
                <span class="kop-stat-foot">Semua siswa</span>
            </div>
        </div>
        <div class="kop-stat">
            <div class="kop-stat-icon" style="background:#fef3c7;color:#b45309;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="kop-stat-info">
                <span class="kop-stat-label">Diterima</span>
                <span class="kop-stat-value">{{ (int) ($perStatus['diterima'] ?? 0) }}</span>
                <span class="kop-stat-foot">{{ (int) ($perStatus['pending'] ?? 0) }} masih diproses</span>
            </div>
        </div>
    </div>

    <div class="kop-two-col">
        <div class="kop-panel">
            <div class="kop-panel-head">
                <div>
                    <h2 class="kop-panel-title">Lamaran per Status</h2>
                    <p class="kop-panel-sub">Seluruh pelamar</p>
                </div>
            </div>
            <div class="kop-panel-body">
                @foreach ($statusLabels as $key => $label)
                    <div style="margin-bottom:12px;">
                        <div class="kop-rank-row" style="justify-content:space-between;">
                            <span class="kop-user-name">{{ $label }}</span>
                            <span class="kop-total">{{ (int) ($perStatus[$key] ?? 0) }}</span>
                        </div>
                        <div class="kop-bar"><i style="width: {{ round(((int) ($perStatus[$key] ?? 0)) / $maxStatus * 100) }}%"></i></div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="kop-panel">
            <div class="kop-panel-head">
                <div>
                    <h2 class="kop-panel-title">Lowongan per Tipe</h2>
                    <p class="kop-panel-sub">Magang, kerja, BKK</p>
                </div>
            </div>
            <div class="kop-panel-body">
                @forelse ($perTipe as $tipe => $jumlah)
                    <div style="margin-bottom:12px;">
                        <div class="kop-rank-row" style="justify-content:space-between;">
                            <span class="kop-user-name">{{ $tipe ?: 'Lainnya' }}</span>
                            <span class="kop-total">{{ (int) $jumlah }}</span>
                        </div>
                        <div class="kop-bar bar-blue"><i style="width: {{ round(((int) $jumlah) / $maxTipe * 100) }}%"></i></div>
                    </div>
                @empty
                    <p class="kop-info-value" style="color:var(--kop-muted);">Belum ada data.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="kop-two-col">
        <div class="kop-panel">
            <div class="kop-panel-head">
                <div>
                    <h2 class="kop-panel-title">Lowongan Paling Diminati</h2>
                    <p class="kop-panel-sub">10 teratas berdasarkan jumlah pelamar</p>
                </div>
            </div>
            <div class="kop-panel-body">
                @forelse ($topLowongan as $item)
                    <div style="margin-bottom:12px;">
                        <div class="kop-rank-row">
                            <span class="kop-rank {{ $loop->iteration <= 3 ? 'top-' . $loop->iteration : '' }}">{{ $loop->iteration }}</span>
                            <div style="flex:1;min-width:0;">
                                <span class="kop-user-name">{{ $item->title }}</span>
                                <span class="kop-user-sub">{{ $item->company }}</span>
                                <div class="kop-bar"><i style="width: {{ round($item->lamarans_count / $maxTop * 100) }}%"></i></div>
                            </div>
                            <span class="kop-total">{{ $item->lamarans_count }}</span>
                        </div>
                    </div>
                @empty
                    <p class="kop-info-value" style="color:var(--kop-muted);">Belum ada lowongan.</p>
                @endforelse
            </div>
        </div>

        <div class="kop-panel">
            <div class="kop-panel-head">
                <div>
                    <h2 class="kop-panel-title">Lowongan per Jurusan</h2>
                    <p class="kop-panel-sub">Berdasarkan data lowongan</p>
                </div>
            </div>
            <div class="kop-panel-body">
                @forelse ($perJurusan as $jurusan => $jumlah)
                    <div style="margin-bottom:12px;">
                        <div class="kop-rank-row" style="justify-content:space-between;">
                            <span class="kop-user-name">{{ $jurusan ?: 'Lainnya' }}</span>
                            <span class="kop-total">{{ (int) $jumlah }}</span>
                        </div>
                        <div class="kop-bar"><i style="width: {{ round(((int) $jumlah) / $maxJurusan * 100) }}%"></i></div>
                    </div>
                @empty
                    <p class="kop-info-value" style="color:var(--kop-muted);">Belum ada data.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="kop-panel">
        <div class="kop-panel-head">
            <div>
                <h2 class="kop-panel-title">Lamaran 6 Bulan Terakhir</h2>
                <p class="kop-panel-sub">Jumlah lamaran masuk per bulan</p>
            </div>
        </div>
        <div class="kop-panel-body">
            @foreach ($tren as $row)
                @php
                    [$y, $m] = explode('-', $row['bulan']);
                @endphp
                <div style="margin-bottom:12px;">
                    <div class="kop-rank-row" style="justify-content:space-between;">
                        <span class="kop-user-name">{{ ($bulanShort[(int) $m] ?? $m) . ' ' . substr($y, 2) }}</span>
                        <span class="kop-total">{{ $row['total'] }}</span>
                    </div>
                    <div class="kop-bar bar-blue"><i style="width: {{ round($row['total'] / $maxTren * 100) }}%"></i></div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
