@extends('layouts.app')

@section('title', 'Koperasi - Panel Admin')
@section('page-title', 'Kelola Koperasi')

@section('content')
<div class="dashboard-shell">
    <div class="dashboard-header">
        <div>
            <p class="dashboard-kicker">Koperasi Sekolah</p>
            <h1 class="form-title" fetchpriority="high">Pesanan Koperasi</h1>
            <p class="form-subtitle">Pantau pembelian siswa di koperasi sekolah.</p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card stat-card-success">
            <div class="stat-label">Total Pendapatan</div>
            <div class="stat-value">{{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</div>
            <div class="stat-foot">Status lunas</div>
        </div>
        <div class="stat-card stat-card-warning">
            <div class="stat-label">Pending</div>
            <div class="stat-value">{{ number_format($totalPending ?? 0, 0, ',', '.') }}</div>
            <div class="stat-foot">Menunggu pembayaran</div>
        </div>
        <div class="stat-card stat-card-info">
            <div class="stat-label">Total Pesanan</div>
            <div class="stat-value">{{ $orders->count() }}</div>
            <div class="stat-foot">Semua transaksi</div>
        </div>
    </div>

    <div class="filter-toolbar">
        <div class="filter-group filter-search">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" class="filter-input" id="koperasiSearch" placeholder="Cari no. pesanan, nama siswa..." autocomplete="off">
        </div>
        <div class="filter-group">
            <select class="filter-select" id="koperasiStatusFilter" onchange="applyKoperasiFilters()">
                <option value="">Semua Status</option>
                <option value="lunas">Lunas</option>
                <option value="pending">Pending</option>
                <option value="batal">Batal</option>
            </select>
        </div>
        <button type="button" class="btn btn-secondary" onclick="clearKoperasiFilters()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            Bersihkan Filter
        </button>
    </div>

    <div class="form-section" style="padding:0;overflow:hidden;">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="checkbox-col"><input type="checkbox" id="selectAllKoperasi" onchange="toggleAllKoperasi(this)"></th>
                        <th>No. Pesanan</th>
                        <th class="hide-sm">Siswa</th>
                        <th>Total</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="koperasiTableBody">
                    @if ($orders->isEmpty())
                        <tr>
                            <td colspan="8" class="empty-state" style="padding:60px 20px;">
                                <div style="font-size:40px;margin-bottom:12px;">🛒</div>
                                <div style="font-weight:700;">Belum ada pesanan koperasi</div>
                            </td>
                        </tr>
                    @else
                        @foreach ($orders as $order)
                            <tr data-search="{{ strtolower('#' . $order->id . ' ' . ($order->user->name ?? '') . ' ' . ($order->user->username ?? '')) }}" data-status="{{ $order->status }}">
                                <td class="checkbox-col"><input type="checkbox" class="rowCheckbox" value="{{ $order->id }}" onchange="updateKoperasiBulkActions()"></td>
                                <td><strong>#{{ $order->id }}</strong></td>
                                <td class="hide-sm">
                                    <strong>{{ $order->user->name ?? 'Unknown' }}</strong>
                                    <div style="font-size:12px;color:#8a9890;">{{ $order->user->username ?? '' }}</div>
                                </td>
                                <td><strong>{{ number_format($order->total, 0, ',', '.') }}</strong></td>
                                <td>{{ $order->metode ? ucfirst($order->metode) : '-' }}</td>
                                <td>
                                    @php
                                        $statusClass = match($order->status) {
                                            'lunas' => 'badge-success',
                                            'batal' => 'badge-danger',
                                            default => 'badge-warning'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                                </td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('koperasi.show', $order->id) }}" class="action-btn action-btn-view">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($orders->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #f0f3f0;">
            {{ $orders->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<script>
    // Search + status filter
    const searchInput = document.getElementById('koperasiSearch');
    const statusFilter = document.getElementById('koperasiStatusFilter');
    const tableBody = document.getElementById('koperasiTableBody');
    const rows = tableBody ? tableBody.querySelectorAll('tbody tr[data-search]') : [];

    function applyKoperasiFilters() {
        const query = searchInput?.value.toLowerCase().trim() || '';
        const status = statusFilter?.value || '';
        let visibleCount = 0;

        rows.forEach(row => {
            const searchText = row.getAttribute('data-search');
            const rowStatus = row.getAttribute('data-status');
            const matchesSearch = !query || searchText.includes(query);
            const matchesStatus = !status || rowStatus === status;
            const matches = matchesSearch && matchesStatus;
            row.style.display = matches ? '' : 'none';
            if (matches) visibleCount++;
        });

        const emptyRow = tableBody?.querySelector('.empty-state')?.closest('tr');
        if (emptyRow) {
            emptyRow.style.display = visibleCount === 0 && (query || status) ? '' : 'none';
        }
    }

    searchInput?.addEventListener('input', applyKoperasiFilters);
    statusFilter?.addEventListener('change', applyKoperasiFilters);

    function clearKoperasiFilters() {
        if (searchInput) searchInput.value = '';
        if (statusFilter) statusFilter.value = '';
        applyKoperasiFilters();
    }

    // Bulk actions
    function toggleAllKoperasi(source) {
        document.querySelectorAll('.rowCheckbox').forEach(cb => {
            cb.checked = source.checked;
            const row = cb.closest('tr');
            if (row) row.classList.toggle('selected', source.checked);
        });
        updateKoperasiBulkActions();
    }

    function updateKoperasiBulkActions() {
        const checked = document.querySelectorAll('.rowCheckbox:checked').length;
        const bulkBar = document.getElementById('koperasiBulkBar');
        if (bulkBar) {
            bulkBar.classList.toggle('visible', checked > 0);
            bulkBar.querySelector('.selected-count').textContent =
                `${checked} item dipilih`;
        }
    }

    const selectAll = document.getElementById('selectAllKoperasi');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.rowCheckbox').forEach(cb => {
                cb.checked = this.checked;
                const row = cb.closest('tr');
                if (row) row.classList.toggle('selected', this.checked);
            });
            updateKoperasiBulkActions();
        });
    }
</script>
@endsection