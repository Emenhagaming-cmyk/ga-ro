@extends('layouts.auth')

@section('title', 'Masuk - SPMB SMK Bahrul Ulum')

@section('content')
<h1 class="form-title" align="center">Masuk</h1>
<p class="form-subtitle">Login Untuk Melakukan Pendaftaran dan membuka fitur yang tersedia</p>

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
        <div class="input-wrap">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Masuk</button>
</form>

<p class="auth-links" style="margin-top:16px;">
    <a href="{{ route('password.request') }}">Lupa kata sandi?</a>
</p>

<p class="auth-links">
    Belum punya akun?
    <a href="{{ route('register') }}">Daftar di sini</a>
</p>
@endsection