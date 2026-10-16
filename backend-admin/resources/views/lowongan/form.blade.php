@extends('layouts.app')

@section('title', ($lowongan->exists ? 'Edit' : 'Tambah') . ' Lowongan - Career Center')
@section('page-title', ($lowongan->exists ? 'Edit' : 'Tambah') . ' Lowongan')

@section('content')
<div class="kop-page">
    @include('koperasi._kop-style')

    <div class="kop-header">
        <div>
            <p class="kop-kicker">Career Center</p>
            <h1 class="kop-title">{{ $lowongan->exists ? 'Edit Lowongan' : 'Tambah Lowongan' }}</h1>
            <p class="kop-subtitle">Yang diisi di sini langsung tampil di halaman Cari Lowongan siswa.</p>
        </div>
        <div class="kop-header-actions">
            <a href="{{ route('lowongan.index') }}" class="kop-btn kop-btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Kembali ke Daftar
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-error">
            <strong>Periksa kembali isian berikut:</strong>
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ $lowongan->exists ? route('lowongan.update', $lowongan) : route('lowongan.store') }}" method="POST">
        @csrf
        @if ($lowongan->exists)
            @method('PUT')
        @endif

        <div class="kop-form-layout">
            <div class="kop-panel">
                <div class="kop-panel-head">
                    <div>
                        <h2 class="kop-panel-title">Informasi Lowongan</h2>
                        <p class="kop-panel-sub">Data dasar yang dilihat pelamar</p>
                    </div>
                </div>
                <div class="kop-panel-body">
                    <div class="kop-field">
                        <label class="kop-label" for="lowTitle">Judul Lowongan</label>
                        <input id="lowTitle" type="text" name="title" value="{{ old('title', $lowongan->title) }}"
                               class="kop-input {{ $errors->has('title') ? 'has-error' : '' }}" required
                               placeholder="cth: Junior Web Developer">
                        @error('title')<span class="kop-field-error">{{ $message }}</span>@enderror
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div class="kop-field">
                            <label class="kop-label" for="lowCompany">Perusahaan</label>
                            <input id="lowCompany" type="text" name="company" value="{{ old('company', $lowongan->company) }}"
                                   class="kop-input {{ $errors->has('company') ? 'has-error' : '' }}" required
                                   placeholder="cth: Digital Nusantara">
                            @error('company')<span class="kop-field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="kop-field">
                            <label class="kop-label" for="lowLocation">Lokasi</label>
                            <input id="lowLocation" type="text" name="location" value="{{ old('location', $lowongan->location) }}"
                                   class="kop-input {{ $errors->has('location') ? 'has-error' : '' }}" required
                                   placeholder="cth: Surabaya" list="lokasiList">
                            <datalist id="lokasiList">
                                <option value="Surabaya"></option>
                                <option value="Sidoarjo"></option>
                                <option value="Gresik"></option>
                                <option value="Malang"></option>
                            </datalist>
                            @error('location')<span class="kop-field-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                        <div class="kop-field">
                            <label class="kop-label" for="lowJurusan">Jurusan</label>
                            <input id="lowJurusan" type="text" name="jurusan" value="{{ old('jurusan', $lowongan->jurusan) }}"
                                   class="kop-input {{ $errors->has('jurusan') ? 'has-error' : '' }}" required list="jurusanList"
                                   placeholder="RPL">
                            <datalist id="jurusanList">
                                <option value="RPL"></option>
                            </datalist>
                            @error('jurusan')<span class="kop-field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="kop-field">
                            <label class="kop-label" for="lowType">Tipe</label>
                            <select id="lowType" name="type" class="kop-select {{ $errors->has('type') ? 'has-error' : '' }}" required>
                                @foreach (['Magang', 'Kerja', 'BKK'] as $t)
                                    <option value="{{ $t }}" @selected(old('type', $lowongan->type) === $t)>{{ $t }}</option>
                                @endforeach
                            </select>
                            @error('type')<span class="kop-field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="kop-field">
                            <label class="kop-label" for="lowArrangement">Model Kerja</label>
                            <select id="lowArrangement" name="work_arrangement" class="kop-select">
                                <option value="onsite" @selected(old('work_arrangement', $lowongan->work_arrangement) === 'onsite')>Onsite</option>
                                <option value="hybrid" @selected(old('work_arrangement', $lowongan->work_arrangement) === 'hybrid')>Hybrid</option>
                                <option value="remote" @selected(old('work_arrangement', $lowongan->work_arrangement) === 'remote')>Remote</option>
                            </select>
                        </div>
                    </div>

                    <div class="kop-field">
                        <label class="kop-label" for="lowDeadline">Batas Lamaran <span class="opt">(opsional)</span></label>
                        <input id="lowDeadline" type="date" name="deadline"
                               value="{{ old('deadline', $lowongan->deadline?->format('Y-m-d')) }}"
                               class="kop-input {{ $errors->has('deadline') ? 'has-error' : '' }}">
                        @error('deadline')<span class="kop-field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="kop-field">
                        <label class="kop-label" for="lowDesc">Deskripsi</label>
                        <textarea id="lowDesc" name="description" rows="5"
                                  class="kop-textarea {{ $errors->has('description') ? 'has-error' : '' }}" required
                                  placeholder="Tugas, syarat, dan benefit…">{{ old('description', $lowongan->description) }}</textarea>
                        @error('description')<span class="kop-field-error">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            <div class="kop-form-side">
                <div class="kop-panel">
                    <div class="kop-panel-head">
                        <h2 class="kop-panel-title">Publikasi</h2>
                    </div>
                    <div class="kop-panel-body">
                        <div class="kop-field">
                            <label class="kop-check">
                                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $lowongan->is_active ?? true))>
                                <span>Aktifkan lowongan.<br>
                                    <small style="color:var(--kop-muted);font-weight:600;">Nonaktif = tidak muncul di halaman siswa.</small>
                                </span>
                            </label>
                        </div>
                        <div class="kop-form-actions" style="flex-direction:column;">
                            <button type="submit" class="kop-btn kop-btn-primary">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                {{ $lowongan->exists ? 'Simpan Perubahan' : 'Tambah Lowongan' }}
                            </button>
                            <a href="{{ route('lowongan.index') }}" class="kop-btn kop-btn-ghost">Batal</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
