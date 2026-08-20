@extends('layouts.app')

@section('title', 'Data Pendaftar SPMB')
@section('page-title', 'Data Pendaftar')

@section('content')
<div class="dashboard-shell">
    <div class="dashboard-header">
        <div>
            <p class="dashboard-kicker">Data Pendaftar</p>
            <h1 class="form-title">Semua Data Pendaftar</h1>
            <p class="form-subtitle">Kelola status dan hapus data pendaftaran yang masuk.</p>
        </div>
        <a href="{{ route('pendaftaran.export') }}" class="btn btn-primary" style="text-decoration:none;">⬇ Export CSV</a>
    </div>

    <form method="GET" action="{{ route('pendaftaran.index') }}" class="filter-bar">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, NISN, NIK, sekolah, no. HP..." style="max-width:320px;">
        <select name="status" style="max-width:160px;">
            <option value="">Semua Status</option>
            @foreach (['baru', 'diproses', 'diterima', 'ditolak'] as $s)
            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <select name="jurusan" style="max-width:160px;">
            <option value="">Semua Jurusan</option>
            @foreach (['RPL', 'TKJ', 'AKL'] as $j)
            <option value="{{ $j }}" {{ request('jurusan') === $j ? 'selected' : '' }}>{{ $j }}</option>
            @endforeach
        </select>
        <label class="filter-duplicate">
            <input type="checkbox" name="duplikat" value="1" {{ request('duplikat') ? 'checked' : '' }}>
            Duplikat NISN/NIK
        </label>
        <button type="submit" class="btn btn-primary" style="padding:10px 20px;">Filter</button>
        @if (request()->anyFilled(['q', 'status', 'jurusan']) || request('duplikat'))
        <a href="{{ route('pendaftaran.index') }}" class="btn btn-secondary" style="padding:10px 20px;text-decoration:none;">Reset</a>
        @endif
    </form>

    <div class="form-section">
        @if (session('success'))
        <div class="alert alert-success">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if ($pendaftarans->isEmpty())
        <div class="empty-state">
            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#9ba8a0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 12px;">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="9" y1="13" x2="15" y2="13"></line>
            </svg>
            <p>Belum ada data pendaftaran</p>
            <p style="font-size:13px;color:#9ba8a0;">Data muncul otomatis saat pendaftar mengisi formulir di web utama.</p>
            <a href="{{ env('FRONTEND_URL', 'https://bhapppp.vercel.app') }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="text-decoration:none;">Buka Web Utama</a>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th class="hide-sm">No. HP</th>
                        <th class="hide-sm">Asal Sekolah</th>
                        <th>Jurusan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pendaftarans as $item)
                        <tr data-id="{{ $item->id }}">
                            <td>{{ $loop->iteration + ($pendaftarans->currentPage() - 1) * $pendaftarans->perPage() }}</td>
                            <td><strong>{{ $item->nama_lengkap }}</strong>
                                @if ($duplicateNisn->contains($item->nisn))
                                <span class="badge badge-danger" title="NISN terdaftar lebih dari satu kali">NISN ganda</span>
                                @endif
                                @if ($duplicateNik->contains($item->nik))
                                <span class="badge badge-danger" title="NIK terdaftar lebih dari satu kali">NIK ganda</span>
                                @endif
                            </td>
                            <td class="hide-sm">{{ $item->no_hp }}</td>
                            <td class="hide-sm">{{ $item->asal_sekolah }}</td>
                            <td><span style="font-weight: 600; color: #3a6450;">{{ $item->jurusan_pilihan }}</span></td>
                            <td>
                                @php
                                    $statusClass = match($item->status) {
                                        'diterima' => 'badge-success',
                                        'ditolak' => 'badge-danger',
                                        'diproses' => 'badge-warning',
                                        default => 'badge-info'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ ucfirst($item->status) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('pendaftaran.show', $item) }}" class="action-btn action-btn-view">Lihat</a>
                                <form action="{{ route('pendaftaran.status', $item) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="status-select" onchange="this.form.submit()" aria-label="Ubah status {{ $item->nama_lengkap }}">
                                        @foreach (['baru', 'diproses', 'diterima', 'ditolak'] as $s)
                                        <option value="{{ $s }}" {{ $item->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                        @endforeach
                                    </select>
                                </form>
                                <button type="button" class="action-btn action-btn-delete" onclick="openDeleteModal('{{ $item->id }}', '{{ $item->nama_lengkap }}')">Hapus</button>
                                <form id="delete-form-{{ $item->id }}" action="{{ route('pendaftaran.destroy', $item) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            {{ $pendaftarans->links() }}
        </div>
    @endif
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="deleteModal" class="modal-overlay" onclick="closeModalOutside(event)">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="modal-icon modal-icon-danger">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                <line x1="10" y1="11" x2="10" y2="17"></line>
                <line x1="14" y1="11" x2="14" y2="17"></line>
            </svg>
        </div>
        <h3 class="modal-title">Hapus Pendaftaran?</h3>
        <p class="modal-desc">Data pendaftar <strong id="deleteName"></strong> akan dihapus permanen dan tidak dapat dikembalikan.</p>
        <div class="modal-actions">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Batal</button>
            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Ya, Hapus</button>
        </div>
    </div>
</div>

<style>
    .dashboard-shell {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .dashboard-kicker {
        display: inline-block;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.16em;
        color: #3a6450;
        margin-bottom: 6px;
    }

    .filter-bar {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
        background: #fbfcfa;
        border: 1px solid #dfe4dd;
        border-radius: 16px;
        padding: 14px 16px;
    }

    .filter-bar input[type="text"], .filter-bar select {
        width: auto;
        margin: 0;
    }

    @media (max-width: 768px) {
        .filter-bar input[type="text"],
        .filter-bar select {
            width: 100%;
            max-width: none !important;
        }

        .filter-duplicate {
            width: 100%;
            justify-content: flex-start;
        }
    }

    .filter-duplicate {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin: 0;
        font-size: 13px;
        font-weight: 700;
        color: #475449;
        text-transform: none;
        letter-spacing: 0;
    }

    .filter-duplicate input[type="checkbox"] {
        width: auto;
        margin: 0;
    }

    .badge {
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: capitalize;
        display: inline-block;
    }
    .badge-success { background: #e8f0e6; color: #2a5238; }
    .badge-danger { background: #fef2f2; color: #991b1b; }
    .badge-warning { background: #fff3e0; color: #b45309; }
    .badge-info { background: #e8eef6; color: #3a4a6b; }

    .status-select {
        width: auto;
        appearance: auto;
        padding: 5px 10px;
        font-size: 11px;
        font-weight: 700;
        border-radius: 8px;
        border: 1px solid #dfe4dd;
        background: #fbfcfa;
        color: #475449;
        cursor: pointer;
        vertical-align: middle;
        margin-right: 4px;
    }
    .status-select:hover {
        border-color: #3a6450;
    }

    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(28, 42, 35, 0.55);
        backdrop-filter: blur(6px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        transition: opacity 0.25s ease;
    }
    .modal-overlay.active {
        display: flex;
        opacity: 1;
    }

    .modal-box {
        background: #fbfcfa;
        border: 1px solid #dfe4dd;
        border-radius: 22px;
        padding: 32px;
        max-width: 400px;
        width: 90%;
        text-align: center;
        box-shadow: 0 24px 60px rgba(28, 42, 35, 0.25);
        transform: scale(0.92);
        transition: transform 0.25s ease;
    }
    .modal-overlay.active .modal-box {
        transform: scale(1);
    }

    .modal-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 18px;
    }
    .modal-icon-danger {
        background: #fef2f2;
        color: #dc2626;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 800;
        color: #1c2a23;
        margin-bottom: 8px;
        letter-spacing: -0.02em;
    }

    .modal-desc {
        font-size: 13px;
        color: #647067;
        line-height: 1.7;
        margin-bottom: 24px;
    }

    .modal-desc strong {
        color: #1c2a23;
        font-weight: 700;
    }

    .modal-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
    }

    .btn-danger {
        background: #dc2626;
        color: #fff;
        box-shadow: 0 8px 18px rgba(220, 38, 38, 0.2);
    }
    .btn-danger:hover {
        background: #b91c1c;
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(220, 38, 38, 0.3);
    }

    @media (max-width: 768px) {
        .modal-actions { flex-direction: column-reverse; }
        .modal-actions .btn { width: 100%; }
    }
</style>

<script>
    let deleteTargetId = null;

    function openDeleteModal(id, name) {
        deleteTargetId = id;
        document.getElementById('deleteName').textContent = name;
        const modal = document.getElementById('deleteModal');
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('active');
        document.body.style.overflow = '';
        deleteTargetId = null;
    }

    function closeModalOutside(e) {
        if (e.target.id === 'deleteModal') {
            closeDeleteModal();
        }
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (deleteTargetId) {
            document.getElementById('delete-form-' + deleteTargetId).submit();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDeleteModal();
    });
</script>
@endsection