@extends('layouts.app')

@section('title', 'Profil Siswa - SMK Bahrul Ulum')

@section('content')
<div class="profile-wrapper">
    <a href="{{ frontendAuthUrl() }}" class="profile-back" aria-label="Kembali ke beranda">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
        Kembali
    </a>

    <div class="profile-hero">
        <div class="profile-avatar" aria-hidden="true">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
        <div class="profile-hero-text">
            <h1 class="profile-name">{{ $user->name }}</h1>
            <p class="profile-role">Akun Siswa SMK Bahrul Ulum</p>
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success" role="status" aria-live="polite">{{ session('success') }}</div>
    @endif
    @if (session('error'))
    <div class="alert alert-error" role="alert" aria-live="assertive">{{ session('error') }}</div>
    @endif

    <section class="profile-section" aria-labelledby="account-heading">
        <h2 id="account-heading" class="profile-section-title">Akun</h2>
        <div class="profile-card">
            <div class="profile-row">
                <span class="profile-label">Username</span>
                <span class="profile-value">{{ $user->username }}</span>
            </div>
            <div class="profile-row">
                <span class="profile-label">Email</span>
                <span class="profile-value">{{ $user->email }}</span>
            </div>
            <div class="profile-row">
                <span class="profile-label">Status</span>
                <span class="profile-badge profile-badge--siswa">Siswa</span>
            </div>
        </div>
    </section>

    @if($pendaftaran)
    <section class="profile-section" aria-labelledby="registration-heading">
        <h2 id="registration-heading" class="profile-section-title">Data Pendaftaran</h2>
        <div class="profile-card">
            <div class="profile-row">
                <span class="profile-label">Nama Lengkap</span>
                <span class="profile-value">{{ $pendaftaran->nama_lengkap }}</span>
            </div>
            <div class="profile-row">
                <span class="profile-label">NISN</span>
                <span class="profile-value">{{ $pendaftaran->nisn ?? '-' }}</span>
            </div>
            <div class="profile-row">
                <span class="profile-label">Asal Sekolah</span>
                <span class="profile-value">{{ $pendaftaran->asal_sekolah }}</span>
            </div>
            <div class="profile-row">
                <span class="profile-label">Jurusan</span>
                <span class="profile-value">{{ $pendaftaran->jurusan_pilihan }}</span>
            </div>
            <div class="profile-row">
                <span class="profile-label">Status Pendaftaran</span>
                <span class="profile-badge profile-badge--{{ $pendaftaran->status }}">{{ ucfirst($pendaftaran->status) }}</span>
            </div>
        </div>
        <a class="profile-btn" href="{{ route('dashboard.siswa') }}">Lihat Dashboard</a>
    </section>
    @else
    <section class="profile-section" aria-labelledby="registration-heading">
        <h2 id="registration-heading" class="profile-section-title">Data Pendaftaran</h2>
        <div class="profile-card profile-card--empty">
            <p>Belum ada data pendaftaran. Lengkapi formulir untuk mengikuti seleksi.</p>
            <a class="profile-btn" href="{{ route('pendaftaran.create') }}">Isi Formulir Pendaftaran</a>
        </div>
    </section>
    @endif

    <div class="profile-footer">
        <form action="{{ route('logout') }}" method="POST" aria-label="Formulir keluar">
            @csrf
            <button type="submit" class="profile-btn profile-btn--logout">Logout</button>
        </form>
    </div>
</div>

<style>
:root {
    --pf-color-primary: #3a6450;
    --pf-color-primary-dark: #2f5b45;
    --pf-color-text: #1c2a23;
    --pf-color-text-secondary: #647067;
    --pf-color-border: #e8ece6;
    --pf-color-bg: #f8faf6;
    --pf-radius-sm: 12px;
    --pf-radius-md: 16px;
    --pf-radius-lg: 20px;
    --pf-shadow-sm: 0 6px 16px rgba(35, 55, 42, 0.08);
    --pf-shadow-md: 0 10px 24px rgba(35, 55, 42, 0.1);
    --pf-transition: all 0.25s ease;
}

.profile-wrapper {
    max-width: 680px;
}

