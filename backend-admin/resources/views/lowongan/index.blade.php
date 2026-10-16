@extends('layouts.app')

@section('title', 'Lowongan - Career Center')
@section('page-title', 'Kelola Lowongan')

@section('content')
<style>
    /* ===== Kelola Lowongan — prefix lo- (di-scope via .lo-page agar tidak bentrok dgn kop-) ===== */
    .lo-page {
        --lo-green: #3a6450;
        --lo-green-soft: #eef4f0;
        --lo-line: #e5e7e6;
        --lo-text: #24312a;
        --lo-muted: #647067;
    }

    .lo-page .alert {
        display: flex;
        align-items: center;
        gap: 8px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
    }

    /* ---- Filter bar ---- */
    .lo-page .kop-filter-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        background: #fff;
        border: 1px solid var(--lo-line);
        border-radius: 14px;
        padding: 14px 16px;
        margin: 20px 0 24px;
    }

    .lo-search {
        position: relative;
        flex: 1 1 240px;
        min-width: 200px;
    }

    .lo-search > svg {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        color: #9aa5a0;
        pointer-events: none;
    }

    .lo-page .lo-search .kop-input {
        width: 100%;
        padding-left: 38px;
    }

    .lo-page .kop-filter-row .kop-select {
        min-width: 140px;
    }

    /* ---- Badge tipe ---- */
    .lo-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11.5px;
        font-weight: 800;
        letter-spacing: .3px;
        padding: 4px 10px;
        border-radius: 999px;
        white-space: nowrap;
    }

    .lo-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .lo-badge--magang  { background: #e8f0fd; color: #2563eb; }
    .lo-badge--kerja   { background: #e7f5ec; color: #1f7a44; }
    .lo-badge--bkk     { background: #fdf1e0; color: #b45309; }
    .lo-badge--default { background: #f1f3f2; color: #5a6660; }

    /* ---- Chip model kerja ---- */
    .lo-chip {
        display: inline-block;
        margin-top: 5px;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
        padding: 2px 8px;
        border-radius: 6px;
        background: #f1f4f2;
        color: #5a6660;
        width: max-content;
    }

    /* ---- Deadline ---- */
    .lo-deadline {
        display: flex;
        flex-direction: column;
        gap: 2px;
        line-height: 1.25;
    }

    .lo-deadline small {
        font-size: 11px;
        font-weight: 800;
    }

    .lo-deadline--lewat  { color: #b91c1c; }
    .lo-deadline--lewat small { color: #dc2626; }
    .lo-deadline--segera { color: #92400e; }
    .lo-deadline--segera small { color: #d97706; }

    .lo-muted { color: #9aa5a0; }

    /* ---- Pill pelamar ---- */
    .lo-pelamar {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 999px;
        background: #f1f4f2;
        color: #5a6660;
        font-size: 12.5px;
        font-weight: 800;
        text-decoration: none;
        transition: background-color .15s, color .15s;
    }

    .lo-pelamar svg { width: 13px; height: 13px; }

    .lo-pelamar--has {
        background: var(--lo-green-soft);
        color: var(--lo-green);
    }

    .lo-pelamar:hover {
        background: var(--lo-green);
        color: #fff;
    }

    /* ---- Table polish ---- */
    .lo-page .kop-table tbody tr {
        transition: background-color .15s ease;
    }

    .lo-page .kop-table tbody tr:hover {
        background-color: #fafcfb;
    }

    .lo-page .kop-table td {
        vertical-align: middle;
    }

    /* ---- Empty state ---- */
    .lo-empty {
        text-align: center;
        padding: 52px 20px;
    }

    .lo-empty-icon {
        width: 76px;
        height: 76px;
        margin: 0 auto 18px;
        border-radius: 50%;
        background: var(--lo-green-soft);
        color: var(--lo-green);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .lo-empty-icon svg { width: 32px; height: 32px; }

    .lo-empty h3 {
        margin: 0 0 6px;
        font-size: 16px;
        font-weight: 800;
        color: var(--lo-text);
    }

    .lo-empty p {
        margin: 0 0 18px;
        font-size: 13.5px;
        color: var(--lo-muted);
    }

    .lo-empty-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
    }
</style>

<div class="kop-page lo-page">
    @include('koperasi._kop-style')

    <div class="kop-header">
        <div>
            <p class="kop-kicker">Career Center</p>
            <h1 class="kop-title">Lowongan</h1>
            <p class="kop-subtitle">Lowongan di sini yang tampil di halaman <strong>Cari Lowongan</strong> milik siswa.</p>
        </div>
        <div class="kop-header-actions">
            <a href="{{ route('career.statistik') }}" class="kop-btn kop-btn-outline">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                Statistik
            </a>
            <a href="{{ route('lowongan.create') }}" class="kop-btn kop-btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Lowongan
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">✓ {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <form method="GET" action="{{ route('lowongan.index') }}" class="kop-filter-row">
        <div class="lo-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" class="kop-input" placeholder="Cari judul, perusahaan, atau lokasi…" aria-label="Cari lowongan">
        </div>

        <select name="type" class="kop-select" aria-label="Filter tipe" onchange="this.form.submit()">
            <option value="">Semua tipe</option>
            @foreach (['Magang', 'Kerja', 'BKK'] as $t)
                <option value="{{ $t }}" @selected(request('type') === $t)>{{ $t }}</option>
            @endforeach
        </select>

        <select name="status" class="kop-select" aria-label="Filter status" onchange="this.form.submit()">
            <option value="">Semua status</option>
            <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
            <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
        </select>

        <button type="submit" class="kop-btn kop-btn-primary">Terapkan</button>
        @if (request()->hasAny(['search', 'type', 'status']))
            <a href="{{ route('lowongan.index') }}" class="kop-btn kop-btn-ghost">Reset</a>
        @endif
    </form>

    <div class="kop-panel">
        <div class="kop-panel-head">
            <div>
                <h2 class="kop-panel-title">Daftar Lowongan</h2>
                <p class="kop-panel-sub">
                    Menampilkan {{ $lowongans->firstItem() ?? 0 }}–{{ $lowongans->lastItem() ?? 0 }} dari {{ $lowongans->total() }} lowongan
                </p>
            </div>
        </div>

        <div class="kop-table-wrap">
            <table class="kop-table">
                <thead>
                    <tr>
                        <th scope="col">Lowongan</th>
                        <th scope="col" class="kop-hide-sm">Jurusan</th>
                        <th scope="col" class="kop-nowrap">Tipe</th>
                        <th scope="col" class="kop-nowrap">Deadline</th>
                        <th scope="col" class="kop-nowrap">Pelamar</th>
                        <th scope="col" class="kop-nowrap">Status</th>
                        <th scope="col" class="kop-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lowongans as $lowongan)
                        @php
                            $typeClass = ['Magang' => 'magang', 'Kerja' => 'kerja', 'BKK' => 'bkk'][$lowongan->type] ?? 'default';
                            $dl = $lowongan->deadline;
                            $dlState = null;
                            if ($dl) {
                                if ($dl->copy()->endOfDay()->isPast()) $dlState = 'lewat';
                                elseif ($dl->lessThan(now()->addDays(7))) $dlState = 'segera';
                            }
                        @endphp
                        <tr>
                            <td style="min-width:230px;">
                                <div class="kop-user-text">
                                    <span class="kop-user-name">{{ $lowongan->title }}</span>
                                    <span class="kop-user-sub">{{ $lowongan->company }} · {{ $lowongan->location }}</span>
                                    @if ($lowongan->work_arrangement)
                                        <span class="lo-chip">{{ $lowongan->work_arrangement }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="kop-hide-sm">{{ $lowongan->jurusan ?: '-' }}</td>
                            <td class="kop-nowrap">
                                <span class="lo-badge lo-badge--{{ $typeClass }}">{{ $lowongan->type }}</span>
                            </td>
                            <td class="kop-nowrap">
                                @if ($dl)
                                    <div class="lo-deadline @if($dlState) lo-deadline--{{ $dlState }} @endif">
                                        <span>{{ $dl->format('d/m/Y') }}</span>
                                        @if ($dlState === 'lewat')
                                            <small>Lewat batas</small>
                                        @elseif ($dlState === 'segera')
                                            <small>Segera berakhir</small>
                                        @endif
                                    </div>
                                @else
                                    <span class="lo-muted">–</span>
                                @endif
                            </td>
                            <td class="kop-nowrap">
                                <a href="{{ route('lamaran.index', ['lowongan' => $lowongan->id]) }}"
                                   class="lo-pelamar @if($lowongan->lamarans_count) lo-pelamar--has @endif"
                                   title="Lihat {{ $lowongan->lamarans_count }} pelamar">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    {{ $lowongan->lamarans_count }}
                                </a>
                            </td>
                            <td class="kop-nowrap">
                                @if ($lowongan->is_active)
                                    <span class="kop-badge kop-badge--diambil">Aktif</span>
                                @else
                                    <span class="kop-badge kop-badge--batal">Nonaktif</span>
                                @endif
                            </td>
                            <td class="kop-nowrap">
                                <div class="kop-act">
                                    <a href="{{ route('lowongan.edit', $lowongan) }}" class="kop-act-btn kop-act--edit">Edit</a>
                                    <form action="{{ route('lowongan.toggle', $lowongan) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="kop-act-btn kop-act--ok">{{ $lowongan->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                    </form>
                                    <form action="{{ route('lowongan.destroy', $lowongan) }}" method="POST" onsubmit="return confirm('Hapus lowongan ini? Tindakan ini tidak bisa dibatalkan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="kop-act-btn kop-act--danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="lo-empty">
                                    <div class="lo-empty-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                    </div>
                                    @if (request()->hasAny(['search', 'type', 'status']))
                                        <h3>Tidak ada lowongan yang cocok</h3>
                                        <p>Coba ubah kata kunci atau reset filter pencarian.</p>
                                        <div class="lo-empty-actions">
                                            <a href="{{ route('lowongan.index') }}" class="kop-btn kop-btn-primary">Reset filter</a>
                                            <a href="{{ route('lowongan.create') }}" class="kop-btn kop-btn-outline">Tambah Lowongan</a>
                                        </div>
                                    @else
                                        <h3>Belum ada lowongan</h3>
                                        <p>Tambahkan lowongan supaya siswa bisa melamar.</p>
                                        <div class="lo-empty-actions">
                                            <a href="{{ route('lowongan.create') }}" class="kop-btn kop-btn-primary">Tambah Lowongan</a>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($lowongans->hasPages())
            <div class="kop-panel-foot">
                {{ $lowongans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection