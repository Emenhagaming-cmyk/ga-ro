@extends('layouts.app')

@section('title', 'Daftar - SPMB SMK Bahrul Ulum')

@section('content')
<div class="form-section">
    <h1 class="form-title">Buat Akun Siswa</h1>
    <p class="form-subtitle">Daftar akun terlebih dahulu untuk mengisi formulir pendaftaran SPMB.</p>

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
            <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama lengkap Anda" required autofocus>
        </div>

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" value="{{ old('username') }}" placeholder="Contoh: ahmad.rizal" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Minimal 8 karakter" required>
            </div>
            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="password_confirmation" placeholder="Ulangi password" required>
            </div>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primary" style="width:100%;">Daftar</button>
        </div>
    </form>

    <p style="margin-top:18px;text-align:center;font-size:13px;color:#647067;">
        Sudah punya akun?
        <a href="{{ route('login') }}" style="color:#3a6450;font-weight:700;text-decoration:none;">Masuk di sini</a>
    </p>
</div>
@endsection