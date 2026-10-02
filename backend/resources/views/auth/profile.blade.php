@extends('layouts.app')

@section('title', 'Profil Siswa - SMK Bahrul Ulum')

@section('content')
@php
    $nama = $pendaftaran?->nama_lengkap ?: $user->name;
    $inisial = strtoupper(mb_substr($nama, 0, 1));
    $bulan = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];

    $tgl = function ($value) use ($bulan) {
        if (empty($value)) {
            return '-';
        }
        try {
            $dt = \Illuminate\Support\Carbon::parse($value);
        } catch (\Throwable $e) {
            return '-';
        }
        return $dt->day . ' ' . $bulan[(int) $dt->month] . ' ' . $dt->year;
    };

    $dtFull = function ($value) use ($bulan) {
        if (empty($value)) {
            return '-';
        }
        try {
            $dt = \Illuminate\Support\Carbon::parse($value);
        } catch (\Throwable $e) {
            return '-';
        }
        return $dt->day . ' ' . $bulan[(int) $dt->month] . ' ' . $dt->year . ', ' . $dt->format('H:i');
    };

    $val = fn ($value) => ($value === null || $value === '') ? '-' : $value;

    // $errors hanya di-share oleh middleware web; jaga agar view tetap bisa dirender di konteks lain
    $errorBag = $errors ?? new \Illuminate\Support\ViewErrorBag;

    $status = $pendaftaran?->status;
    $statusLabel = ['baru' => 'Baru', 'diproses' => 'Diproses', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak'][$status] ?? 'Belum Mendaftar';
    $statusClass = $status ?: 'kosong';

    $berkas = [
        'Foto 3×4' => $pendaftaran?->foto_3x4,
        'Kartu Keluarga' => $pendaftaran?->kk_file,
        'Ijazah / SKL' => $pendaftaran?->ijazah_file,
        'SKTM' => $pendaftaran?->sktm_file,
    ];

    $ayah = ['Pendidikan' => $pendaftaran?->pendidikan_ayah, 'Pekerjaan' => $pendaftaran?->pekerjaan_ayah, 'Penghasilan' => $pendaftaran?->penghasilan_ayah, 'Alamat' => $pendaftaran?->alamat_ayah, 'No. HP' => $pendaftaran?->hp_ayah];
    $ibu = ['Pendidikan' => $pendaftaran?->pendidikan_ibu, 'Pekerjaan' => $pendaftaran?->pekerjaan_ibu, 'Penghasilan' => $pendaftaran?->penghasilan_ibu, 'Alamat' => $pendaftaran?->alamat_ibu, 'No. HP' => $pendaftaran?->hp_ibu];
@endphp

<div class="pf">
    <!-- Header (tombol kembali & pengaturan sudah ada di navbar) -->
    <h1 class="pf-header-title">Profil Siswa</h1>

    @if (session('success'))
        <div class="pf-alert pf-alert--ok" role="status" aria-live="polite">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="pf-alert pf-alert--err" role="alert" aria-live="assertive">{{ session('error') }}</div>
    @endif
    @if ($errorBag->any())
        <div class="pf-alert pf-alert--err" role="alert" aria-live="assertive">
            @foreach ($errorBag->all() as $message)
                <div>{{ $message }}</div>
            @endforeach
        </div>
    @endif

    <!-- Kartu profil -->
    <section class="pf-card pf-card--profile">
        @if ($canEdit)
            <a href="{{ route('dashboard.siswa') }}#edit-section" class="pf-card-pencil" title="Edit data pendaftaran" aria-label="Edit data pendaftaran">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
            </a>
        @endif

        <div class="pf-profile-top">
            <form class="pf-avatar-form" action="{{ route('profil.avatar.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="pf-avatar" id="pfAvatar">
                    @if ($user->avatar)
                        <img src="{{ $user->avatar }}" alt="Foto profil {{ $nama }}" id="pfAvatarImg">
                    @else
                        <span id="pfAvatarInitial" aria-hidden="true">{{ $inisial }}</span>
                    @endif

                    <label for="pfAvatarInput" class="pf-avatar-cam" title="Ganti foto profil" aria-label="Ganti foto profil">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    </label>
                </div>
                <input type="file" name="avatar" id="pfAvatarInput" class="pf-file-input" accept="image/jpeg,image/png,image/webp" hidden>
            </form>

            <div class="pf-profile-meta">
                <h2 class="pf-profile-name">{{ $nama }}</h2>
                <ul class="pf-profile-list">
                    <li><span class="pf-muted">NISN</span><strong>{{ $val($pendaftaran?->nisn) }}</strong></li>
                    <li><span class="pf-muted">Jurusan</span><strong>{{ $val($pendaftaran?->jurusan_pilihan) }}</strong></li>
                    <li><span class="pf-muted">Status</span><span class="pf-pill pf-pill--{{ $statusClass }}">{{ $statusLabel }}</span></li>
                </ul>
            </div>
        </div>

        <p class="pf-upload-hint" id="pfUploadError" role="alert" hidden></p>

        @if ($user->avatar)
            <form action="{{ route('profil.avatar.destroy') }}" method="POST" class="pf-avatar-remove" onsubmit="return confirm('Hapus foto profil?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="pf-link-btn">Hapus foto profil</button>
            </form>
        @else
            <p class="pf-upload-hint">Klik ikon kamera untukunggah foto profil (JPG/PNG/WebP, maks 512&nbsp;KB).</p>
        @endif
    </section>

    <!-- Tabs -->
    <div class="pf-tabs" role="tablist" aria-label="Section profil">
        <button class="pf-tab is-active" role="tab" id="pf-tab-profil" aria-controls="pf-panel-profil" aria-selected="true" data-pf-tab="profil">Profil Siswa</button>
        <button class="pf-tab" role="tab" id="pf-tab-pendaftaran" aria-controls="pf-panel-pendaftaran" aria-selected="false" tabindex="-1" data-pf-tab="pendaftaran">Informasi Pendaftaran</button>
        <button class="pf-tab" role="tab" id="pf-tab-keluarga" aria-controls="pf-panel-keluarga" aria-selected="false" tabindex="-1" data-pf-tab="keluarga">Data Anggota Keluarga</button>
    </div>

    <!-- Panel: Profil -->
    <div class="pf-panel" role="tabpanel" id="pf-panel-profil" aria-labelledby="pf-tab-profil" data-pf-panel="profil">
        <h2 class="pf-block-title">Informasi Akademik</h2>
        <div class="pf-grid">
            <article class="pf-stat">
                <span class="pf-stat-label">Jurusan</span>
                <span class="pf-stat-value">{{ $val($pendaftaran?->jurusan_pilihan) }}</span>
                <button type="button" class="pf-stat-link" data-pf-goto="pendaftaran">Lihat detail</button>
            </article>
            <article class="pf-stat">
                <span class="pf-stat-label">Nilai Rata-rata</span>
                <span class="pf-stat-value">{{ $val($pendaftaran?->rata_rata_nilai) }}</span>
                <button type="button" class="pf-stat-link" data-pf-goto="pendaftaran">Lihat detail</button>
            </article>
            <article class="pf-stat">
                <span class="pf-stat-label">Status</span>
                <span class="pf-stat-value">{{ $statusLabel }}</span>
                <button type="button" class="pf-stat-link" data-pf-goto="pendaftaran">Lihat detail</button>
            </article>
            <article class="pf-stat">
                <span class="pf-stat-label">Gelombang</span>
                <span class="pf-stat-value">{{ $val($pendaftaran?->gelombang) }}</span>
                <button type="button" class="pf-stat-link" data-pf-goto="pendaftaran">Lihat detail</button>
            </article>
        </div>

        <section class="pf-card">
            <div class="pf-card-head">
                <h3>Informasi Pribadi</h3>
                @if ($canEdit)
                    <a href="{{ route('dashboard.siswa') }}#edit-section" class="pf-card-pencil pf-card-pencil--inline" title="Edit data" aria-label="Edit data pribadi">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                    </a>
                @endif
            </div>
            <div class="pf-rows">
                <div class="pf-row"><span class="pf-row-label">Nama Lengkap</span><span class="pf-row-value">{{ $nama }}</span></div>
                <div class="pf-row"><span class="pf-row-label">NISN</span><span class="pf-row-value">{{ $val($pendaftaran?->nisn) }}</span></div>
                <div class="pf-row"><span class="pf-row-label">NIK</span><span class="pf-row-value">{{ $val($pendaftaran?->nik) }}</span></div>
                <div class="pf-row"><span class="pf-row-label">Tempat, Tanggal Lahir</span><span class="pf-row-value">{{ $pendaftaran?->tempat_lahir ? $pendaftaran->tempat_lahir . ', ' . $tgl($pendaftaran->tanggal_lahir) : '-' }}</span></div>
                <div class="pf-row"><span class="pf-row-label">Jenis Kelamin</span><span class="pf-row-value">{{ $val($pendaftaran?->jenis_kelamin) }}</span></div>
                <div class="pf-row"><span class="pf-row-label">Agama</span><span class="pf-row-value">{{ $val($pendaftaran?->agama) }}</span></div>
                <div class="pf-row"><span class="pf-row-label">Kewarganegaraan</span><span class="pf-row-value">{{ $val($pendaftaran?->kewarnegaraan) }}</span></div>
                <div class="pf-row"><span class="pf-row-label">No. HP</span><span class="pf-row-value">{{ $val($pendaftaran?->no_hp ?? $user->username) }}</span></div>
                <div class="pf-row"><span class="pf-row-label">Email</span><span class="pf-row-value">{{ $pendaftaran?->email ?: $user->email }}</span></div>
                <div class="pf-row"><span class="pf-row-label">Alamat</span><span class="pf-row-value">{{ $pendaftaran?->alamat ?: '-' }}{{ $pendaftaran?->rt_rw ? ', ' . $pendaftaran->rt_rw : '' }}{{ $pendaftaran?->kode_pos ? ', ' . $pendaftaran->kode_pos : '' }}</span></div>
                <div class="pf-row"><span class="pf-row-label">Username</span><span class="pf-row-value">{{ $user->username }}</span></div>
                <div class="pf-row"><span class="pf-row-label">Terdaftar Pada</span><span class="pf-row-value">{{ $tgl($pendaftaran?->created_at) }}</span></div>
            </div>
        </section>
    </div>

    <!-- Panel: Pendaftaran -->
    <div class="pf-panel" role="tabpanel" id="pf-panel-pendaftaran" aria-labelledby="pf-tab-pendaftaran" data-pf-panel="pendaftaran" hidden>
        @if ($pendaftaran)
            <section class="pf-card">
                <div class="pf-card-head"><h3>Data Sekolah &amp; Seleksi</h3></div>
                <div class="pf-rows">
                    <div class="pf-row"><span class="pf-row-label">Asal Sekolah</span><span class="pf-row-value">{{ $val($pendaftaran->asal_sekolah) }}</span></div>
                    <div class="pf-row"><span class="pf-row-label">Gelombang</span><span class="pf-row-value">{{ $val($pendaftaran->gelombang) }}</span></div>
                    <div class="pf-row"><span class="pf-row-label">Tahun Lulus</span><span class="pf-row-value">{{ $val($pendaftaran->tahun_lulus) }}</span></div>
                    <div class="pf-row"><span class="pf-row-label">Rata-rata Nilai</span><span class="pf-row-value">{{ $val($pendaftaran->rata_rata_nilai) }}</span></div>
                    <div class="pf-row"><span class="pf-row-label">Jurusan Pilihan</span><span class="pf-row-value">{{ $val($pendaftaran->jurusan_pilihan) }}</span></div>
                    <div class="pf-row"><span class="pf-row-label">Kategori Pendaftar</span><span class="pf-row-value">{{ $val($pendaftaran->kategori_pendaftar) }}</span></div>
                    <div class="pf-row"><span class="pf-row-label">Jenis Pembayaran</span><span class="pf-row-value">{{ $val($pendaftaran->jenis_pembayaran) }}</span></div>
                    <div class="pf-row"><span class="pf-row-label">Berkas Tambahan</span><span class="pf-row-value">{{ $val($pendaftaran->berkas_tambahan) }}</span></div>
                </div>
            </section>

            <section class="pf-card">
                <div class="pf-card-head"><h3>Status Pendaftaran</h3></div>
                <div class="pf-rows">
                    <div class="pf-row"><span class="pf-row-label">Status</span><span class="pf-row-value"><span class="pf-pill pf-pill--{{ $statusClass }}">{{ $statusLabel }}</span></span></div>
                    <div class="pf-row"><span class="pf-row-label">Tanggal Daftar</span><span class="pf-row-value">{{ $dtFull($pendaftaran->created_at) }}</span></div>
                    <div class="pf-row"><span class="pf-row-label">Terakhir Diperbarui</span><span class="pf-row-value">{{ $dtFull($pendaftaran->status_updated_at ?: $pendaftaran->updated_at) }}</span></div>
                    <div class="pf-row"><span class="pf-row-label">Data Dikonfirmasi</span><span class="pf-row-value">{{ $pendaftaran->data_confirmed ? 'Sudah' : 'Belum' }}</span></div>
                </div>
            </section>

            <section class="pf-card">
                <div class="pf-card-head"><h3>Berkas Upload</h3></div>
                <div class="pf-rows">
                    @foreach ($berkas as $label => $file)
                        <div class="pf-row">
                            <span class="pf-row-label">{{ $label }}</span>
                            <span class="pf-row-value pf-file-state {{ $file ? 'is-ok' : 'is-empty' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                    @if ($file)
                                        <path d="M20 6L9 17l-5-5"/>
                                    @else
                                        <circle cx="12" cy="12" r="9"/><path d="M15 9l-6 6M9 9l6 6"/>
                                    @endif
                                </svg>
                                {{ $file ? 'Terunggah' : 'Belum diunggah' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </section>
        @else
            <div class="pf-card pf-card--empty">
                <p>Kamu belum mengisi formulir pendaftaran. Lengkapi data dulu supaya profil dan status pendaftaran bisa tampil di sini.</p>
                <a class="pf-btn" href="{{ route('pendaftaran.create') }}">Isi Formulir Pendaftaran</a>
            </div>
        @endif
    </div>

    <!-- Panel: Keluarga -->
    <div class="pf-panel" role="tabpanel" id="pf-panel-keluarga" aria-labelledby="pf-tab-keluarga" data-pf-panel="keluarga" hidden>
        @if ($pendaftaran)
            <section class="pf-card">
                <div class="pf-card-head"><h3>Data Keluarga</h3></div>
                <div class="pf-rows">
                    <div class="pf-row"><span class="pf-row-label">Jumlah Saudara</span><span class="pf-row-value">{{ $val($pendaftaran->jumlah_saudara) }}</span></div>
                    <div class="pf-row"><span class="pf-row-label">Anak Ke</span><span class="pf-row-value">{{ $val($pendaftaran->anak_ke) }}</span></div>
                    <div class="pf-row"><span class="pf-row-label">Status Keluarga</span><span class="pf-row-value">{{ $val($pendaftaran->status_keluarga) }}</span></div>
                    <div class="pf-row"><span class="pf-row-label">Email Orang Tua</span><span class="pf-row-value">{{ $val($pendaftaran->email_orang_tua) }}</span></div>
                </div>
            </section>

            <div class="pf-duo">
                <section class="pf-card">
                    <div class="pf-card-head"><h3>Ayah</h3></div>
                    <div class="pf-rows">
                        <div class="pf-row"><span class="pf-row-label">Nama</span><span class="pf-row-value">{{ $val($pendaftaran->nama_ayah) }}</span></div>
                        @foreach ($ayah as $label => $value)
                            <div class="pf-row"><span class="pf-row-label">{{ $label }}</span><span class="pf-row-value">{{ $val($value) }}</span></div>
                        @endforeach
                    </div>
                </section>

                <section class="pf-card">
                    <div class="pf-card-head"><h3>Ibu</h3></div>
                    <div class="pf-rows">
                        <div class="pf-row"><span class="pf-row-label">Nama</span><span class="pf-row-value">{{ $val($pendaftaran->nama_ibu) }}</span></div>
                        @foreach ($ibu as $label => $value)
                            <div class="pf-row"><span class="pf-row-label">{{ $label }}</span><span class="pf-row-value">{{ $val($value) }}</span></div>
                        @endforeach
                    </div>
                </section>
            </div>

            <section class="pf-card">
                <div class="pf-card-head"><h3>Wali</h3></div>
                <div class="pf-rows">
                    <div class="pf-row"><span class="pf-row-label">Nama Wali</span><span class="pf-row-value">{{ $val($pendaftaran->nama_wali) }}</span></div>
                    <div class="pf-row"><span class="pf-row-label">Hubungan</span><span class="pf-row-value">{{ $val($pendaftaran->hubungan_wali) }}</span></div>
                </div>
            </section>
        @else
            <div class="pf-card pf-card--empty">
                <p>Data keluarga akan tampil di sini setelah formulir pendaftaran kamu diisi.</p>
                <a class="pf-btn" href="{{ route('pendaftaran.create') }}">Isi Formulir Pendaftaran</a>
            </div>
        @endif
    </div>

    <div class="pf-footer">
        <a class="pf-btn pf-btn--ghost" href="{{ route('dashboard.siswa') }}">Dashboard Siswa</a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="pf-btn pf-btn--logout">Logout</button>
        </form>
    </div>
</div>

<style>
.pf {
    --pf-primary: #3a6450;
    --pf-primary-dark: #2f5b45;
    --pf-text: #1c2a23;
    --pf-text-soft: #647067;
    --pf-border: #e6ebe4;
    --pf-bg: #f4f7f2;
    --pf-radius: 18px;
    --pf-shadow: 0 8px 24px rgba(28, 42, 35, 0.07);

    max-width: 880px;
    margin: 0 auto;
    padding: 24px 20px 48px;
    color: var(--pf-text);
}

/* Header */
.pf-header-title {
    margin: 0 0 18px;
    font-size: 20px;
    font-weight: 800;
    text-align: center;
    letter-spacing: -0.01em;
}

/* Alert */
.pf-alert {
    padding: 13px 16px;
    border-radius: 14px;
    font-size: 13.5px;
    font-weight: 600;
    margin-bottom: 16px;
    border: 1px solid transparent;
}
.pf-alert--ok { background: #e8f2ea; color: #24503a; border-color: #cfe3d4; }
.pf-alert--err { background: #fdf1f0; color: #99332b; border-color: #f0cdc9; }

/* Card */
.pf-card {
    position: relative;
    background: #fff;
    border: 1px solid var(--pf-border);
    border-radius: var(--pf-radius);
    box-shadow: var(--pf-shadow);
    padding: 20px;
    margin-bottom: 16px;
}
.pf-card--profile { padding: 22px; }
.pf-card-head {
    display: flex; align-items: center; justify-content: space-between; gap: 12px;
    margin-bottom: 12px;
}
.pf-card-head h3 { margin: 0; font-size: 15px; font-weight: 800; letter-spacing: -0.01em; }
.pf-card-pencil {
    position: absolute; top: 18px; right: 18px;
    width: 34px; height: 34px;
    display: flex; align-items: center; justify-content: center;
    border-radius: 10px;
    background: var(--pf-bg);
    color: var(--pf-primary);
    border: 1px solid var(--pf-border);
    text-decoration: none;
    transition: all 0.2s ease;
}
.pf-card-pencil svg { width: 16px; height: 16px; }
.pf-card-pencil:hover { background: var(--pf-primary); color: #fff; border-color: var(--pf-primary); }
.pf-card-pencil--inline { position: static; }

/* Profile card */
.pf-profile-top { display: flex; align-items: center; gap: 20px; }
.pf-avatar-form { margin: 0; flex-shrink: 0; }
.pf-avatar {
    position: relative;
    width: 96px; height: 96px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2f5b45, #4a7a62);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 36px; font-weight: 800;
    box-shadow: 0 8px 20px rgba(58, 100, 80, 0.28);
    overflow: visible;
}
.pf-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; display: block; }
.pf-avatar-cam {
    position: absolute;
    right: -2px; bottom: -2px;
    width: 32px; height: 32px;
    border-radius: 50%;
    background: #fff;
    color: var(--pf-primary);
    border: 1px solid var(--pf-border);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(28, 42, 35, 0.16);
    transition: all 0.2s ease;
}
.pf-avatar-cam svg { width: 15px; height: 15px; }
.pf-avatar-cam:hover { background: var(--pf-primary); color: #fff; }
.pf-avatar.is-loading { opacity: 0.6; }

.pf-profile-meta { min-width: 0; }
.pf-profile-name {
    margin: 0 0 8px;
    font-size: 22px;
    font-weight: 800;
    letter-spacing: -0.02em;
    line-height: 1.2;
    word-break: break-word;
    padding-right: 40px;
}
.pf-profile-list { list-style: none; margin: 0; padding: 0; display: grid; gap: 5px; }
.pf-profile-list li { display: flex; align-items: center; gap: 8px; font-size: 13.5px; }
.pf-profile-list strong { font-weight: 700; }
.pf-muted { color: var(--pf-text-soft); min-width: 58px; }

.pf-upload-hint { margin: 14px 0 0; font-size: 12.5px; color: var(--pf-text-soft); line-height: 1.6; }
#pfUploadError:not([hidden]) { color: #99332b; font-weight: 700; }
.pf-avatar-remove { margin-top: 10px; }
.pf-link-btn {
    background: none; border: none; padding: 0;
    color: #99332b; font-size: 12.5px; font-weight: 700;
    cursor: pointer; text-decoration: underline; text-underline-offset: 3px;
}
.pf-link-btn:hover { color: #7a2820; }

/* Pill status */
.pf-pill {
    display: inline-block;
    padding: 3px 11px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
}
.pf-pill--baru { background: #eef1f0; color: #4c5a63; }
.pf-pill--diproses { background: #fff3d6; color: #8a6d1a; }
.pf-pill--diterima { background: #d4edda; color: #155724; }
.pf-pill--ditolak { background: #f8d7da; color: #842029; }
.pf-pill--kosong { background: #f1f3f1; color: #7a857e; }

/* Tabs */
.pf-tabs {
    display: flex;
    gap: 4px;
    background: #fff;
    border: 1px solid var(--pf-border);
    border-radius: 14px;
    padding: 5px;
    margin-bottom: 18px;
    box-shadow: var(--pf-shadow);
}
.pf-tab {
    flex: 1;
    padding: 10px 8px;
    border: none;
    background: none;
    border-radius: 10px;
    font-size: 13.5px;
    font-weight: 700;
    color: var(--pf-text-soft);
    cursor: pointer;
    font-family: inherit;
    transition: all 0.2s ease;
}
.pf-tab:hover { color: var(--pf-primary); background: var(--pf-bg); }
.pf-tab.is-active { background: var(--pf-primary); color: #fff; box-shadow: 0 4px 12px rgba(58, 100, 80, 0.28); }

/* Kartu statistik */
.pf-block-title {
    font-size: 12px;
    font-weight: 800;
    color: var(--pf-primary);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin: 0 0 10px 2px;
}
.pf-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 16px;
}
.pf-stat {
    background: #2f5b45;
    color: #fff;
    border-radius: var(--pf-radius);
    padding: 16px 14px;
    min-height: 128px;
    display: flex;
    flex-direction: column;
    box-shadow: 0 8px 20px rgba(58, 100, 80, 0.22);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.pf-stat:nth-child(2) { background: linear-gradient(150deg, #2f5b45 0%, #437159 100%); }
.pf-stat:nth-child(3) { background: linear-gradient(150deg, #4a7a62 0%, #639a80 100%); }
.pf-stat:nth-child(4) { background: linear-gradient(150deg, #35604a 0%, #4c7f65 100%); }
.pf-stat:hover { transform: translateY(-3px); box-shadow: 0 14px 28px rgba(58, 100, 80, 0.26); }
.pf-stat-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    opacity: 0.85;
}
.pf-stat-value {
    font-size: 20px;
    font-weight: 800;
    margin: 8px 0 auto;
    letter-spacing: -0.02em;
    line-height: 1.2;
    word-break: break-word;
}
.pf-stat-link {
    margin-top: 12px;
    align-self: flex-start;
    background: rgba(255, 255, 255, 0.16);
    border: none;
    color: #fff;
    font-family: inherit;
    font-size: 11.5px;
    font-weight: 700;
    padding: 5px 10px;
    border-radius: 999px;
    cursor: pointer;
    transition: background 0.2s ease;
}
.pf-stat-link:hover { background: rgba(255, 255, 255, 0.3); }

/* Rows */
.pf-rows { display: grid; }
.pf-row {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 16px;
    padding: 11px 0;
    border-bottom: 1px solid #f1f4f0;
}
.pf-row:last-child { border-bottom: none; }
.pf-row-label { font-size: 13px; color: var(--pf-text-soft); font-weight: 600; flex-shrink: 0; }
.pf-row-value { font-size: 13.5px; font-weight: 700; text-align: right; word-break: break-word; }

.pf-file-state { display: inline-flex; align-items: center; gap: 6px; }
.pf-file-state svg { width: 15px; height: 15px; }
.pf-file-state.is-ok { color: #2c6b46; }
.pf-file-state.is-empty { color: #8a938c; font-weight: 600; }

.pf-duo { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.pf-duo .pf-card { margin-bottom: 16px; }

/* Empty & footer */
.pf-card--empty { text-align: center; padding: 32px 24px; }
.pf-card--empty p { margin: 0 0 18px; color: var(--pf-text-soft); font-size: 14px; line-height: 1.7; }

.pf-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid var(--pf-border);
    flex-wrap: wrap;
}
.pf-btn {
    display: inline-block;
    padding: 12px 22px;
    background: var(--pf-primary);
    color: #fff;
    border-radius: 13px;
    font-weight: 700;
    font-size: 13.5px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    font-family: inherit;
    box-shadow: 0 6px 16px rgba(58, 100, 80, 0.2);
    transition: all 0.2s ease;
}
.pf-btn:hover { background: var(--pf-primary-dark); color: #fff; transform: translateY(-2px); }
.pf-btn--ghost { background: #fff; color: var(--pf-primary); border: 1px solid var(--pf-border); box-shadow: none; }
.pf-btn--ghost:hover { background: var(--pf-bg); color: var(--pf-primary-dark); }
.pf-btn--logout { background: #fff; color: #b3362c; border: 1px solid #e6c3bf; box-shadow: none; }
.pf-btn--logout:hover { background: #fdf1f0; color: #99332b; }

@media (max-width: 760px) {
    .pf-grid { grid-template-columns: repeat(2, 1fr); }
    .pf-duo { grid-template-columns: 1fr; }
}
@media (max-width: 520px) {
    .pf { padding: 18px 14px 40px; }
    .pf-profile-top { flex-direction: column; text-align: center; }
    .pf-profile-name { padding-right: 0; font-size: 19px; }
    .pf-profile-list { justify-items: center; }
    .pf-profile-list li { justify-content: center; }
    .pf-muted { min-width: auto; }
    .pf-row { flex-direction: column; align-items: flex-start; gap: 3px; }
    .pf-row-value { text-align: left; }
    .pf-stat { min-height: 112px; padding: 14px 12px; }
    .pf-stat-value { font-size: 17px; }
    .pf-footer { flex-direction: column-reverse; }
    .pf-footer .pf-btn { width: 100%; text-align: center; }
}
</style>

<script>
(function () {
    var tabs = Array.prototype.slice.call(document.querySelectorAll('[data-pf-tab]'));
    var panels = Array.prototype.slice.call(document.querySelectorAll('[data-pf-panel]'));

    function activate(key, moveFocus) {
        tabs.forEach(function (tab) {
            var on = tab.dataset.pfTab === key;
            tab.classList.toggle('is-active', on);
            tab.setAttribute('aria-selected', on ? 'true' : 'false');
            tab.tabIndex = on ? 0 : -1;
            if (on && moveFocus) { tab.focus(); }
        });
        panels.forEach(function (panel) {
            panel.hidden = panel.dataset.pfPanel !== key;
        });
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () { activate(tab.dataset.pfTab, false); });
        tab.addEventListener('keydown', function (event) {
            var index = tabs.indexOf(tab);
            var next = null;
            if (event.key === 'ArrowRight') { next = tabs[(index + 1) % tabs.length]; }
            if (event.key === 'ArrowLeft') { next = tabs[(index - 1 + tabs.length) % tabs.length]; }
            if (next) { event.preventDefault(); activate(next.dataset.pfTab, true); }
        });
    });

    document.querySelectorAll('[data-pf-goto]').forEach(function (button) {
        button.addEventListener('click', function () { activate(button.dataset.pfGoto, true); });
    });

    var input = document.getElementById('pfAvatarInput');
    var avatar = document.getElementById('pfAvatar');
    var error = document.getElementById('pfUploadError');
    var allowed = ['image/jpeg', 'image/png', 'image/webp'];
    var maxBytes = 512 * 1024;

    if (input && avatar) {
        input.addEventListener('change', function () {
            var file = input.files && input.files[0];
            if (!file) { return; }

            if (allowed.indexOf(file.type) === -1 || file.size > maxBytes) {
                if (error) {
                    error.textContent = 'Foto harus format JPG, PNG, atau WebP dan maksimal 512 KB.';
                    error.hidden = false;
                }
                input.value = '';
                return;
            }

            if (error) { error.hidden = true; }

            var reader = new FileReader();
            reader.onload = function (event) {
                var target = document.getElementById('pfAvatarImg');
                if (!target) {
                    target = document.createElement('img');
                    target.id = 'pfAvatarImg';
                    target.alt = 'Pratinjau foto profil';
                    var initial = document.getElementById('pfAvatarInitial');
                    if (initial) { initial.remove(); }
                    avatar.insertBefore(target, avatar.firstChild);
                }
                target.src = event.target.result;
            };
            reader.readAsDataURL(file);

            avatar.classList.add('is-loading');
            avatar.closest('form').submit();
        });
    }
})();
</script>
@endsection