.profile-back {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 18px; border-radius: var(--pf-radius-sm); font-weight: 700; font-size: 13px;
    color: var(--pf-color-primary); background: #fff; border: 1.5px solid #dfe4dd;
    text-decoration: none; margin-bottom: 24px; transition: var(--pf-transition);
}
.profile-back:hover { background: #f0f4ee; border-color: var(--pf-color-primary); transform: translateX(-2px); }

.profile-hero {
    display: flex; align-items: center; gap: 20px;
    margin-bottom: 32px; padding: 28px;
    background: linear-gradient(135deg, #2f5b45 0%, #3a6450 50%, #4a7a62 100%);
    border-radius: var(--pf-radius-lg); position: relative; overflow: hidden;
}
.profile-hero::before {
    content: '';
    position: absolute; top: -40%; right: -8%; width: 260px; height: 260px;
    background: radial-gradient(circle, rgba(125, 184, 141, 0.28), transparent 70%);
    border-radius: 50%; pointer-events: none;
}
.profile-hero::after {
    content: '';
    position: absolute; bottom: -30%; left: 15%; width: 180px; height: 180px;
    background: radial-gradient(circle, rgba(255,255,255,0.1), transparent 70%);
    border-radius: 50%; pointer-events: none;
}

.profile-avatar {
    width: 72px; height: 72px; border-radius: 50%;
    background: rgba(255,255,255,0.15); color: #fff;
    font-size: 28px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    border: 2px solid rgba(255,255,255,0.25);
    flex-shrink: 0; position: relative; z-index: 1;
    backdrop-filter: blur(4px);
}

.profile-hero-text { position: relative; z-index: 1; color: #fff; min-width: 0; }
.profile-name { font-size: 22px; font-weight: 800; margin: 0; letter-spacing: -0.02em; line-height: 1.2; }
.profile-role { font-size: 13px; font-weight: 600; opacity: 0.85; margin: 4px 0 0; }

.profile-section { margin-bottom: 24px; }
.profile-section-title {
    font-size: 12px; font-weight: 800; color: var(--pf-color-primary);
    margin: 0 0 10px; text-transform: uppercase; letter-spacing: 0.08em;
}

.profile-card {
    background: #fff; border: 1px solid var(--pf-color-border); border-radius: var(--pf-radius-md);
    padding: 4px 0; overflow: hidden;
}

.profile-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 14px 20px; border-bottom: 1px solid rgba(58,100,80,0.06);
    gap: 16px;
}
.profile-row:last-child { border-bottom: none; }

.profile-label { font-size: 13px; color: var(--pf-color-text-secondary); font-weight: 600; flex-shrink: 0; }
.profile-value { font-size: 14px; color: var(--pf-color-text); font-weight: 700; text-align: right; word-break: break-word; }

.profile-badge {
    padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; flex-shrink: 0;
}
.profile-badge--siswa { background: #e8f0e6; color: #3a6450; }
.profile-badge--baru { background: #eef1f0; color: #5b6475; }
.profile-badge--diproses { background: #fff3d6; color: #8a6d1a; }
.profile-badge--diterima { background: #d4edda; color: #155724; }
.profile-badge--ditolak { background: #f8d7da; color: #842029; }

.profile-card--empty {
    text-align: center; padding: 32px 24px; color: var(--pf-color-text-secondary); font-size: 14px;
}
.profile-card--empty p { margin: 0 0 20px; line-height: 1.7; }

.profile-btn {
    display: inline-block; margin-top: 18px; padding: 12px 24px;
    background: var(--pf-color-primary); color: #fff; border-radius: var(--pf-radius-sm);
    font-weight: 700; text-decoration: none; font-size: 14px;
    box-shadow: 0 6px 16px rgba(58, 100, 80, 0.2);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    border: none; cursor: pointer;
}
.profile-btn:hover { background: var(--pf-color-primary-dark); transform: translateY(-2px); box-shadow: 0 10px 24px rgba(58, 100, 80, 0.25); color: #fff; }

.profile-btn--logout {
    background: #fff; color: #b3362c; border: 1.5px solid #e2b6b2;
    box-shadow: none;
}
.profile-btn--logout:hover { background: #fdf1f0; color: #b3362c; box-shadow: var(--pf-shadow-sm); }

.profile-footer { margin-top: 28px; padding-top: 24px; border-top: 1px solid var(--pf-color-border); }

.alert {
    padding: 14px 18px; border-radius: var(--pf-radius-sm); margin-bottom: 20px; font-size: 13px; font-weight: 600; border: 1px solid;
}
.alert-success { background: #e8f0e6; color: #2a5238; border-color: rgba(58, 100, 80, 0.2); }
.alert-error { background: #fef2f2; color: #991b1b; border-color: rgba(153, 27, 27, 0.15); }

@media (max-width: 520px) {
    .profile-hero { flex-direction: column; text-align: center; padding: 24px; }
    .profile-name { font-size: 20px; }
    .profile-avatar { width: 64px; height: 64px; font-size: 24px; }
    .profile-row { flex-direction: column; align-items: flex-start; gap: 4px; padding: 14px 18px; }
    .profile-value { text-align: left; }
    .profile-btn { width: 100%; text-align: center; }
}
</style>
@endsection
