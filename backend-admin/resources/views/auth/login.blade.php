@extends('layouts.app')

@section('title', 'Masuk Admin - SPMB SMK Bahrul Ulum')

@section('content')
<div class="form-section" style="max-width:480px;margin:20px auto 0;">
    <style>
        .pw-wrap {
            position: relative;
        }

        .pw-wrap input {
            padding-right: 44px;
        }

        .pw-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
            background: none;
            border: none;
            border-radius: 8px;
            color: #647067;
            cursor: pointer;
            transition: color 0.25s ease;
        }

        .pw-toggle:hover {
            color: #3a6450;
        }

        .pw-toggle:focus-visible {
            outline: 2px solid #3a6450;
            outline-offset: 2px;
        }
    </style>

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
            <div class="pw-wrap">
                <input type="password" name="password" placeholder="••••••••" required>
                <button type="button" class="pw-toggle" onclick="togglePw(this)" aria-label="Tampilkan password" aria-pressed="false" title="Tampilkan password">
                    <svg class="eye-open" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    <svg class="eye-off" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                </button>
            </div>
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
