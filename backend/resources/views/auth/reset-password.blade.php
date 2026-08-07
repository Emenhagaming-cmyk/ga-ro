@extends('layouts.app')

@section('title', 'Reset Kata Sandi - SPMB SMK Bahrul Ulum')

@section('content')
<div class="form-section">
    <h1 class="form-title">Reset Kata Sandi</h1>
    <p class="form-subtitle">Masukkan email terdaftar dan kata sandi baru Anda.</p>

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
            <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
        </div>

        <div class="form-group">
            <label>Kata Sandi Baru</label>
            <input type="password" name="password" placeholder="Minimal 8 karakter" required>
        </div>

        <div class="form-group">
            <label>Konfirmasi Kata Sandi</label>
            <input type="password" name="password_confirmation" placeholder="Ulangi kata sandi baru" required>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primary" style="width:100%;">Simpan Kata Sandi</button>
        </div>
    </form>

    <p style="margin-top:18px;text-align:center;font-size:13px;color:#647067;">
        <a href="{{ route('login') }}" style="color:#3a6450;font-weight:700;text-decoration:none;">Kembali ke Masuk</a>
    </p>
</div>
@endsection