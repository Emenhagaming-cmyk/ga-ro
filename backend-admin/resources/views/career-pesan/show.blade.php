@extends('layouts.app')

@section('title', 'Percakapan Pesan Karir - Panel Admin')
@section('page-title', 'Percakapan')

@section('content')
@if (session('success'))
    <div class="alert alert-success">✓ {{ session('success') }}</div>
@endif

<div class="wa-page">
    {{-- Header ala WhatsApp: kembali + avatar + identitas siswa --}}
    <div class="wa-head">
        <a href="{{ route('career.pesan.index') }}" class="wa-back" aria-label="Kembali ke daftar percakapan">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        </a>
        <div class="wa-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</div>
        <div class="wa-peer">
            <span class="wa-peer-name">{{ $user->name }}</span>
            <span class="wa-peer-sub">{{ $user->username ?? $user->email }} · {{ $messages->count() }} pesan</span>
        </div>
    </div>

    <div class="wa-thread" id="waThread">
        @forelse ($messages as $msg)
            @php $fromAdmin = $msg->sender === 'admin'; @endphp
            <div class="wa-row {{ $fromAdmin ? 'wa-out' : 'wa-in' }}">
                <div class="wa-bubble">
                    <span class="wa-text">{{ $msg->body }}</span>
                    <span class="wa-meta" title="{{ $msg->created_at->format('d/m/Y H:i') }}">{{ $msg->created_at->format('H:i') }}</span>
                </div>
            </div>
        @empty
            <p class="wa-empty">Belum ada pesan. Tulis balasan pertama untuk {{ $user->name }}.</p>
        @endforelse
    </div>

    @error('body')
        <p class="form-error wa-error">{{ $message }}</p>
    @enderror

    <form action="{{ route('career.pesan.store', $user) }}" method="POST" class="wa-composer">
        @csrf
        <input type="text" name="body" id="replyBody" class="wa-input" value="{{ old('body') }}"
               placeholder="Tulis balasan..." maxlength="2000" required autocomplete="off"
               aria-label="Balasan untuk {{ $user->name }}">
        <button type="submit" class="wa-send" aria-label="Kirim balasan">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M2 21l21-9L2 3v7l15 2-15 2z"/></svg>
        </button>
    </form>
</div>

<script>
    // Buka percakapan langsung di pesan terbaru.
    var waThread = document.getElementById('waThread');
    if (waThread) { waThread.scrollTop = waThread.scrollHeight; }
</script>
@endsection
