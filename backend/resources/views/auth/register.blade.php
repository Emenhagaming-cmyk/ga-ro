@extends('layouts.auth')

@section('title', 'Daftar - AKUN SMK Bahrul Ulum')

@section('content')
<h1 class="form-title" align="center">Buat Akun Siswa</h1>
<p class="form-subtitle" align="center">Bikin akun dulu ya ~</p>

@if ($errors->any())
<div class="alert alert-error">
    @foreach ($errors->all() as $error)
        {{ $error }}<br>
    @endforeach
</div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="form-group">
        <label>Nama Lengkap</label>
        <div class="input-wrap">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama lengkap Anda" required autofocus>
        </div>
    </div>

    <div class="form-group">
        <label>Username</label>
        <div class="input-wrap">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <input type="text" name="username" value="{{ old('username') }}" placeholder="Contoh: ahmad.rizal" required>
        </div>
    </div>

    <div class="form-group">
        <label>Email</label>
        <div class="input-wrap">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
            </svg>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required>
        </div>
    </div>

    <div class="form-group">
        <label>Password</label>
        <div class="input-wrap">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            <input type="password" name="password" placeholder="Minimal 8 karakter" required>
        </div>
    </div>

    <div class="form-group">
        <label>Konfirmasi Password</label>
        <div class="input-wrap">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            <input type="password" name="password_confirmation" placeholder="Ulangi password" required>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Daftar</button>
</form>

<p class="auth-links">
    Udah punya akun?
    <a href="{{ route('login') }}">Login di sini</a>
</p>
@endsection