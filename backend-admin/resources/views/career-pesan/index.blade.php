@extends('layouts.app')

@section('title', 'Pesan Karir - Panel Admin')
@section('page-title', 'Pesan Karir')

@section('content')
<div class="kop-page">
    @include('koperasi._kop-style')

    <div class="kop-header">
        <div>
            <p class="kop-kicker">Career Center</p>
            <h1 class="kop-title">Pesan Karir</h1>
            <p class="kop-subtitle">Pertanyaan siswa soal lowongan, PKL, dan lamaran. Balas dari sini.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">✓ {{ session('success') }}</div>
    @endif

    <div class="kop-panel">
        <div class="kop-panel-head">
            <div>
                <h2 class="kop-panel-title">Daftar Percakapan</h2>
                <p class="kop-panel-sub">{{ $conversations->count() }} percakapan dengan siswa</p>
            </div>
        </div>

        <div class="kop-table-wrap">
            <table class="kop-table">
                <thead>
                    <tr>
                        <th scope="col">Siswa</th>
                        <th scope="col" class="kop-hide-sm">Pesan Terakhir</th>
                        <th scope="col" class="kop-hide-sm kop-nowrap">Waktu</th>
                        <th scope="col" class="kop-nowrap">Status</th>
                        <th scope="col" class="kop-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($conversations as $row)
                        <tr>
                            <td style="min-width:180px;">
                                <div class="kop-user-text">
                                    <span class="kop-user-name">{{ $row['user']->name }}</span>
                                    <span class="kop-user-sub">{{ $row['user']->username ?? '' }}</span>
                                </div>
                            </td>
                            <td class="kop-hide-sm">{{ \Illuminate\Support\Str::limit($row['last']->body ?? '-', 70) }}</td>
                            <td class="kop-hide-sm kop-nowrap">{{ $row['last']?->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="kop-nowrap">
                                @if ($row['unread'] > 0)
                                    <span class="kop-badge kop-badge--pending">{{ $row['unread'] }} belum dibaca</span>
                                @else
                                    <span class="kop-badge kop-badge--diambil">Terbaca</span>
                                @endif
                            </td>
                            <td class="kop-nowrap">
                                <div class="kop-act">
                                    <a href="{{ route('career.pesan.show', $row['user']) }}" class="kop-act-btn kop-act--view">Buka</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="kop-empty">
                                    <div class="kop-empty-icon">💬</div>
                                    <h3>Belum ada pesan dari siswa</h3>
                                    <p>Pertanyaan siswa akan muncul di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
