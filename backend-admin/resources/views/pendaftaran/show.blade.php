@extends('layouts.app')

@section('title', 'Detail Pendaftar - SPMB SMK Bahrul Ulum')

@section('content')
@php
    $badge = [
        'baru' => ['text' => 'Baru', 'color' => '#b45309', 'bg' => '#fef3c7'],
        'diproses' => ['text' => 'Diproses', 'color' => '#1d4ed8', 'bg' => '#dbeafe'],
        'diterima' => ['text' => 'Diterima', 'color' => '#166534', 'bg' => '#dcfce7'],
        'ditolak' => ['text' => 'Ditolak', 'color' => '#b91c1c', 'bg' => '#fee2e2'],
    ][$pendaftaran->status] ?? ['text' => $pendaftaran->status, 'color' => '#666', 'bg' => '#eee'];
    $fileLabels = [
        'foto_3x4' => 'Foto 3x4',
        'kk_file' => 'Kartu Keluarga',
        'ijazah_file' => 'Ijazah/Raport',
        'sktm_file' => 'Surat Keterangan',
    ];
@endphp

<div class="dashboard-shell">
    <div class="dashboard-header">
        <div>
            <p class="dashboard-kicker">Admin Dashboard</p>
            <h1 class="form-title">Detail Pendaftar: {{ $pendaftaran->nama_lengkap }}</h1>
            <p class="form-subtitle">Data lengkap pendaftaran SPMB. Ubah status jika diperlukan.</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('pendaftaran.index') }}" class="btn btn-secondary">← Kembali</a>
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success">✓ {{ session('success') }}</div>
    @endif

    {{-- Status & Quick Update --}}
    <div class="form-section">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:14px;">
            <h2 class="form-title" style="margin:0;">Status Pendaftaran</h2>
            <span style="padding:8px 18px;border-radius:999px;font-weight:800;font-size:13px;background:{{ $badge['bg'] }};color:{{ $badge['color'] }};">
                {{ strtoupper($badge['text']) }}
            </span>
        </div>

        <form method="POST" action="{{ route('pendaftaran.status', $pendaftaran) }}" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-top:14px;">
            @csrf
            @method('PUT')
            <label for="status" style="margin:0;">Ubah status:</label>
            <select name="status" id="status" style="width:auto;flex:1;min-width:160px;">
                @foreach (['baru', 'diproses', 'diterima', 'ditolak'] as $s)
                <option value="{{ $s }}" {{ $pendaftaran->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Simpan Status</button>
        </form>

        @if ($pendaftaran->status_updated_at)
        <p style="font-size:12px;color:#647067;margin-top:10px;">Status terakhir diperbarui: {{ \Carbon\Carbon::parse($pendaftaran->status_updated_at)->format('d M Y H:i') }}</p>
        @endif
    </div>

    {{-- Identitas --}}
    <div class="form-section">
        <h2 class="form-title">Identitas Calon Peserta</h2>
        <div class="detail-grid">
            @foreach ([
                'Nama Lengkap' => $pendaftaran->nama_lengkap,
                'Nama Panggilan' => $pendaftaran->nama_panggilan,
                'NISN' => $pendaftaran->nisn,
                'NIK' => $pendaftaran->nik,
                'Tempat Lahir' => $pendaftaran->tempat_lahir,
                'Tanggal Lahir' => optional($pendaftaran->tanggal_lahir)->format('d M Y'),
                'Umur' => $pendaftaran->umur,
                'Agama' => $pendaftaran->agama,
                'Kewarganegaraan' => $pendaftaran->kewarnegaraan,
                'Kategori Pendaftar' => $pendaftaran->kategori_pendaftar,
                'Jenis Kelamin' => $pendaftaran->jenis_kelamin,
                'Alamat' => $pendaftaran->alamat,
                'RT/RW' => $pendaftaran->rt_rw,
                'Kode Pos' => $pendaftaran->kode_pos,
                'No HP / WA' => $pendaftaran->no_hp,
                'Email' => $pendaftaran->email,
            ] as $label => $value)
            <div class="detail-item">
                <div class="detail-label">{{ $label }}</div>
                <div class="detail-value">{{ $value ?: '-' }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Sekolah --}}
    <div class="form-section">
        <h2 class="form-title">Data Sekolah</h2>
        <div class="detail-grid">
            @foreach ([
                'Asal Sekolah' => $pendaftaran->asal_sekolah,
                'Gelombang' => $pendaftaran->gelombang,
                'Tahun Lulus' => $pendaftaran->tahun_lulus,
                'Rata-rata Nilai' => $pendaftaran->rata_rata_nilai,
                'Jurusan Pilihan' => $pendaftaran->jurusan_pilihan,
            ] as $label => $value)
            <div class="detail-item">
                <div class="detail-label">{{ $label }}</div>
                <div class="detail-value">{{ $value ?: '-' }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Keluarga --}}
    <div class="form-section">
        <h2 class="form-title">Data Keluarga</h2>
        <div class="detail-grid">
            @foreach ([
                'Jumlah Saudara' => $pendaftaran->jumlah_saudara,
                'Anak Ke' => $pendaftaran->anak_ke,
                'Status Keluarga' => $pendaftaran->status_keluarga,
                'Nama Ayah' => $pendaftaran->nama_ayah,
                'Pendidikan Ayah' => $pendaftaran->pendidikan_ayah,
                'Pekerjaan Ayah' => $pendaftaran->pekerjaan_ayah,
                'Penghasilan Ayah' => $pendaftaran->penghasilan_ayah,
                'Alamat Ayah' => $pendaftaran->alamat_ayah,
                'HP Ayah' => $pendaftaran->hp_ayah,
                'Nama Ibu' => $pendaftaran->nama_ibu,
                'Pendidikan Ibu' => $pendaftaran->pendidikan_ibu,
                'Pekerjaan Ibu' => $pendaftaran->pekerjaan_ibu,
                'Penghasilan Ibu' => $pendaftaran->penghasilan_ibu,
                'Alamat Ibu' => $pendaftaran->alamat_ibu,
                'HP Ibu' => $pendaftaran->hp_ibu,
                'Nama Wali' => $pendaftaran->nama_wali,
                'Hubungan Wali' => $pendaftaran->hubungan_wali,
                'Email Orang Tua' => $pendaftaran->email_orang_tua,
            ] as $label => $value)
            <div class="detail-item">
                <div class="detail-label">{{ $label }}</div>
                <div class="detail-value">{{ $value ?: '-' }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Pembayaran & Berkas --}}
    <div class="form-section">
        <h2 class="form-title">Pembayaran & Berkas</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Jenis Pembayaran</div>
                <div class="detail-value">{{ $pendaftaran->jenis_pembayaran ?: '-' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Berkas Tambahan</div>
                <div class="detail-value">{{ $pendaftaran->berkas_tambahan ?: '-' }}</div>
            </div>
        </div>

        <hr class="section-divider">

        <div class="detail-grid">
            @foreach ($fileLabels as $field => $label)
            <div class="detail-item">
                <div class="detail-label">{{ $label }}</div>
                <div class="detail-value">
                    @if ($pendaftaran->{$field})
                    <a href="{{ asset('storage/' . $pendaftaran->{$field}) }}" target="_blank" rel="noopener" style="color:#3a6450;font-weight:700;">⬇ Unduh file</a>
                    @else
                    <span style="color:#a3a8a4;">Belum diunggah</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

    <style>
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 12px;
    }
    .detail-item {
        background: #f0f4ee;
        border: 1px solid #dfe4dd;
        border-radius: 12px;
        padding: 12px 14px;
    }
    .detail-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #647067;
        margin-bottom: 4px;
    }
    .detail-value {
        font-size: 14px;
        font-weight: 700;
        color: #1c2a23;
        word-break: break-word;
    }
    @media (max-width: 480px) {
        .detail-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection
