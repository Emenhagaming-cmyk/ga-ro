@extends('layouts.app')

@section('title', 'Dashboard Siswa - SMK Bahrul Ulum')

@section('content')
@php
    $hasData = !empty($pendaftaran);
    if ($hasData) {
        $isPreview = $pendaftaran->user_id !== Auth::id();
        $deadline = $pendaftaran->created_at->copy()->addDays(3);
        $canEdit = !$isPreview && $pendaftaran->status === 'baru' && now()->lt($deadline);
    }
@endphp

<div class="dashboard-shell">
    <div class="dashboard-header">
        <div>
            <p class="dashboard-kicker">Dashboard Siswa</p>
            <h1 class="form-title">HALO {{ $hasData ? $pendaftaran->nama_lengkap : Auth::user()->name }}</h1>
            <p class="form-subtitle">Pantau status pendaftaran Anda.</p>
        </div>
        <!-- <a href="{{ route('logout') }}" class="btn btn-secondary"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> -->
           
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    </div>

    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
    <div class="alert alert-error">
        @foreach ($errors->all() as $error)
            {{ $error }}<br>
        @endforeach
    </div>
    @endif

    @if (!$hasData)
    {{-- Empty state: siswa belum mengisi form --}}
    <div class="alert alert-success" style="margin-top:24px;">
        Anda belum mengisi formulir pendaftaran.
        <a href="{{ route('pendaftaran.create') }}" class="btn btn-primary" style="margin-left:12px;">Daftar</a>
    </div>
    @else
        @if ($isPreview)
        <div class="alert alert-success">
            Mode preview — melihat data pendaftar <strong>{{ $pendaftaran->nama_lengkap }}</strong>.
        </div>
        @endif

        {{-- Status Card --}}
        <div class="form-section">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
                <h2 class="form-title" style="margin:0;">Status Pendaftaran</h2>
                @php
                    $badge = [
                        'baru' => ['text' => 'Baru', 'color' => '#b45309', 'bg' => '#fef3c7'],
                        'diproses' => ['text' => 'Diproses', 'color' => '#1d4ed8', 'bg' => '#dbeafe'],
                        'diterima' => ['text' => 'Diterima', 'color' => '#166534', 'bg' => '#dcfce7'],
                        'ditolak' => ['text' => 'Ditolak', 'color' => '#b91c1c', 'bg' => '#fee2e2'],
                    ][$pendaftaran->status] ?? ['text' => $pendaftaran->status, 'color' => '#666', 'bg' => '#eee'];
                @endphp
                <span style="padding:8px 18px;border-radius:999px;font-weight:800;font-size:13px;background:{{ $badge['bg'] }};color:{{ $badge['color'] }};">
                    {{ strtoupper($badge['text']) }}
                </span>
            </div>

            <div class="status-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;">
                <div class="status-item"><div class="status-label">Nama Lengkap</div><div class="status-value">{{ $pendaftaran->nama_lengkap }}</div></div>
                <div class="status-item"><div class="status-label">NISN</div><div class="status-value">{{ $pendaftaran->nisn ?? '-' }}</div></div>
                <div class="status-item"><div class="status-label">Asal Sekolah</div><div class="status-value">{{ $pendaftaran->asal_sekolah }}</div></div>
                <div class="status-item"><div class="status-label">Jurusan Pilihan</div><div class="status-value">{{ $pendaftaran->jurusan_pilihan }}</div></div>
                <div class="status-item"><div class="status-label">Tanggal Daftar</div><div class="status-value">{{ $pendaftaran->created_at->format('d M Y') }}</div></div>
                @if ($pendaftaran->status_updated_at)
                <div class="status-item"><div class="status-label">Status Diperbarui</div><div class="status-value">{{ \Carbon\Carbon::parse($pendaftaran->status_updated_at)->format('d M Y H:i') }}</div></div>
                @endif
            </div>
        </div>

        {{-- Edit Form – hanya ketika boleh edit --}}
        @if ($canEdit)
        <div class="form-section" style="margin-top:24px;">
            <h2 class="form-title">Edit Formulir Pendaftaran</h2>
            <p class="form-subtitle">
                Anda masih dapat mengubah data sebelum diproses oleh pihak sekolah. Batas waktu edit: <strong style="color:#b45309;">{{ $deadline->format('d M Y H:i') }}</strong> (sisa {{ (int) round(now()->diffInHours($deadline, false)) }} jam).
            </p>
            <form method="POST" action="{{ route('pendaftaran.update', $pendaftaran) }}">
                @csrf @method('PUT')

                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $pendaftaran->nama_lengkap) }}" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>NISN</label>
                        <input type="text" name="nisn" value="{{ old('nisn', $pendaftaran->nisn) }}">
                    </div>
                    <div class="form-group">
                        <label>NIK</label>
                        <input type="text" name="nik" value="{{ old('nik', $pendaftaran->nik) }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $pendaftaran->tempat_lahir) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', optional($pendaftaran->tanggal_lahir)->format('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" required>
                            <option value="Laki-laki" {{ $pendaftaran->jenis_kelamin === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ $pendaftaran->jenis_kelamin === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>No. HP / WhatsApp</label>
                        <input type="tel" name="no_hp" value="{{ old('no_hp', $pendaftaran->no_hp) }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Alamat Lengkap</label>
                    <textarea name="alamat" required>{{ old('alamat', $pendaftaran->alamat) }}</textarea>
                </div>

                <hr class="section-divider">

                <div class="form-group">
                    <label>Asal Sekolah</label>
                    <input type="text" name="asal_sekolah" value="{{ old('asal_sekolah', $pendaftaran->asal_sekolah) }}" required>
                </div>

                <div class="form-group">
                    <label>Jurusan Pilihan</label>
                    <select name="jurusan_pilihan" required>
                        <option value="RPL" {{ $pendaftaran->jurusan_pilihan === 'RPL' ? 'selected' : '' }}>Rekayasa Perangkat Lunak (RPL)</option>
                    </select>
                </div>

                <hr class="section-divider">

                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Orang Tua</label>
                        <input type="text" name="nama_orang_tua" value="{{ old('nama_orang_tua', $pendaftaran->nama_orang_tua) }}" required>
                    </div>
                    <div class="form-group">
                        <label>No. HP Orang Tua</label>
                        <input type="tel" name="no_hp_orang_tua" value="{{ old('no_hp_orang_tua', $pendaftaran->no_hp_orang_tua) }}">
                    </div>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
        @else
        <div class="alert alert-success" style="margin-top:24px;">
            @if ($pendaftaran->status === 'baru')
                 Batas waktu edit telah berakhir. Hubungi admin bila ingin mengubah data.
            @elseif ($pendaftaran->status === 'diterima')
                 Selamat! Anda diterima di jurusan {{ $pendaftaran->jurusan_pilihan }}.
            @elseif ($pendaftaran->status === 'ditolak')
                 Maaf, pendaftaran Anda tidak diterima. Hubungi admin untuk info lebih lanjut.
            @else
                 Formulir Anda sedang diproses admin. Pantau status secara berkala.
            @endif
        </div>
        @endif
    @endif
</div>

<style>
    .dashboard-shell { max-width: 820px; }
    .dashboard-header { display:flex;justify-content:space-between;align-items:flex-start;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
    .dashboard-kicker { color:#3a6450;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;margin-bottom:6px; }
    .status-item { background:#f0f4ee;border:1px solid #dfe4dd;border-radius:12px;padding:14px 16px; }
    .status-label { font-size:11px;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;color:#647067;margin-bottom:6px; }
    .status-value { font-size:15px;font-weight:700;color:#1c2a23; }
</style>
@endsection