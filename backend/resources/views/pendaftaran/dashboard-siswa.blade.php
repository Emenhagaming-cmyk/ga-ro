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
    <div class="ds-empty">
        <div class="ds-empty-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#3a6450" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
        </div>
        <h3>Belum Ada Pendaftaran</h3>
        <p>Kamu belum mengisi formulir pendaftaran. Yuk mulai sekarang!</p>
        <a href="{{ route('pendaftaran.create') }}" class="btn-ds btn-ds-primary">Daftar Sekarang</a>
    </div>
@else
    <a href="{{ frontendAuthUrl() }}" class="ds-back-btn">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
        Kembali
    </a>

    {{-- Welcome Banner --}}
    <div class="ds-banner">
        <div class="ds-banner-text">
            <p class="ds-banner-greeting">Selamat datang, {{ $pendaftaran->nama_lengkap }}! 👋</p>
            <h2 class="ds-banner-title">Semangat mengejar prestasi!</h2>
        </div>
        <img src="{{ asset('images/doodle-studying.png') }}" alt="Doodle" class="ds-banner-doodle" />
    </div>

    {{-- Stat Cards --}}
    <div class="ds-stats">
        @php
            $badge = [
                'baru' => ['text' => 'Baru', 'color' => '#b45309', 'bg' => '#fef3c7'],
                'diproses' => ['text' => 'Diproses', 'color' => '#1d4ed8', 'bg' => '#dbeafe'],
                'diterima' => ['text' => 'Diterima', 'color' => '#166534', 'bg' => '#dcfce7'],
                'ditolak' => ['text' => 'Ditolak', 'color' => '#b91c1c', 'bg' => '#fee2e2'],
            ][$pendaftaran->status] ?? ['text' => $pendaftaran->status, 'color' => '#666', 'bg' => '#eee'];
        @endphp
        <div class="ds-stat-card">
            <div class="ds-stat-icon" style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="ds-stat-info">
                <span class="ds-stat-label">Status Pendaftaran</span>
                <span class="ds-stat-value" style="color:{{ $badge['color'] }};">{{ strtoupper($badge['text']) }}</span>
            </div>
        </div>
        <div class="ds-stat-card">
            <div class="ds-stat-icon" style="background:#eef5ff;color:#1d4ed8;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            </div>
            <div class="ds-stat-info">
                <span class="ds-stat-label">Jurusan Pilihan</span>
                <span class="ds-stat-value">{{ $pendaftaran->jurusan_pilihan }}</span>
            </div>
        </div>
        <div class="ds-stat-card">
            <div class="ds-stat-icon" style="background:#f0fdf4;color:#166534;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div class="ds-stat-info">
                <span class="ds-stat-label">Tanggal Daftar</span>
                <span class="ds-stat-value">{{ $pendaftaran->created_at->format('d M Y') }}</span>
            </div>
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    {{-- Status Detail --}}
    <div class="ds-card" id="status-section">
        <div class="ds-card-header">
            <h3 class="ds-card-title">Status Pendaftaran</h3>
            <span class="ds-badge" style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};">{{ strtoupper($badge['text']) }}</span>
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

    {{-- Status Messages --}}
    @if (!$canEdit)
    <div class="ds-card" style="border-left:4px solid #3a6450;">
        <p style="font-size:14px;color:#647067;margin:0;">
            @if ($pendaftaran->status === 'baru')
                Batas waktu edit telah berakhir. Hubungi admin bila ingin mengubah data.
            @elseif ($pendaftaran->status === 'diterima')
                🎉 Selamat! Kamu diterima di jurusan <strong style="color:#166534;">{{ $pendaftaran->jurusan_pilihan }}</strong>.
            @elseif ($pendaftaran->status === 'ditolak')
                Maaf, pendaftaran Anda tidak diterima. Hubungi admin untuk info lebih lanjut.
            @else
                Formulir kamu sedang diproses admin. Pantau status secara berkala.
            @endif
        </p>
    </div>
    @endif

    {{-- Quick Actions --}}
    <div class="ds-actions">
        @if ($canEdit)
        <a href="#edit-section" class="btn-ds btn-ds-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit Formulir
        </a>
        @endif
        @if ($pendaftaran->status === 'diterima')
        <a href="{{ route('pendaftaran.bukti', $pendaftaran) }}" class="btn-ds btn-ds-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Unduh Bukti Diterima
        </a>
        @endif
    </div>

    {{-- Edit Form --}}
    @if ($canEdit)
    <div class="ds-card" id="edit-section" style="margin-top:24px;">
        <div class="ds-card-header">
            <h3 class="ds-card-title">Edit Formulir Pendaftaran</h3>
        </div>
        <p style="font-size:13px;color:#647067;margin:0 0 20px;line-height:1.7;">
            Batas waktu edit: <strong style="color:#b45309;">{{ $deadline->format('d M Y H:i') }}</strong> (sisa {{ max(0, (int) round(now()->diffInHours($deadline, false))) }} jam).
        </p>
        <form method="POST" action="{{ route('pendaftaran.update', $pendaftaran) }}">
            @csrf @method('PUT')
            <div class="ds-form-row">
                <div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $pendaftaran->nama_lengkap) }}" required></div>
                <div class="form-group"><label>NISN</label><input type="text" name="nisn" value="{{ old('nisn', $pendaftaran->nisn) }}"></div>
            </div>
            <div class="ds-form-row">
                <div class="form-group"><label>Tempat Lahir</label><input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $pendaftaran->tempat_lahir) }}" required></div>
                <div class="form-group"><label>Tanggal Lahir</label><input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', optional($pendaftaran->tanggal_lahir)->format('Y-m-d')) }}" required></div>
            </div>
            <div class="ds-form-row">
                <div class="form-group"><label>Jenis Kelamin</label><select name="jenis_kelamin" required><option value="Laki-laki" {{ $pendaftaran->jenis_kelamin === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option><option value="Perempuan" {{ $pendaftaran->jenis_kelamin === 'Perempuan' ? 'selected' : '' }}>Perempuan</option></select></div>
                <div class="form-group"><label>No. HP / WhatsApp</label><input type="tel" name="no_hp" value="{{ old('no_hp', $pendaftaran->no_hp) }}" required></div>
            </div>
            <div class="form-group"><label>Alamat Lengkap</label><textarea name="alamat" required>{{ old('alamat', $pendaftaran->alamat) }}</textarea></div>
            <hr class="section-divider">
            <div class="form-group"><label>Asal Sekolah</label><input type="text" name="asal_sekolah" value="{{ old('asal_sekolah', $pendaftaran->asal_sekolah) }}" required></div>
            <div class="form-group"><label>Jurusan Pilihan</label><select name="jurusan_pilihan" required><option value="RPL" {{ $pendaftaran->jurusan_pilihan === 'RPL' ? 'selected' : '' }}>Rekayasa Perangkat Lunak (RPL)</option></select></div>
            <hr class="section-divider">
            <div class="ds-form-row">
                <div class="form-group"><label>Nama Orang Tua</label><input type="text" name="nama_orang_tua" value="{{ old('nama_orang_tua', $pendaftaran->nama_orang_tua) }}" required></div>
                <div class="form-group"><label>No. HP Orang Tua</label><input type="tel" name="no_hp_orang_tua" value="{{ old('no_hp_orang_tua', $pendaftaran->no_hp_orang_tua) }}"></div>
            </div>
            <div style="margin-top:24px;"><button type="submit" class="btn-ds btn-ds-primary">Simpan Perubahan</button></div>
        </form>
    </div>
    @endif
@endif

<style>
.ds-back-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px; border-radius: 10px; font-weight: 700; font-size: 13px;
    color: #3a6450; background: #fff; border: 1.5px solid #dfe4dd;
    text-decoration: none; margin-bottom: 16px; transition: all 0.2s ease;
}
.ds-back-btn:hover { background: #f0f4ee; border-color: #3a6450; transform: translateX(-2px); }

.ds-banner {
    display: flex; align-items: center; justify-content: space-between;
    background: linear-gradient(135deg, #2f5b45, #3a6450);
    border-radius: 20px; padding: 32px 36px; margin-bottom: 24px;
    position: relative; overflow: hidden;
}
.ds-banner-text { position: relative; z-index: 1; color: #fff; }
.ds-banner-greeting { font-size: 14px; font-weight: 600; opacity: 0.85; margin: 0 0 4px; }
.ds-banner-title { font-size: 24px; font-weight: 800; margin: 0; letter-spacing: -0.02em; }
.ds-banner-doodle {
    height: 140px; width: auto; object-fit: contain;
    animation: ds-float 4s ease-in-out infinite;
    filter: drop-shadow(0 8px 20px rgba(0,0,0,0.15));
    position: relative; z-index: 1;
}
@keyframes ds-float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }

.ds-stats { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; margin-bottom: 24px; }
.ds-stat-card {
    display: flex; align-items: center; gap: 14px;
    background: #fff; border: 1px solid #e8ece6; border-radius: 16px;
    padding: 18px 20px; transition: box-shadow 0.2s;
}
.ds-stat-card:hover { box-shadow: 0 8px 24px rgba(35,55,42,0.08); }
.ds-stat-icon {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.ds-stat-info { display: flex; flex-direction: column; gap: 2px; }
.ds-stat-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #647067; }
.ds-stat-value { font-size: 16px; font-weight: 800; color: #1c2a23; }

.ds-card {
    background: #fff; border: 1px solid #e8ece6; border-radius: 16px;
    padding: 24px 28px; margin-bottom: 16px;
}
.ds-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.ds-card-title { font-size: 16px; font-weight: 800; color: #1c2a23; margin: 0; }
.ds-badge { padding: 6px 14px; border-radius: 999px; font-weight: 800; font-size: 12px; }

.ds-detail-grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(200px,1fr)); gap: 14px; }
.ds-detail-item {
    background: #f8faf6; border: 1px solid #eef1ec; border-radius: 12px;
    padding: 14px 16px;
}
.ds-detail-label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #647067; margin-bottom: 4px; }
.ds-detail-value { font-size: 14px; font-weight: 700; color: #1c2a23; }

.ds-actions { display: flex; gap: 10px; margin-bottom: 16px; flex-wrap: wrap; }
.btn-ds {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 22px; border-radius: 12px; font-weight: 700; font-size: 13px;
    text-decoration: none; transition: all 0.2s ease; border: none; cursor: pointer; font-family: inherit;
}
.btn-ds-primary { background: #3a6450; color: #fff; box-shadow: 0 6px 16px rgba(58,100,80,0.2); }
.btn-ds-primary:hover { background: #2f5b45; transform: translateY(-1px); }
.btn-ds-outline { background: #fff; color: #3a6450; border: 1.5px solid #dfe4dd; }
.btn-ds-outline:hover { background: #f0f4ee; border-color: #3a6450; }

.ds-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

.ds-empty {
    text-align: center; padding: 80px 24px;
}
.ds-empty-icon {
    width: 80px; height: 80px; border-radius: 20px; background: #e8f0e6;
    display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;
}
.ds-empty h3 { font-size: 20px; font-weight: 800; color: #1c2a23; margin: 0 0 8px; }
.ds-empty p { font-size: 14px; color: #647067; margin: 0 0 24px; }

@media (max-width: 768px) {
    .ds-banner { flex-direction: column; text-align: center; padding: 28px 24px; }
    .ds-banner-doodle { height: 100px; margin-top: 16px; }
    .ds-banner-title { font-size: 20px; }
    .ds-stats { grid-template-columns: 1fr; }
    .ds-form-row { grid-template-columns: 1fr; }
    .ds-detail-grid { grid-template-columns: 1fr; }
    .ds-card { padding: 20px; }
}
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
