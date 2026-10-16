@extends('layouts.app')

@section('title', 'Detail Pelamar - Career Center')
@section('page-title', 'Detail Pelamar')

@section('content')
<div class="kop-page">
    @include('koperasi._kop-style')

    <div class="kop-header">
        <div>
            <p class="kop-kicker">Career Center</p>
            <h1 class="kop-title">{{ $lamaran->nama_lengkap ?: ($lamaran->user->name ?? 'Pelamar') }}</h1>
            <p class="kop-subtitle">
                Melamar <strong>{{ $lamaran->lowongan->title ?? '-' }}</strong>
                @if ($lamaran->lowongan?->company) · {{ $lamaran->lowongan->company }} @endif
            </p>
        </div>
        <div class="kop-header-actions">
            <a href="{{ route('lamaran.index') }}" class="kop-btn kop-btn-ghost">Kembali ke Daftar</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">✓ {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

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

    <div class="kop-form-layout">
        <div>
            <div class="kop-panel">
                <div class="kop-panel-head">
                    <div>
                        <h2 class="kop-panel-title">Data Pelamar</h2>
                        <p class="kop-panel-sub">Status saat ini: <span class="kop-badge {{ $tone }}">{{ $label }}</span></p>
                    </div>
                </div>
                <div class="kop-panel-body">
                    <div class="kop-info-grid">
                        <div class="kop-info-item">
                            <span class="kop-info-label">Nama Lengkap</span>
                            <span class="kop-info-value">{{ $lamaran->nama_lengkap ?: ($lamaran->user->name ?? '-') }}</span>
                        </div>
                        <div class="kop-info-item">
                            <span class="kop-info-label">NISN</span>
                            <span class="kop-info-value">{{ $lamaran->nisn ?: '-' }}</span>
                        </div>
                        <div class="kop-info-item">
                            <span class="kop-info-label">Email</span>
                            <span class="kop-info-value">{{ $lamaran->email ?: ($lamaran->user->email ?? '-') }}</span>
                        </div>
                        <div class="kop-info-item">
                            <span class="kop-info-label">Jurusan Pilihan</span>
                            <span class="kop-info-value">{{ $lamaran->jurusan_pilihan ?: '-' }}</span>
                        </div>
                        <div class="kop-info-item">
                            <span class="kop-info-label">Akun</span>
                            <span class="kop-info-value">{{ $lamaran->user->username ?? $lamaran->user->name ?? '-' }}</span>
                        </div>
                        <div class="kop-info-item">
                            <span class="kop-info-label">Dikirim</span>
                            <span class="kop-info-value">{{ $lamaran->created_at?->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>

                    <div class="kop-field">
                        <label class="kop-label">Berkas CV</label>
                        @if ($lamaran->cv_path)
                            <a href="{{ route('lamaran.cv', $lamaran) }}" class="kop-btn kop-btn-outline">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                Unduh CV
                            </a>
                        @else
                            <p class="kop-info-value" style="color:var(--kop-muted);">Pelamar tidak mengunggah CV.</p>
                        @endif
                    </div>

                    <div class="kop-field">
                        <label class="kop-label">Surat Lamaran</label>
                        <div class="kop-panel" style="margin:0;background:var(--kop-bg);">
                            <div class="kop-panel-body" style="white-space:pre-wrap;font-size:13.5px;line-height:1.7;">{{ $lamaran->cover_letter }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="kop-panel">
                <div class="kop-panel-head">
                    <h2 class="kop-panel-title">Riwayat Status</h2>
                </div>
                <div class="kop-panel-body">
                    @forelse ($lamaran->statusHistories as $history)
                        <div class="kop-rank-row">
                            <span class="kop-badge kop-badge--batal">{{ $history->status_from ?: 'baru' }} → {{ $history->status_to }}</span>
                            <span class="kop-user-sub">
                                {{ $history->created_at?->format('d/m/Y H:i') }}
                                @if ($history->actionBy) · oleh {{ $history->actionBy->name }} @endif
                            </span>
                            @if ($history->catatan)
                                <span class="kop-user-sub" style="margin-top:4px;">{{ $history->catatan }}</span>
                            @endif
                        </div>
                    @empty
                        <p class="kop-info-value" style="color:var(--kop-muted);">Belum ada perubahan status.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="kop-form-side">
            <div class="kop-panel">
                <div class="kop-panel-head">
                    <h2 class="kop-panel-title">Ubah Status</h2>
                </div>
                <div class="kop-panel-body">
                    <form action="{{ route('lamaran.status', $lamaran) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="kop-field">
                            <label class="kop-label" for="lamStatus">Status</label>
                            <select id="lamStatus" name="status" class="kop-select">
                                @foreach (['pending' => 'Diproses', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak', 'dibatalkan' => 'Dibatalkan'] as $value => $optLabel)
                                    <option value="{{ $value }}" @selected(old('status', $lamaran->status) === $value)>{{ $optLabel }}</option>
                                @endforeach
                            </select>
                            @error('status')<span class="kop-field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="kop-field">
                            <label class="kop-label" for="lamNote">Catatan <span class="opt">(opsional)</span></label>
                            <textarea id="lamNote" name="catatan_admin" rows="3" class="kop-textarea"
                                      placeholder="cth: Silakan datang interview 20 Okt, 09.00">{{ old('catatan_admin', $lamaran->catatan_admin) }}</textarea>
                            @error('catatan_admin')<span class="kop-field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="kop-form-actions">
                            <button type="submit" class="kop-btn kop-btn-primary">Simpan Status</button>
                        </div>
                    </form>

                    @if ($lamaran->catatan_admin)
                        <p class="kop-user-sub" style="margin-top:10px;">Catatan tersimpan: {{ $lamaran->catatan_admin }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
