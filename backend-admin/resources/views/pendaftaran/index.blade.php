@extends('layouts.app')

@section('title', 'Data Pendaftar SPMB')

@section('content')
@php
    $totalPendaftar = $stats['total'];
    $baruCount = $stats['baru'];
    $diterimaCount = $stats['diterima'];
@endphp

<div class="dashboard-shell">
    <div class="dashboard-header">
        <div>
            <p class="dashboard-kicker">Admin Dashboard</p>
            <h1 class="form-title">Pantau Data Pendaftar SPMB</h1>
            <p class="form-subtitle">Ringkasan cepat status pendaftaran dan informasi siswa yang masuk.</p>
        </div>
        <a href="{{ route('pendaftaran.export') }}" class="btn btn-primary" style="text-decoration:none;">⬇ Export CSV</a>
    </div>

    <div class="stats-grid" id="statsGrid">
        <div class="stat-card stat-card-green">
            <div class="stat-label">Total Pendaftar</div>
            <div class="stat-value" id="statTotal">{{ $totalPendaftar }}</div>
            <div class="stat-foot">Data terbaru terkelola</div>
        </div>
        <div class="stat-card stat-card-blue">
            <div class="stat-label">Baru</div>
            <div class="stat-value" id="statBaru">{{ $baruCount }}</div>
            <div class="stat-foot">Menunggu diproses</div>
        </div>
        <div class="stat-card stat-card-gold">
            <div class="stat-label">Diterima</div>
            <div class="stat-value" id="statDiterima">{{ $diterimaCount }}</div>
            <div class="stat-foot">Sudah disetujui</div>
        </div>
    </div>

    <div id="newDataBanner" style="display:none;background:#ecfdf5;color:#047857;border:1px solid #a7f3d0;border-radius:14px;padding:14px 18px;font-weight:700;">
        🔔 Data pendaftaran baru masuk — <button id="reloadNow" class="btn btn-primary" style="margin-left:8px;font-size:12px;padding:6px 14px;">Muat Ulang</button>
    </div>

    <div class="insight-card">
        <div class="insight-title">Ringkasan AI</div>
        <p>{{ $insight }}</p>
    </div>

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
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>No. HP</th>
                        <th>Asal Sekolah</th>
                        <th>Jurusan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pendaftarans as $item)
                        <tr data-id="{{ $item->id }}">
                            <td>{{ $loop->iteration + ($pendaftarans->currentPage() - 1) * $pendaftarans->perPage() }}</td>
                            <td><strong>{{ $item->nama_lengkap }}</strong></td>
                            <td>{{ $item->no_hp }}</td>
                            <td>{{ $item->asal_sekolah }}</td>
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

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .stat-card {
        padding: 20px 22px;
        border-radius: 20px;
        color: #1c2a23;
        box-shadow: 0 12px 28px rgba(28, 42, 35, 0.06);
        border: 1px solid rgba(223, 228, 221, 0.9);
    }

    .stat-card-green {
        background: linear-gradient(135deg, #edf7eb 0%, #f8fcf7 100%);
    }

    .stat-card-blue {
        background: linear-gradient(135deg, #eef5ff 0%, #f8fbff 100%);
    }

    .stat-card-gold {
        background: linear-gradient(135deg, #fff7e8 0%, #fffcf5 100%);
    }

    .stat-label {
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: #647067;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 30px;
        font-weight: 800;
        color: #1c2a23;
        margin-bottom: 6px;
    }

    .stat-foot {
        font-size: 13px;
        color: #647067;
    }

    .insight-card {
        padding: 18px 20px;
        border-radius: 18px;
        background: linear-gradient(135deg, #f7f3ff 0%, #ffffff 100%);
        border: 1px solid rgba(125, 184, 141, 0.18);
        box-shadow: 0 10px 24px rgba(28, 42, 35, 0.05);
    }

    .insight-title {
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: #5b4b8a;
        margin-bottom: 8px;
    }

    .insight-card p {
        margin: 0;
        color: #475449;
        line-height: 1.7;
        font-size: 14px;
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
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 480px) {
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

    (function autoRefresh() {
        let latestKnownId = parseInt(document.querySelector('table tbody tr')?.dataset.id || '0', 10);
        const banner = document.getElementById('newDataBanner');
        const reloadBtn = document.getElementById('reloadNow');
        if (reloadBtn) reloadBtn.addEventListener('click', () => location.reload());

        async function poll() {
            try {
                const res = await fetch('{{ route("pendaftaran.snapshot") }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
                if (!res.ok) return;
                const data = await res.json();
                if (data.stats) {
                    const t = document.getElementById('statTotal'); if (t) t.textContent = data.stats.total;
                    const b = document.getElementById('statBaru'); if (b) b.textContent = data.stats.baru;
                    const d = document.getElementById('statDiterima'); if (d) d.textContent = data.stats.diterima;
                }
                if (data.latest_id && latestKnownId && data.latest_id > latestKnownId) {
                    banner.style.display = 'block';
                }
                if (data.latest_id && !latestKnownId) {
                    latestKnownId = data.latest_id;
                }
            } catch (e) {}
        }
        setInterval(poll, 5000);
        poll();
    })();
</script>
@endsection