@extends('layouts.app')

@section('title', 'Dashboard Siswa - SMK Bahrul Ulum')

@section('sidebar')
@php
    $hasData = !empty($pendaftaran);
    if ($hasData) {
        $isPreview = $pendaftaran->user_id !== Auth::id();
        $deadline = $pendaftaran->created_at->copy()->addDays(3);
        $canEdit = !$isPreview && $pendaftaran->status === 'baru' && now()->lt($deadline);
    }
@endphp
<a href="{{ route('dashboard.siswa') }}" class="active">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>
    Dashboard
</a>
<a href="#status-section">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
    Status Pendaftaran
</a>
@if ($hasData && $canEdit)
<a href="#edit-section">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
    Edit Formulir
</a>
@endif
@endsection

@section('content')
@if (!$hasData)
    <div class="ds-empty" role="status" aria-label="Belum ada data pendaftaran">
        <div class="ds-empty-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#3a6450" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
        </div>
        <h3>Belum Ada Pendaftaran</h3>
        <p>Kamu belum mengisi formulir pendaftaran. Yuk mulai sekarang!</p>
        <a href="{{ route('pendaftaran.create') }}" class="btn-ds btn-ds-primary">Daftar Sekarang</a>
    </div>
@else
    <a href="{{ frontendAuthUrl() }}" class="ds-back-btn" aria-label="Kembali ke beranda">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
        Kembali
    </a>

    @if (session('success'))
    <div class="alert alert-success" role="status" aria-live="polite">{{ session('success') }}</div>
    @endif
    @if (session('error'))
    <div class="alert alert-error" role="alert" aria-live="assertive">{{ session('error') }}</div>
    @endif

    @php
        $badge = [
            'baru' => ['text' => 'Baru', 'color' => '#b45309', 'bg' => '#fef3c7'],
            'diproses' => ['text' => 'Diproses', 'color' => '#1d4ed8', 'bg' => '#dbeafe'],
            'diterima' => ['text' => 'Diterima', 'color' => '#166534', 'bg' => '#dcfce7'],
            'ditolak' => ['text' => 'Ditolak', 'color' => '#b91c1c', 'bg' => '#fee2e2'],
        ][$pendaftaran->status] ?? ['text' => $pendaftaran->status, 'color' => '#666', 'bg' => '#eee'];
        $announceIcons = [
            'baru' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
            'diproses' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
            'diterima' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
            'ditolak' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
        ];
        $announceEyebrow = [
            'baru' => 'Informasi Penting',
            'diproses' => 'Status Pendaftaran',
            'diterima' => 'Pengumuman Hasil Seleksi',
            'ditolak' => 'Pengumuman Hasil Seleksi',
        ][$pendaftaran->status] ?? 'Status Pendaftaran';
    @endphp

    <div class="ds-banner">
        <div class="ds-banner-text">
            <p class="ds-banner-greeting">Selamat datang, {{ $pendaftaran->nama_lengkap }}! 👋</p>
            <h1 class="ds-banner-title">Semangat mengejar prestasi!</h1>
            <p class="ds-banner-subtitle">Pantau status pendaftaran dan kelola formulir kamu di sini.</p>
        </div>
        <div class="ds-banner-doodle-wrap">
            <img src="{{ asset('images/doodle-studying.png') }}" alt="" class="ds-banner-doodle" aria-hidden="true" />
        </div>
    </div>

    @if (!$canEdit)
    <div class="ds-announce ds-announce--{{ $pendaftaran->status }}" role="status" aria-live="polite">
        <div class="ds-announce-icon">{!! $announceIcons[$pendaftaran->status] ?? '' !!}</div>
        <div class="ds-announce-body">
            <p class="ds-announce-eyebrow">{{ $announceEyebrow }}</p>
            <p class="ds-announce-text">
                @if ($pendaftaran->status === 'baru')
                    Batas waktu edit telah berakhir. Hubungi admin bila ingin mengubah data.
                @elseif ($pendaftaran->status === 'diterima')
                    Selamat kamu resmi jadi bagian dari SMK Bahrul Ulum dan Kamu diterima di jurusan <strong>{{ $pendaftaran->jurusan_pilihan }} !</strong>
                @elseif ($pendaftaran->status === 'ditolak')
                    Maaf, pendaftaran Anda tidak diterima. Hubungi admin untuk info lebih lanjut.
                @else
                    Formulir kamu sedang diproses admin. Pantau status secara berkala.
                @endif
            </p>
            @if ($pendaftaran->status === 'diterima')
            <a href="{{ route('pendaftaran.bukti', $pendaftaran) }}" class="btn-ds btn-ds-primary ds-announce-cta">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Unduh Bukti Diterima
            </a>
            @endif
        </div>
    </div>
    @endif

    <div class="ds-stats">
        <div class="ds-stat-card">
            <div class="ds-stat-icon ds-stat-icon--{{ $pendaftaran->status }}" style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="ds-stat-info">
                <span class="ds-stat-label">Status Pendaftaran</span>
                <span class="ds-stat-value" style="color:{{ $badge['color'] }};">{{ strtoupper($badge['text']) }}</span>
            </div>
        </div>
        <div class="ds-stat-card">
            <div class="ds-stat-icon ds-stat-icon--jurusan" style="background:#eef5ff;color:#1d4ed8;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            </div>
            <div class="ds-stat-info">
                <span class="ds-stat-label">Jurusan Pilihan</span>
                <span class="ds-stat-value">{{ $pendaftaran->jurusan_pilihan }}</span>
            </div>
        </div>
        <div class="ds-stat-card">
            <div class="ds-stat-icon ds-stat-icon--tanggal" style="background:#f0fdf4;color:#166534;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div class="ds-stat-info">
                <span class="ds-stat-label">Tanggal Daftar</span>
                <span class="ds-stat-value">{{ $pendaftaran->created_at->format('d M Y') }}</span>
            </div>
        </div>
    </div>

    <div class="ds-card" id="status-section">
        <div class="ds-card-header">
            <h2 class="ds-card-title">Status Pendaftaran</h2>
            <span class="ds-badge ds-badge--{{ $pendaftaran->status }}" style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};">{{ strtoupper($badge['text']) }}</span>
        </div>
        <div class="ds-detail-grid">
            <div class="ds-detail-item">
                <span class="ds-detail-label">Nama Lengkap</span>
                <span class="ds-detail-value">{{ $pendaftaran->nama_lengkap }}</span>
            </div>
            <div class="ds-detail-item">
                <span class="ds-detail-label">NISN</span>
                <span class="ds-detail-value">{{ $pendaftaran->nisn ?? '-' }}</span>
            </div>
            <div class="ds-detail-item">
                <span class="ds-detail-label">Asal Sekolah</span>
                <span class="ds-detail-value">{{ $pendaftaran->asal_sekolah }}</span>
            </div>
            <div class="ds-detail-item">
                <span class="ds-detail-label">Jurusan Pilihan</span>
                <span class="ds-detail-value">{{ $pendaftaran->jurusan_pilihan }}</span>
            </div>
            <div class="ds-detail-item">
                <span class="ds-detail-label">Tanggal Daftar</span>
                <span class="ds-detail-value">{{ $pendaftaran->created_at->format('d M Y') }}</span>
            </div>
            @if ($pendaftaran->status_updated_at)
            <div class="ds-detail-item">
                <span class="ds-detail-label">Status Diperbarui</span>
                <span class="ds-detail-value">{{ \Carbon\Carbon::parse($pendaftaran->status_updated_at)->format('d M Y H:i') }}</span>
            </div>
            @endif
        </div>
    </div>

    @if ($canEdit)
    <div class="ds-actions">
        <a href="#edit-section" class="btn-ds btn-ds-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit Formulir
        </a>
    </div>
    @endif

    @if ($canEdit)
    <div class="ds-card ds-card--edit" id="edit-section">
        <div class="ds-card-header">
            <h2 class="ds-card-title">Edit Formulir Pendaftaran</h2>
        </div>
        <p class="ds-deadline-text">
            Batas waktu edit: <strong class="ds-deadline-highlight">{{ $deadline->format('d M Y H:i') }}</strong> (sisa {{ max(0, (int) round(now()->diffInHours($deadline, false))) }} jam).
        </p>
        <form method="POST" action="{{ route('pendaftaran.update', $pendaftaran) }}" novalidate>
            @csrf @method('PUT')
            <div class="ds-form-row">
                <div class="form-group">
                    <label for="edit-nama-lengkap">Nama Lengkap</label>
                    <input type="text" id="edit-nama-lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $pendaftaran->nama_lengkap) }}" required>
                </div>
                <div class="form-group">
                    <label for="edit-nisn">NISN</label>
                    <input type="text" id="edit-nisn" name="nisn" value="{{ old('nisn', $pendaftaran->nisn) }}">
                </div>
            </div>
            <div class="ds-form-row">
                <div class="form-group">
                    <label for="edit-tempat-lahir">Tempat Lahir</label>
                    <input type="text" id="edit-tempat-lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $pendaftaran->tempat_lahir) }}" required>
                </div>
                <div class="form-group">
                    <label for="edit-tanggal-lahir">Tanggal Lahir</label>
                    <input type="date" id="edit-tanggal-lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', optional($pendaftaran->tanggal_lahir)->format('Y-m-d')) }}" required>
                </div>
            </div>
            <div class="ds-form-row">
                <div class="form-group">
                    <label for="edit-jenis-kelamin">Jenis Kelamin</label>
                    <select id="edit-jenis-kelamin" name="jenis_kelamin" required>
                        <option value="Laki-laki" {{ $pendaftaran->jenis_kelamin === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ $pendaftaran->jenis_kelamin === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit-no-hp">No. HP / WhatsApp</label>
                    <input type="tel" id="edit-no-hp" name="no_hp" value="{{ old('no_hp', $pendaftaran->no_hp) }}" required>
                </div>
            </div>
            <div class="form-group">
                <label for="edit-alamat">Alamat Lengkap</label>
                <textarea id="edit-alamat" name="alamat" required>{{ old('alamat', $pendaftaran->alamat) }}</textarea>
            </div>
            <hr class="section-divider" aria-hidden="true">
            <div class="form-group">
                <label for="edit-asal-sekolah">Asal Sekolah</label>
                <input type="text" id="edit-asal-sekolah" name="asal_sekolah" value="{{ old('asal_sekolah', $pendaftaran->asal_sekolah) }}" required>
            </div>
            <div class="form-group">
                <label for="edit-jurusan-pilihan">Jurusan Pilihan</label>
                <select id="edit-jurusan-pilihan" name="jurusan_pilihan" required>
                    <option value="RPL" {{ $pendaftaran->jurusan_pilihan === 'RPL' ? 'selected' : '' }}>Rekayasa Perangkat Lunak (RPL)</option>
                </select>
            </div>
            <hr class="section-divider" aria-hidden="true">
            <div class="ds-form-row">
                <div class="form-group">
                    <label for="edit-nama-orangtua">Nama Orang Tua</label>
                    <input type="text" id="edit-nama-orangtua" name="nama_orang_tua" value="{{ old('nama_orang_tua', $pendaftaran->nama_orang_tua) }}" required>
                </div>
                <div class="form-group">
                    <label for="edit-no-hp-orangtua">No. HP Orang Tua</label>
                    <input type="tel" id="edit-no-hp-orangtua" name="no_hp_orang_tua" value="{{ old('no_hp_orang_tua', $pendaftaran->no_hp_orang_tua) }}">
                </div>
            </div>
            <div class="ds-form-actions">
                <button type="submit" class="btn-ds btn-ds-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
    @endif
@endif

<style>
:root {
    --ds-color-primary: #3a6450;
    --ds-color-primary-dark: #2f5b45;
    --ds-color-text: #1c2a23;
    --ds-color-text-secondary: #647067;
    --ds-color-border: #e8ece6;
    --ds-color-bg: #f8faf6;
    --ds-radius-sm: 10px;
    --ds-radius-md: 14px;
    --ds-radius-lg: 20px;
    --ds-shadow-sm: 0 6px 16px rgba(35, 55, 42, 0.08);
    --ds-shadow-md: 0 10px 24px rgba(35, 55, 42, 0.1);
    --ds-transition: all 0.25s ease;
}

.ds-back-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 18px; border-radius: var(--ds-radius-sm); font-weight: 700; font-size: 13px;
    color: var(--ds-color-primary); background: #fff; border: 1.5px solid #dfe4dd;
    text-decoration: none; margin-bottom: 20px; transition: var(--ds-transition);
}
.ds-back-btn:hover { background: #f0f4ee; border-color: var(--ds-color-primary); transform: translateX(-2px); }

.ds-banner {
    display: flex; align-items: center; justify-content: space-between;
    background: linear-gradient(135deg, #2f5b45 0%, #3a6450 50%, #4a7a62 100%);
    border-radius: var(--ds-radius-lg); padding: 36px 40px; margin-bottom: 24px;
    position: relative; overflow: hidden;
}
.ds-banner::before {
    content: '';
    position: absolute; top: -50%; right: -10%; width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(125, 184, 141, 0.25), transparent 70%);
    border-radius: 50%; pointer-events: none;
}
.ds-banner::after {
    content: '';
    position: absolute; bottom: -30%; left: 20%; width: 200px; height: 200px;
    background: radial-gradient(circle, rgba(255,255,255,0.08), transparent 70%);
    border-radius: 50%; pointer-events: none;
}
.ds-banner-text { position: relative; z-index: 1; color: #fff; flex: 1; min-width: 0; }
.ds-banner-greeting { font-size: 14px; font-weight: 600; opacity: 0.85; margin: 0 0 6px; }
.ds-banner-title { font-size: 26px; font-weight: 800; margin: 0; letter-spacing: -0.02em; line-height: 1.2; }
.ds-banner-subtitle { font-size: 14px; font-weight: 500; opacity: 0.8; margin: 8px 0 0; line-height: 1.5; }
.ds-banner-doodle-wrap {
    position: relative; z-index: 1; flex-shrink: 0; margin-left: 24px;
    width: 176px; height: 176px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    background: radial-gradient(circle at 35% 30%, #ffffff 0%, #f1f7f0 72%);
    box-shadow: 0 14px 30px rgba(11, 30, 20, 0.30), 0 0 0 8px rgba(255, 255, 255, 0.14);
    animation: ds-float 4s ease-in-out infinite;
}
.ds-banner-doodle {
    height: 132px; width: auto; object-fit: contain;
    filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.12));
}
@keyframes ds-float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }

.ds-stats { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; margin-bottom: 24px; }
.ds-stat-card {
    display: flex; align-items: center; gap: 16px;
    background: #fff; border: 1px solid var(--ds-color-border); border-radius: var(--ds-radius-md);
    padding: 20px 22px; transition: var(--ds-transition); position: relative; overflow: hidden;
}
.ds-stat-card::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: var(--ds-color-primary); opacity: 0; transition: var(--ds-transition);
}
.ds-stat-card:hover { box-shadow: var(--ds-shadow-sm); transform: translateY(-2px); }
.ds-stat-card:hover::before { opacity: 1; }
.ds-stat-icon {
    width: 48px; height: 48px; border-radius: var(--ds-radius-sm);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    transition: var(--ds-transition);
}
.ds-stat-info { display: flex; flex-direction: column; gap: 3px; min-width: 0; }
.ds-stat-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--ds-color-text-secondary); }
.ds-stat-value { font-size: 15px; font-weight: 800; color: var(--ds-color-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.ds-card {
    background: #fff; border: 1px solid var(--ds-color-border); border-radius: var(--ds-radius-lg);
    padding: 28px 32px; margin-bottom: 16px; transition: var(--ds-transition);
}
.ds-card--edit { margin-top: 24px; }
.ds-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
.ds-card-title { font-size: 17px; font-weight: 800; color: var(--ds-color-text); margin: 0; letter-spacing: -0.01em; }
.ds-badge { padding: 6px 14px; border-radius: 999px; font-weight: 800; font-size: 12px; letter-spacing: 0.02em; }

.ds-announce {
    display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0;
    border-radius: var(--ds-radius-lg); border: 1px solid; border-left-width: 6px;
    padding: 28px 28px; margin-bottom: 24px;
}
.ds-announce--diterima { background: #f0fdf4; border-color: #d1ecd6; border-left-color: #166534; }
.ds-announce--ditolak { background: #fef2f2; border-color: #f5d6d6; border-left-color: #b91c1c; }
.ds-announce--diproses { background: #eff6ff; border-color: #d3e2fb; border-left-color: #1d4ed8; }
.ds-announce--baru { background: #fffbeb; border-color: #f3e4bd; border-left-color: #b45309; }
.ds-announce-icon {
    width: 48px; height: 48px; border-radius: 12px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 14px;
}
.ds-announce--diterima .ds-announce-icon { background: #dcfce7; color: #166534; }
.ds-announce--ditolak .ds-announce-icon { background: #fee2e2; color: #b91c1c; }
.ds-announce--diproses .ds-announce-icon { background: #dbeafe; color: #1d4ed8; }
.ds-announce--baru .ds-announce-icon { background: #fef3c7; color: #b45309; }
.ds-announce-body { flex: 1; min-width: 0; display: flex; flex-direction: column; align-items: center; }
.ds-announce-eyebrow {
    font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
    margin: 0 0 6px;
}
.ds-announce--diterima .ds-announce-eyebrow { color: #166534; }
.ds-announce--ditolak .ds-announce-eyebrow { color: #b91c1c; }
.ds-announce--diproses .ds-announce-eyebrow { color: #1d4ed8; }
.ds-announce--baru .ds-announce-eyebrow { color: #b45309; }
.ds-announce-text { font-size: 14px; line-height: 1.7; color: var(--ds-color-text); margin: 0; }
.ds-announce-text strong { font-weight: 800; }
.ds-announce-cta { margin-top: 16px; }

.ds-detail-grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); gap: 12px; }
.ds-detail-item {
    background: var(--ds-color-bg); border: 1px solid #eef1ec; border-radius: var(--ds-radius-sm);
    padding: 16px 18px; transition: var(--ds-transition);
}
.ds-detail-item:hover { border-color: #dde4da; box-shadow: 0 2px 8px rgba(35,55,42,0.04); }
.ds-detail-label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--ds-color-text-secondary); margin-bottom: 5px; }
.ds-detail-value { font-size: 14px; font-weight: 700; color: var(--ds-color-text); word-break: break-word; }

.ds-actions { display: flex; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
.btn-ds {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 24px; border-radius: var(--ds-radius-sm); font-weight: 700; font-size: 13px;
    text-decoration: none; transition: var(--ds-transition); border: none; cursor: pointer; font-family: inherit;
    white-space: nowrap;
}
.btn-ds-primary { background: var(--ds-color-primary); color: #fff; box-shadow: 0 6px 16px rgba(58,100,80,0.2); }
.btn-ds-primary:hover { background: var(--ds-color-primary-dark); transform: translateY(-1px); box-shadow: 0 8px 20px rgba(58,100,80,0.25); }
.btn-ds-outline { background: #fff; color: var(--ds-color-primary); border: 1.5px solid #dfe4dd; }
.btn-ds-outline:hover { background: #f0f4ee; border-color: var(--ds-color-primary); }

.ds-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.ds-form-actions { margin-top: 24px; }

.ds-deadline-text { font-size: 13px; color: var(--ds-color-text-secondary); margin: 0 0 20px; line-height: 1.7; }
.ds-deadline-highlight { color: #b45309; }

.ds-empty {
    text-align: center; padding: 80px 24px;
}
.ds-empty-icon {
    width: 80px; height: 80px; border-radius: var(--ds-radius-lg); background: #e8f0e6;
    display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;
}
.ds-empty h3 { font-size: 20px; font-weight: 800; color: var(--ds-color-text); margin: 0 0 8px; }
.ds-empty p { font-size: 14px; color: var(--ds-color-text-secondary); margin: 0 0 24px; max-width: 400px; margin-left: auto; margin-right: auto; }

@media (max-width: 768px) {
    .ds-banner { flex-direction: column; text-align: center; padding: 28px 24px; }
    .ds-banner-doodle-wrap { width: 140px; height: 140px; margin-top: 20px; margin-left: 0; }
    .ds-banner-doodle { height: 104px; }
    .ds-banner-title { font-size: 22px; }
    .ds-stats { grid-template-columns: 1fr; }
    .ds-form-row { grid-template-columns: 1fr; }
    .ds-detail-grid { grid-template-columns: 1fr; }
    .ds-card { padding: 22px 20px; }
    .ds-card-header { flex-direction: column; align-items: flex-start; }
}

@media (max-width: 480px) {
    .ds-banner { padding: 24px 20px; }
    .ds-banner-title { font-size: 20px; }
    .ds-stat-card { padding: 16px 18px; }
    .ds-stat-icon { width: 42px; height: 42px; }
    .btn-ds { width: 100%; justify-content: center; }
    .ds-actions { flex-direction: column; }
}

.alert {
    padding: 14px 18px; border-radius: var(--ds-radius-sm); margin-bottom: 20px; font-size: 13px; font-weight: 600; border: 1px solid;
}
.alert-success { background: #e8f0e6; color: #2a5238; border-color: rgba(58, 100, 80, 0.2); }
.alert-error { background: #fef2f2; color: #991b1b; border-color: rgba(153, 27, 27, 0.15); }

.section-divider { border: none; border-top: 1px solid var(--ds-color-border); margin: 24px 0; }

.form-group { margin-bottom: 18px; }
.form-group label { display: block; margin-bottom: 6px; font-weight: 700; color: var(--ds-color-primary); font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; }
.form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px 14px; border: 1.5px solid #dfe4dd; border-radius: var(--ds-radius-sm); font-family: inherit; font-size: 13px; color: var(--ds-color-text); background: #ffffff; transition: var(--ds-transition); }
.form-group select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23647067' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 14px center; padding-right: 36px; }
.form-group input::placeholder, .form-group textarea::placeholder { color: #a3a8a4; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: var(--ds-color-primary); box-shadow: 0 0 0 3px rgba(58, 100, 80, 0.1); }
.form-group textarea { resize: vertical; min-height: 70px; }
</style>

@if ($hasData)
<script>
    (function () {
        var initialStatus = {{ Js::from($pendaftaran->status) }};
        var url = {{ Js::from(route('dashboard.siswa.snapshot')) }};
        async function poll() {
            try {
                var res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
                if (!res.ok) return;
                var data = await res.json();
                if (data.status && data.status !== initialStatus) {
                    location.reload();
                }
            } catch (e) {}
        }
        setInterval(poll, 15000);
        poll();
    })();
</script>
@endif
@endsection
