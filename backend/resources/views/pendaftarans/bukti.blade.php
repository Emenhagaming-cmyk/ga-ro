<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { margin: 36px 44px; color: #1c2a23; font-size: 12px; line-height: 1.65; }
        .kop { text-align: center; }
        .kop img { width: 64px; height: 64px; object-fit: contain; }
        .kop .yayasan { font-size: 11px; letter-spacing: 0.14em; text-transform: uppercase; color: #3a6450; }
        .kop .nama { font-size: 22px; font-weight: bold; letter-spacing: 0.02em; }
        .kop .alamat { font-size: 10px; color: #555; }
        .garis { border-top: 2px solid #3a6450; margin: 10px 0 4px; }
        .garis2 { border-top: 1px solid #3a6450; margin-bottom: 26px; }
        .judul { text-align: center; font-size: 15px; font-weight: bold; text-decoration: underline; margin: 0 0 4px; }
        .nomor { text-align: center; font-size: 11px; margin-bottom: 20px; }
        .isi { text-align: justify; }
        .tabel { width: 100%; margin: 14px 0; border-collapse: collapse; }
        .tabel td { padding: 6px 10px; border: 1px solid #bbb; font-size: 12px; }
        .tabel td.k { width: 42%; background: #f2f6f1; font-weight: bold; }
        .ttd { float: right; width: 250px; text-align: center; margin-top: 26px; }
        .ttd p { margin: 2px 0; font-size: 12px; }
        .ttd .nama { font-weight: bold; text-decoration: underline; margin-top: 64px; }
    </style>
</head>
<body>
    <div class="kop">
        <img src="{{ public_path('logo.png') }}" alt="Logo Sekolah">
        <div class="yayasan">Yayasan Pendidikan Bahrul Ulum</div>
        <div class="nama">SMK BAHRUL ULUM SURABAYA</div>
        <div class="alamat">Surabaya, Jawa Timur &bull; info@smk-bahrululum.sch.id</div>
    </div>
    <div class="garis"></div>
    <div class="garis2"></div>

    <p class="judul">SURAT KETERANGAN DITERIMA</p>
    <p class="nomor">Nomor: 421.5/{{ $pendaftaran->id }}/SMK-BU/{{ now()->year }}</p>

    @php
        $tahunAjaran = now()->month >= 6 ? now()->year : now()->year - 1;
        $tanggalSurat = \Carbon\Carbon::parse($pendaftaran->confirmed_at ?? now())->isoFormat('D MMMM YYYY');
    @endphp

    <div class="isi">
        <p>Berdasarkan hasil seleksi penerimaan peserta didik baru, yang bertanda tangan di bawah ini Kepala SMK Bahrul Ulum Surabaya menerangkan bahwa:</p>

        <table class="tabel">
            <tr><td class="k">Nama Lengkap</td><td>{{ $pendaftaran->nama_lengkap }}</td></tr>
            <tr><td class="k">NISN</td><td>{{ $pendaftaran->nisn }}</td></tr>
            <tr><td class="k">Tempat, Tanggal Lahir</td><td>{{ $pendaftaran->tempat_lahir }}, {{ $pendaftaran->tanggal_lahir ? \Carbon\Carbon::parse($pendaftaran->tanggal_lahir)->isoFormat('D MMMM YYYY') : '-' }}</td></tr>
            <tr><td class="k">Asal Sekolah</td><td>{{ $pendaftaran->asal_sekolah }}</td></tr>
            <tr><td class="k">Jurusan yang Dipilih</td><td>{{ $pendaftaran->jurusan_pilihan }}</td></tr>
        </table>

        <p>Berdasarkan data tersebut, nama di atas <strong>dinyatakan DITERIMA</strong> sebagai peserta didik baru SMK Bahrul Ulum Surabaya Tahun Ajaran {{ $tahunAjaran }}/{{ $tahunAjaran + 1 }}.</p>

        <p>Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.</p>
    </div>

    <div class="ttd">
        <p>Surabaya, {{ $tanggalSurat }}</p>
        <p>Kepala Sekolah,</p>
        <p class="nama">Pak Rojib</p>
        <p>NIP. 200928</p>
    </div>
</body>
</html>