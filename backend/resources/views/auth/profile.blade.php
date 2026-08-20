@extends('layouts.app')

@section('title', 'Profil Siswa - SMK Bahrul Ulum')

@section('content')
<div class="form-section">
    <div class="profile-hero">
        <div class="avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
        <div>
            <h1 class="form-title" style="margin-bottom:2px;">{{ $user->name }}</h1>
            <p class="form-subtitle" style="margin-bottom:0;">Akun Siswa SMK Bahrul Ulum</p>
        </div>
    </div>

    <h2 class="profile-heading">Akun</h2>
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
            <span class="profile-badge badge-siswa">Siswa</span>
        </div>
    </div>

    @if($pendaftaran)
    <h2 class="profile-heading">Data Pendaftaran</h2>
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
            <span class="profile-badge badge-{{ $pendaftaran->status }}">{{ ucfirst($pendaftaran->status) }}</span>
        </div>
    </div>
    <a class="profile-btn" href="{{ route('dashboard.siswa') }}">Lihat Dashboard</a>
    @else
    <h2 class="profile-heading">Data Pendaftaran</h2>
    <div class="profile-card profile-card-empty">
        <p>Belum ada data pendaftaran. Lengkapi formulir untuk mengikuti seleksi.</p>
        <a class="profile-btn" href="{{ route('pendaftaran.create') }}">Isi Formulir Pendaftaran</a>
    </div>
    @endif

    <div style="margin-top:20px;text-align:center;">
        <form action="{{ route('logout') }}" method="POST" style="margin:0;">
            @csrf
            <button type="submit" class="profile-btn profile-btn-logout">Logout</button>
        </form>
    </div>
</div>

<style>
    .profile-hero {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 8px;
    }

    .avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2f5b45 0%, #3a6450 100%);
        color: #fff;
        font-size: 26px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 22px rgba(58, 100, 80, 0.25);
        flex-shrink: 0;
    }

    .profile-heading {
        font-size: 13px;
        font-weight: 800;
        color: #3a6450;
        margin: 24px 0 0;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .profile-card {
        background: #f8faf8;
        border: 1px solid rgba(58,100,80,0.12);
        border-radius: 14px;
        padding: 20px;
        margin-top: 12px;
    }

    .profile-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid rgba(58,100,80,0.06);
    }

    .profile-row:last-child {
        border-bottom: none;
    }

    .profile-label {
        font-size: 13px;
        color: #647067;
        font-weight: 600;
    }

    .profile-value {
        font-size: 14px;
        color: #1c2a23;
        font-weight: 700;
        text-align: right;
    }

    .profile-badge {
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .badge-siswa {
        background: #e8f0e6;
        color: #3a6450;
    }

    .badge-baru {
        background: #eef1f0;
        color: #5b6475;
    }

    .badge-diproses {
        background: #fff3d6;
        color: #8a6d1a;
    }

    .badge-diterima {
        background: #d4edda;
        color: #155724;
    }

    .badge-ditolak {
        background: #f8d7da;
        color: #842029;
    }

    .profile-btn {
        display: inline-block;
        margin-top: 16px;
        padding: 12px 24px;
        background: #3a6450;
        color: #fff;
        border-radius: 14px;
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 10px 24px rgba(58, 100, 80, 0.2);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .profile-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 30px rgba(58, 100, 80, 0.28);
        color: #fff;
    }

    .profile-btn-logout {
        background: #fff;
        color: #b3362c;
        border: 1.5px solid #e2b6b2;
        box-shadow: none;
        margin-top: 0;
    }

    .profile-btn-logout:hover {
        background: #fdf1f0;
        color: #b3362c;
        box-shadow: 0 10px 24px rgba(179, 54, 44, 0.12);
    }

    .profile-card-empty {
        text-align: center;
        color: #647067;
        font-size: 14px;
    }

    .profile-card-empty p {
        margin-bottom: 4px;
    }

    @media (max-width: 520px) {
        .profile-hero {
            flex-direction: column;
            text-align: center;
        }
    }
</style>
@endsection