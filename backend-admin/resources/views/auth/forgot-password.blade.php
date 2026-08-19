@extends('layouts.app')

@section('title', 'Lupa Kata Sandi - SPMB SMK Bahrul Ulum')

@section('content')
<div class="form-section">
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
            <input type="text" name="username" value="{{ old('username') }}" placeholder="Masukkan username atau email" required autofocus>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primary" style="width:100%;">Kirim Link Reset</button>
        </div>
    </form>

    <p style="margin-top:18px;text-align:center;font-size:13px;color:#647067;">
        <a href="{{ route('login') }}" style="color:#3a6450;font-weight:700;text-decoration:none;">Kembali ke Masuk</a>
    </p>
</div>
@endsection