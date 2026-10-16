@extends('layouts.app')

@section('title', 'Percakapan Pesan Karir - Panel Admin')
@section('page-title', 'Percakapan')

@section('content')
<div class="kop-page">
    @include('koperasi._kop-style')

    <div class="kop-header">
        <div>
            <p class="kop-kicker">Pesan Karir</p>
            <h1 class="kop-title">{{ $user->name }}</h1>
            <p class="kop-subtitle">{{ $user->username ?? '' }} · {{ $user->email ?? '' }}</p>
        </div>
        <div class="kop-header-actions">
            <a href="{{ route('career.pesan.index') }}" class="kop-btn kop-btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Kembali
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">✓ {{ session('success') }}</div>
    @endif

    <div class="kop-panel">
        <div class="kop-panel-head">
            <div>
                <h2 class="kop-panel-title">Percakapan</h2>
                <p class="kop-panel-sub">{{ $messages->count() }} pesan</p>
            </div>
        </div>
        <div class="kop-panel-body" style="max-height:60vh;overflow-y:auto;">
            @forelse ($messages as $msg)
                @php $fromAdmin = $msg->sender === 'admin'; @endphp
                <div class="bubble-row" style="{{ $fromAdmin ? 'justify-content:flex-end;' : '' }}">
                    <div class="bubble" style="border-color:{{ $fromAdmin ? 'var(--kop-primary)' : 'var(--kop-border)' }};background:{{ $fromAdmin ? 'var(--kop-primary)' : '#fff' }};color:{{ $fromAdmin ? '#fff' : 'var(--kop-text)' }};">
                        <div class="bubble-author">{{ $fromAdmin ? 'BKK / Admin' : $user->name }}</div>
                        <div class="bubble-body">{{ $msg->body }}</div>
                        <div class="bubble-time">{{ $msg->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            @empty
                <div class="kop-empty">
                    <div class="kop-empty-icon">💬</div>
                    <h3>Belum ada pesan.</h3>
                </div>
            @endforelse
        </div>
    </div>

    <div class="kop-panel">
        <div class="kop-panel-head">
            <div>
                <h2 class="kop-panel-title">Balas</h2>
                <p class="kop-panel-sub">Balasan tampil di halaman Pesan milik siswa.</p>
            </div>
        </div>
        <div class="kop-panel-body">
            <form action="{{ route('career.pesan.store', $user) }}" method="POST">
                @csrf
                <div class="form-field">
                    <label for="replyBody">Balasan untuk {{ $user->name }}</label>
                    <textarea id="replyBody" name="body" rows="4" class="kop-input" required maxlength="2000" placeholder="Tulis balasan...">{{ old('body') }}</textarea>
                    @error('body')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-actions">
                    <button type="submit" class="kop-btn kop-btn-primary">Kirim Balasan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
