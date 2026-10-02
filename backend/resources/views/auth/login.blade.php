@extends('layouts.auth')

@section('title', 'Masuk - SPMB SMK Bahrul Ulum')

@section('content')
<h1 class="form-title" align="center">Masuk</h1>
<p class="form-subtitle" align="center">Hai Siswa! Login Terlebih dahulu ya</p>

@if (session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

@if ($errors->any())
<div class="alert alert-error">
    @foreach ($errors->all() as $error)
        {{ $error }}<br>
    @endforeach
</div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="form-group">
        <label>Username</label>
        <div class="input-wrap">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <input type="text" name="username" value="{{ old('username') }}" placeholder="Masukkan username" required autofocus>
        </div>
    </div>

    <div class="form-group">
        <label>Password</label>
        <div class="input-wrap pw-field">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            <input type="password" name="password" placeholder="password" required>
            <button type="button" class="pw-toggle" onclick="togglePw(this)" aria-label="Tampilkan password" aria-pressed="false" title="Tampilkan password">
                <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                <svg class="eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
            </button>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Masuk</button>
</form>

<p class="auth-links" style="margin-top:16px;">
    <a href="{{ route('password.request') }}">Lupa kata sandi?</a>
</p>

<p class="auth-links">
    Belum punya akun?
    <a href="{{ route('register') }}">Daftar dulu ya!</a>
</p>
@endsection