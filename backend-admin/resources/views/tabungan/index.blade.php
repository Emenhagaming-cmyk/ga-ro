@extends('layouts.app')

@section('title', 'Kelola Tabungan - Panel Admin')
@section('page-title', 'Kelola Tabungan')

@section('content')
<div class="dashboard-shell">
    <div class="dashboard-header">
        <div>
            <p class="dashboard-kicker">Tabungan Siswa</p>
            <h1 class="form-title" fetchpriority="high">Kelola Tabungan Siswa</h1>
            <p class="form-subtitle">Pantau saldo dan riwayat transaksi setor/tarik siswa.</p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card stat-card-primary">
            <div class="stat-label">Total Saldo</div>
            <div class="stat-value">{{ number_format($totalSaldo ?? 0, 0, ',', '.') }}</div>
            <div class="stat-foot">Semua siswa</div>
        </div>
        <div class="stat-card stat-card-info">
            <div class="stat-label">Jumlah Siswa</div>
            <div class="stat-value">{{ $siswa->count() }}</div>
            <div class="stat-foot">Akun dengan tabungan</div>
        </div>
        <div class="stat-card stat-card-warning">
            <div class="stat-label">Total Transaksi</div>
            <div class="stat-value">{{ \App\Models\Tabungan::count() }}</div>
            <div class="stat-foot">Setor & tarik</div>
        </div>
    </div>

    <div class="filter-toolbar">
        <div class="filter-group filter-search">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" class="filter-input" id="tabunganSearch" placeholder="Cari nama, username, email..." autocomplete="off">
        </div>
        <button type="button" class="btn btn-secondary" onclick="clearTabunganFilters()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            Bersihkan Filter
        </button>
    </div>

    <div class="form-section" style="padding:0;overflow:hidden;">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="checkbox-col"><input type="checkbox" id="selectAllTabungan" onchange="toggleAllTabungan(this)"></th>
                        <th>Nama</th>
                        <th class="hide-sm">Username</th>
                        <th>Email</th>
                        <th>Saldo</th>
                        <th>Total Setor</th>
                        <th>Total Tarik</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="tabunganTableBody">
                    @if ($siswa->isEmpty())
                        <tr>
                            <td colspan="8" class="empty-state" style="padding:60px 20px;">
                                <div style="font-size:40px;margin-bottom:12px;">💰</div>
                                <div style="font-weight:700;">Belum ada siswa dengan tabungan</div>
                            </td>
                        </tr>
                    @else
                        @foreach ($siswa as $s)
                            @php
                                $saldo = ($s->total_setor ?? 0) - ($s->total_tarik ?? 0);
                            @endphp
                            <tr data-search="{{ strtolower($s->name . ' ' . $s->username . ' ' . $s->email) }}">
                                <td class="checkbox-col"><input type="checkbox" class="rowCheckbox" value="{{ $s->id }}" onchange="updateTabunganBulkActions()"></td>
                                <td><strong>{{ $s->name }}</strong></td>
                                <td class="hide-sm">{{ $s->username }}</td>
                                <td>{{ $s->email }}</td>
                                <td><strong style="color:#3a6450;">{{ number_format($saldo, 0, ',', '.') }}</strong></td>
                                <td>{{ number_format($s->total_setor ?? 0, 0, ',', '.') }}</td>
                                <td>{{ number_format($s->total_tarik ?? 0, 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('tabungan.show', $s->id) }}" class="action-btn action-btn-view">Lihat</a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($siswa->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #f0f3f0;">
            {{ $siswa->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<script>
    // Search filter
    const searchInput = document.getElementById('tabunganSearch');
    const tableBody = document.getElementById('tabunganTableBody');
    const rows = tableBody ? tableBody.querySelectorAll('tbody tr[data-search]') : [];

    searchInput?.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        let visibleCount = 0;

        rows.forEach(row => {
            const searchText = row.getAttribute('data-search');
            const matches = !query || searchText.includes(query);
            row.style.display = matches ? '' : 'none';
            if (matches) visibleCount++;
        });

        // Show/hide empty state
        const emptyRow = tableBody?.querySelector('.empty-state')?.closest('tr');
        if (emptyRow) {
            emptyRow.style.display = visibleCount === 0 && query ? '' : 'none';
        }
    });

    function clearTabunganFilters() {
        if (searchInput) {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
        }
    }

    // Bulk actions
    function toggleAllTabungan(source) {
        document.querySelectorAll('.rowCheckbox').forEach(cb => {
            cb.checked = source.checked;
            const row = cb.closest('tr');
            if (row) row.classList.toggle('selected', source.checked);
        });
        updateTabunganBulkActions();
    }

    function updateTabunganBulkActions() {
        const checked = document.querySelectorAll('.rowCheckbox:checked').length;
        const bulkBar = document.getElementById('tabunganBulkBar');
        if (bulkBar) {
            bulkBar.classList.toggle('visible', checked > 0);
            bulkBar.querySelector('.selected-count').textContent =
                `${checked} item dipilih`;
        }
    }

    // Select all checkbox sync
    const selectAll = document.getElementById('selectAllTabungan');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.rowCheckbox').forEach(cb => {
                cb.checked = this.checked;
                const row = cb.closest('tr');
                if (row) row.classList.toggle('selected', this.checked);
            });
            updateTabunganBulkActions();
        });
    }
</script>
@endsection