@extends('layouts.auth')

@section('title', 'Lupa Kata Sandi - SPMB SMK Bahrul Ulum')

@section('content')
    <h1 class="form-title">Lupa Kata Sandi</h1>
    <p class="form-subtitle">Masukkan username atau email untuk mendapat link reset kata sandi.</p>

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-error">
        @foreach ($errors->all() as $error)
            {{ $error }}<br>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="form-group">
            <label>Username atau Email</label>
            <div class="input-wrap">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                <input type="text" name="username" value="{{ old('username') }}" placeholder="Masukkan username atau email" required autofocus>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Kirim Link Reset</button>
    </form>

    <p class="auth-links">
        <a href="{{ route('login') }}">Kembali ke Masuk</a>
    </p>
@endsection