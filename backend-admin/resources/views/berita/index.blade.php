@extends('layouts.app')

@section('title', 'Kelola Berita — Panel Admin')

@section('page-title', 'Kelola Berita')

@section('content')
<div class="main-content">

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="alert alert-success" style="margin-bottom:20px;">✓ {{ session('success') }}</div>
    @endif

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 class="form-title" style="margin:0;">Berita & Pengumuman</h1>
            <p class="form-subtitle" style="margin:6px 0 0;">Kelola artikel berita yang tampil di website sekolah.</p>
        </div>
        <a href="{{ route('berita.create') }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:8px;white-space:nowrap;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Berita
        </a>
    </div>

    {{-- Stats bar --}}
    <div class="stats-grid">
        <div class="stat-card stat-card-primary">
            <div class="stat-label">Total Berita</div>
            <div class="stat-value">{{ $beritas->total() }}</div>
        </div>
        <div class="stat-card stat-card-success">
            <div class="stat-label">Dipublikasikan</div>
            <div class="stat-value">{{ $beritas->where('is_published', true)->count() }}</div>
        </div>
        <div class="stat-card stat-card-warning">
            <div class="stat-label">Unggulan</div>
            <div class="stat-value">{{ $beritas->where('featured', true)->count() }}</div>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <div class="filter-toolbar">
        <div class="filter-group filter-search">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" class="filter-input" id="beritaSearch" placeholder="Cari judul, penulis..." autocomplete="off">
        </div>
        <div class="filter-group">
            <select class="filter-select" id="beritaCategoryFilter" onchange="applyBeritaFilters()">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <select class="filter-select" id="beritaStatusFilter" onchange="applyBeritaFilters()">
                <option value="">Semua Status</option>
                <option value="published">Dipublikasikan</option>
                <option value="draft">Draft</option>
                <option value="featured">Unggulan</option>
            </select>
        </div>
        <button type="button" class="btn btn-secondary" onclick="clearBeritaFilters()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            Bersihkan Filter
        </button>
    </div>

    {{-- Tabel --}}
    <div class="form-section" style="padding:0;overflow:hidden;">
        <div class="table-wrap" style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
            <table style="width:100%;border-collapse:collapse;min-width:700px;">
                <thead>
                    <tr>
                        <th class="checkbox-col"><input type="checkbox" id="selectAllBerita" onchange="toggleAllBerita(this)"></th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="beritaTableBody">
                    @forelse($beritas as $item)
                    <tr data-search="{{ strtolower($item->title . ' ' . $item->author . ' ' . $item->category) }}" data-status="{{ $item->is_published ? 'published' : 'draft' }}" data-featured="{{ $item->featured ? 'true' : 'false' }}">
                        <td class="checkbox-col"><input type="checkbox" class="rowCheckbox" value="{{ $item->id }}" onchange="updateBeritaBulkActions()"></td>
                        {{-- Judul + thumbnail --}}
                        <td style="padding:14px 20px;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                @if($item->image_path)
                                    <img src="{{ '/storage/' . $item->image_path }}" alt="{{ $item->title }}"
                                        style="width:48px;height:36px;object-fit:cover;border-radius:8px;flex-shrink:0;background:#e8f0e6;">
                                @else
                                    <div style="width:48px;height:36px;border-radius:8px;background:linear-gradient(135deg,#2f5b45,#7db88d);flex-shrink:0;display:flex;align-items:center;justify-content:center;">
                                        <svg width="16" height="16" fill="none" stroke="rgba(255,255,255,.7)" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                    </div>
                                @endif
                                <div style="min-width:0;">
                                    <div style="font-weight:700;font-size:14px;color:#1c2a23;line-height:1.3;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden;">
                                        {{ $item->title }}
                                        @if($item->featured)
                                        <span style="display:inline-block;margin-left:6px;padding:1px 6px;border-radius:6px;background:#fef3e0;color:#f39c12;font-size:10px;font-weight:700;vertical-align:middle;">★ Unggulan</span>
                                        @endif
                                    </div>
                                    <div style="font-size:11px;color:#8a9a8f;margin-top:2px;">{{ Str::limit($item->excerpt, 60) }}</div>
                                </div>
                            </div>
                        </td>
                        {{-- Kategori --}}
                        <td style="padding:14px 16px;white-space:nowrap;">
                            <span style="display:inline-block;padding:4px 10px;border-radius:8px;font-size:12px;font-weight:700;color:#fff;background:{{ $item->category_color }};">
                                {{ $item->category }}
                            </span>
                        </td>
                        {{-- Tanggal --}}
                        <td style="padding:14px 16px;font-size:13px;color:#647067;white-space:nowrap;">
                            {{ $item->published_at?->translatedFormat('d M Y') ?? '-' }}
                        </td>
                        {{-- Status --}}
                        <td style="padding:14px 16px;white-space:nowrap;">
                            @if($item->is_published)
                                <span class="badge badge-success">Tayang</span>
                            @else
                                <span class="badge badge-neutral">Draft</span>
                            @endif
                        </td>
                        {{-- Aksi --}}
                        <td style="padding:14px 16px;text-align:right;white-space:nowrap;">
                            <a href="{{ route('berita.edit', $item) }}" class="action-btn action-btn-edit">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Edit
                            </a>
                            <form method="POST" action="{{ route('berita.destroy', $item) }}" style="display:inline;"
                                onsubmit="return confirm('Hapus berita ini? Tindakan tidak bisa dibatalkan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn action-btn-delete">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="padding:60px;text-align:center;color:#8a9a8f;">
                            <div style="font-size:40px;margin-bottom:12px;">📰</div>
                            <div style="font-weight:700;">Belum ada berita</div>
                            <div style="font-size:13px;margin-top:4px;">Klik "Tambah Berita" untuk mulai.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($beritas->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #f0f3f0;">
            {{ $beritas->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>

<script>
    // Search + category + status filter
    const searchInput = document.getElementById('beritaSearch');
    const categoryFilter = document.getElementById('beritaCategoryFilter');
    const statusFilter = document.getElementById('beritaStatusFilter');
    const tableBody = document.getElementById('beritaTableBody');
    const rows = tableBody ? tableBody.querySelectorAll('tbody tr[data-search]') : [];

    function applyBeritaFilters() {
        const query = searchInput?.value.toLowerCase().trim() || '';
        const category = categoryFilter?.value || '';
        const status = statusFilter?.value || '';
        let visibleCount = 0;

        rows.forEach(row => {
            const searchText = row.getAttribute('data-search');
            const rowStatus = row.getAttribute('data-status');
            const rowFeatured = row.getAttribute('data-featured');
            const matchesSearch = !query || searchText.includes(query);
            const matchesCategory = !category || row.querySelector('td:nth-child(3) span')?.textContent?.trim() === category;
            const matchesStatus = !status ||
                (status === 'published' && rowStatus === 'published') ||
                (status === 'draft' && rowStatus === 'draft') ||
                (status === 'featured' && rowFeatured === 'true');
            const matches = matchesSearch && matchesStatus;
            row.style.display = matches ? '' : 'none';
            if (matches) visibleCount++;
        });

        const emptyRow = tableBody?.querySelector('td[colspan="6"]')?.closest('tr');
        if (emptyRow) {
            emptyRow.style.display = visibleCount === 0 && (query || category || status) ? '' : 'none';
        }
    }

    searchInput?.addEventListener('input', applyBeritaFilters);
    categoryFilter?.addEventListener('change', applyBeritaFilters);
    statusFilter?.addEventListener('change', applyBeritaFilters);

    function clearBeritaFilters() {
        if (searchInput) searchInput.value = '';
        if (categoryFilter) categoryFilter.value = '';
        if (statusFilter) statusFilter.value = '';
        applyBeritaFilters();
    }

    // Bulk actions
    function toggleAllBerita(source) {
        document.querySelectorAll('.rowCheckbox').forEach(cb => {
            cb.checked = source.checked;
            const row = cb.closest('tr');
            if (row) row.classList.toggle('selected', source.checked);
        });
        updateBeritaBulkActions();
    }

    function updateBeritaBulkActions() {
        const checked = document.querySelectorAll('.rowCheckbox:checked').length;
        const bulkBar = document.getElementById('beritaBulkBar');
        if (bulkBar) {
            bulkBar.classList.toggle('visible', checked > 0);
            bulkBar.querySelector('.selected-count').textContent =
                `${checked} item dipilih`;
        }
    }

    const selectAll = document.getElementById('selectAllBerita');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.rowCheckbox').forEach(cb => {
                cb.checked = this.checked;
                const row = cb.closest('tr');
                if (row) row.classList.toggle('selected', this.checked);
            });
            updateBeritaBulkActions();
        });
    }
</script>
@endsection