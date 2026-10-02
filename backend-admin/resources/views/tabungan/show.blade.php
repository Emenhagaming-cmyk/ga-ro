@extends('layouts.app')

@section('title', 'Detail Tabungan - Panel Admin')
@section('page-title', 'Detail Tabungan')

@section('content')
<div class="dashboard-shell">
    <div class="dashboard-header">
        <div>
            <p class="dashboard-kicker">Tabungan Siswa</p>
            <h1 class="form-title" fetchpriority="high">Detail Tabungan</h1>
            <p class="form-subtitle">
                Siswa: <strong>{{ $siswa->name }}</strong> ({{ $siswa->username }}) —
                Saldo: <strong style="color:#3a6450;">{{ number_format($saldo, 0, ',', '.') }}</strong>
            </p>
        </div>
        <a href="{{ route('tabungan.index') }}" class="btn btn-secondary" style="text-decoration:none;padding:8px 18px;font-size:13px;">Kembali</a>
    </div>

    <div class="form-section" style="padding:16px;overflow-x:auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:12px;">
            <h2 style="font-size:17px;font-weight:800;color:#1c2a23;">Riwayat Transaksi</h2>
            <button onclick="document.getElementById('addForm').style.display='flex'" class="btn btn-primary" style="padding:8px 18px;font-size:13px;">+ Tambah Transaksi</button>
        </div>

        <div id="addForm" style="display:none;gap:10px;align-items:flex-end;background:#f8faf9;padding:14px;border-radius:12px;margin-bottom:14px;border:1px solid #e5eae4;">
            <form method="POST" action="{{ route('tabungan.store') }}" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;width:100%;">
                @csrf
                <input type="hidden" name="user_id" value="{{ $siswa->id }}">
                <div>
                    <label style="font-size:11px;font-weight:700;color:#647067;display:block;margin-bottom:4px;">Tipe</label>
                    <select name="type" style="padding:8px 10px;border-radius:8px;border:1px solid #dfe4dd;font-size:13px;background:#fff;">
                        <option value="setor">Setor</option>
                        <option value="tarik">Tarik</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:11px;font-weight:700;color:#647067;display:block;margin-bottom:4px;">Nominal (Rp)</label>
                    <input type="number" name="amount" min="1" required style="padding:8px 10px;border-radius:8px;border:1px solid #dfe4dd;font-size:13px;width:120px;">
                </div>
                <div style="flex:1;min-width:160px;">
                    <label style="font-size:11px;font-weight:700;color:#647067;display:block;margin-bottom:4px;">Deskripsi</label>
                    <input type="text" name="description" maxlength="255" style="padding:8px 10px;border-radius:8px;border:1px solid #dfe4dd;font-size:13px;width:100%;">
                </div>
                <button type="submit" class="btn btn-primary" style="padding:8px 18px;font-size:13px;">Simpan</button>
                <button type="button" onclick="document.getElementById('addForm').style.display='none'" class="btn btn-secondary" style="padding:8px 18px;font-size:13px;">Batal</button>
            </form>
        </div>

        @if ($tabungans->isEmpty())
            <div class="empty-state">
                <p>Belum ada transaksi tabungan untuk siswa ini.</p>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Jumlah</th>
                            <th>Deskripsi</th>
                            <th>Dicatat Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tabungans as $t)
                            <tr>
                                <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if ($t->type === 'setor')
                                        <span class="badge badge-success">Setor</span>
                                    @else
                                        <span class="badge badge-warning">Tarik</span>
                                    @endif
                                </td>
                                <td><strong>{{ number_format($t->amount, 0, ',', '.') }}</strong></td>
                                <td>{{ $t->description ?? '-' }}</td>
                                <td>{{ $t->inputter->name ?? 'Siswa' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
