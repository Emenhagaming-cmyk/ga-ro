@extends('layouts.app')

@section('title', 'Masuk Admin - SPMB SMK Bahrul Ulum')

@section('content')
<div class="form-section" style="max-width:480px;margin:20px auto 0;">
    <h1 class="form-title">Masuk Panel Admin</h1>
    <p class="form-subtitle">Login khusus administrator untuk memantau data pendaftaran SPMB SMK Bahrul Ulum.</p>

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
            <input type="text" name="username" value="{{ old('username') }}" placeholder="Masukkan username admin" required autofocus>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primary" style="width:100%;">Masuk</button>
        </div>
    </form>

    <p style="margin-top:14px;text-align:center;font-size:13px;color:#647067;">
        <a href="{{ route('password.request') }}" style="color:#3a6450;font-weight:700;text-decoration:none;">Lupa kata sandi?</a>
    </p>
</div>
@endsection
