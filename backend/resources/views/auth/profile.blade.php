@extends('layouts.app')

@section('title', 'Profil Siswa - SMK Bahrul Ulum')

@section('content')
<div class="form-section">
    <h1 class="form-title">Profil Siswa</h1>
    <p class="form-subtitle">Informasi akun dan data pendaftaran Anda.</p>

    <div class="profile-card">
        <div class="profile-row">
            <span class="profile-label">Nama</span>
            <span class="profile-value">{{ $user->name }}</span>
        </div>
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
    <h2 class="form-title" style="font-size:20px;margin-top:32px;">Data Pendaftaran</h2>
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
    @endif

    <p style="margin-top:20px;text-align:center;">
        <a href="/" style="color:#3a6450;font-weight:700;text-decoration:none;">&larr; Kembali ke Beranda</a>
    </p>
</div>

<style>
    .profile-card {
        background: #f8faf8;
        border: 1px solid rgba(58,100,80,0.12);
        border-radius: 14px;
        padding: 20px;
        margin-top: 16px;
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
    .badge-diterima {
        background: #d4edda;
        color: #155724;
    }
</style>
@endsection
