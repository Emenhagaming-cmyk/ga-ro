@extends('layouts.auth')

@section('title', 'Reset Kata Sandi - SPMB SMK Bahrul Ulum')

@section('content')
    <h1 class="form-title">Reset Kata Sandi</h1>
    <p class="form-subtitle">Masukkan email terdaftar dan kata sandi baru kamu ya.</p>

    @if ($errors->any())
    <div class="alert alert-error">
        @foreach ($errors->all() as $error)
            {{ $error }}<br>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
            <label>Email terdaftar</label>
            <div class="input-wrap">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
            </div>
        </div>

        <div class="form-group">
            <label>Kata Sandi Baru</label>
            <div class="input-wrap">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                <input type="password" name="password" placeholder="Minimal 8 karakter" required>
            </div>
        </div>

        <div class="form-group">
            <label>Konfirmasi Kata Sandi</label>
            <div class="input-wrap">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                <input type="password" name="password_confirmation" placeholder="Ulangi kata sandi baru" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Kata Sandi</button>
    </form>

    <p class="auth-links">
        <a href="{{ route('login') }}">Kembali</a>
    </p>
@endsection