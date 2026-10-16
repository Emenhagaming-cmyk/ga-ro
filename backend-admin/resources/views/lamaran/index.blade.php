@extends('layouts.app')

@section('title', 'Pelamar - Career Center')
@section('page-title', 'Daftar Pelamar')

@section('content')
<div class="kop-page">
    @include('koperasi._kop-style')

    <div class="kop-header">
        <div>
            <p class="kop-kicker">Career Center</p>
            <h1 class="kop-title">Pelamar</h1>
            <p class="kop-subtitle">Lamaran yang dikirim siswa. Buka detail untuk melihat CV dan mengubah status.</p>
        </div>
        <div class="kop-header-actions">
            <a href="{{ route('career.statistik') }}" class="kop-btn kop-btn-outline">Statistik</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">✓ {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <form method="GET" action="{{ route('lamaran.index') }}" class="kop-filter-row">
        <input type="text" name="search" value="{{ request('search') }}" class="kop-input" style="flex:1;min-width:180px;" placeholder="Cari nama / NISN / email">
        <select name="lowongan" class="kop-select" style="max-width:280px;">
            <option value="">Semua lowongan</option>
            @foreach ($lowongans as $l)
                <option value="{{ $l->id }}" @selected((string) request('lowongan') === (string) $l->id)>{{ $l->title }} — {{ $l->company }}</option>
            @endforeach
        </select>
        <select name="status" class="kop-select">
            <option value="">Semua status</option>
            @foreach (['pending' => 'Diproses', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak', 'dibatalkan' => 'Dibatalkan'] as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="kop-btn kop-btn-primary">Filter</button>
        @if (request()->hasAny(['search', 'lowongan', 'status']))
            <a href="{{ route('lamaran.index') }}" class="kop-btn kop-btn-ghost">Reset</a>
        @endif
    </form>

    <div class="kop-panel">
        <div class="kop-panel-head">
            <div>
                <h2 class="kop-panel-title">Daftar Lamaran</h2>
                <p class="kop-panel-sub">
                    Menampilkan {{ $lamarans->firstItem() ?? 0 }}–{{ $lamarans->lastItem() ?? 0 }} dari {{ $lamarans->total() }} lamaran
                </p>
            </div>
        </div>

        <div class="kop-table-wrap">
            <table class="kop-table">
                <thead>
                    <tr>
                        <th scope="col">Pelamar</th>
                        <th scope="col">Lowongan</th>
                        <th scope="col" class="kop-hide-sm">Jurusan</th>
                        <th scope="col" class="kop-nowrap">Dikirim</th>
                        <th scope="col" class="kop-nowrap">Status</th>
                        <th scope="col" class="kop-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lamarans as $lamaran)
                        @php
                            $tone = match ($lamaran->status) {
                                'diterima' => 'kop-badge--diambil',
                                'ditolak' => 'kop-badge--expired',
                                'dibatalkan' => 'kop-badge--batal',
                                default => 'kop-badge--pending',
                            };
                            $label = match ($lamaran->status) {
                                'diterima' => 'Diterima',
                                'ditolak' => 'Ditolak',
                                'dibatalkan' => 'Dibatalkan',
                                default => 'Diproses',
                            };
                        @endphp
                        <tr>
                            <td style="min-width:190px;">
                                <div class="kop-user-text">
                                    <span class="kop-user-name">{{ $lamaran->nama_lengkap ?: ($lamaran->user->name ?? '-') }}</span>
                                    <span class="kop-user-sub">{{ $lamaran->email ?: ($lamaran->user->email ?? '-') }}</span>
                                </div>
                            </td>
                            <td style="min-width:180px;">
                                <div class="kop-user-text">
                                    <span class="kop-user-name">{{ $lamaran->lowongan->title ?? '-' }}</span>
                                    <span class="kop-user-sub">{{ $lamaran->lowongan->company ?? '' }}</span>
                                </div>
                            </td>
                            <td class="kop-hide-sm">{{ $lamaran->jurusan_pilihan ?: '-' }}</td>
                            <td class="kop-nowrap">{{ $lamaran->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="kop-nowrap"><span class="kop-badge {{ $tone }}">{{ $label }}</span></td>
                            <td class="kop-nowrap">
                                <div class="kop-act">
                                    <a href="{{ route('lamaran.show', $lamaran) }}" class="kop-act-btn kop-act--view">Detail</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="kop-empty">
                                    <div class="kop-empty-icon">📨</div>
                                    <h3>Belum ada lamaran</h3>
                                    <p>Lamaran siswa akan muncul di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($lamarans->hasPages())
            <div class="kop-panel-foot">
                {{ $lamarans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
