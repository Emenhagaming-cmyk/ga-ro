<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 0; padding: 0; }
        .card { width: 226px; height: 157px; border: 1px solid #000; padding: 10px; box-sizing: border-box; }
        .header { font-size: 14px; font-weight: bold; text-align: center; margin-bottom: 5px; }
        .photo { float: right; width: 60px; height: 80px; margin-left: 5px; object-fit: cover; }
        .field { font-size: 10px; margin: 2px 0; }
    </style>
</head>
<body>
<div class="card">
    <div class="header">Kartu Peserta SMK Bahrul Ulum</div>
    @if($pendaftaran->foto_3x4)
        <img src="{{ storage_path('app/public/' . $pendaftaran->foto_3x4) }}" class="photo" alt="Foto 3x4">
    @endif
    <div class="field">Nama: {{ $pendaftaran->nama_lengkap }}</div>
    <div class="field">NISN: {{ $pendaftaran->nisn }}</div>
    <div class="field">Jurusan: {{ $pendaftaran->jurusan_pilihan }}</div>
    <div class="field">Status: {{ ucfirst($pendaftaran->status) }}</div>
</div>
</body>
</html>