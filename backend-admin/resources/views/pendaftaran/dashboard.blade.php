@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="dashboard-shell">
    <div class="dashboard-header">
        <div>
            <p class="dashboard-kicker">Admin Dashboard</p>
            <h1 class="form-title">Pantau Data Pendaftar SPMB</h1>
            <p class="form-subtitle">Ringkasan cepat status pendaftaran dan informasi siswa yang masuk.</p>
        </div>
    </div>

    <div class="stats-grid" id="statsGrid">
        <div class="stat-card stat-card-green">
            <div class="stat-label">Total Pendaftar</div>
            <div class="stat-value" id="statTotal">{{ $stats['total'] }}</div>
            <div class="stat-foot">Semua data masuk</div>
        </div>
        <div class="stat-card stat-card-blue">
            <div class="stat-label">Baru</div>
            <div class="stat-value" id="statBaru">{{ $stats['baru'] }}</div>
            <div class="stat-foot">Menunggu diproses</div>
        </div>
        <div class="stat-card stat-card-violet">
            <div class="stat-label">Diproses</div>
            <div class="stat-value" id="statDiproses">{{ $stats['diproses'] }}</div>
            <div class="stat-foot">Sedang diverifikasi</div>
        </div>
        <div class="stat-card stat-card-gold">
            <div class="stat-label">Diterima</div>
            <div class="stat-value" id="statDiterima">{{ $stats['diterima'] }}</div>
            <div class="stat-foot">Sudah disetujui</div>
        </div>
        <div class="stat-card stat-card-red">
            <div class="stat-label">Ditolak</div>
            <div class="stat-value" id="statDitolak">{{ $stats['ditolak'] }}</div>
            <div class="stat-foot">Tidak lolos seleksi</div>
        </div>
    </div>

    <div id="newDataBanner" style="display:none;background:#ecfdf5;color:#047857;border:1px solid #a7f3d0;border-radius:14px;padding:14px 18px;font-weight:700;">
        🔔 Data pendaftaran baru masuk — <button id="reloadNow" class="btn btn-primary" style="margin-left:8px;font-size:12px;padding:6px 14px;">Muat Ulang</button>
    </div>

    <div class="insight-card">
        <div class="insight-title">Ringkasan Data</div>
        <p>{{ $insight }}</p>
    </div>

    <div class="charts-grid">
        <div class="chart-card">
            <h3 class="chart-title">Pendaftar per Hari (30 Hari)</h3>
            <canvas id="chartDaily" height="110"></canvas>
        </div>
        <div class="chart-card">
            <h3 class="chart-title">Distribusi Jurusan</h3>
            <canvas id="chartJurusan" height="110"></canvas>
        </div>
        <div class="chart-card">
            <h3 class="chart-title">Status Pendaftaran</h3>
            <canvas id="chartStatus" height="110"></canvas>
        </div>
    </div>

    <div class="form-section">
        @if (session('success'))
        <div class="alert alert-success">
            ✓ {{ session('success') }}
        </div>
        @endif

        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:8px;">
            <h2 style="font-size:17px;font-weight:800;color:#1c2a23;">Pendaftar Terbaru</h2>
            <a href="{{ route('pendaftaran.index') }}" class="btn btn-secondary" style="text-decoration:none;padding:8px 18px;font-size:13px;">Lihat Semua</a>
        </div>

        @if ($terbaru->isEmpty())
            <div class="empty-state">
                <p>Belum ada data pendaftaran</p>
                <p style="font-size:13px;color:#9ba8a0;">Data muncul otomatis saat pendaftar mengisi formulir di web utama.</p>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th class="hide-sm">No. HP</th>
                            <th>Jurusan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($terbaru as $item)
                            <tr data-id="{{ $item->id }}">
                                <td><strong>{{ $item->nama_lengkap }}</strong></td>
                                <td class="hide-sm">{{ $item->no_hp }}</td>
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
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
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
        grid-template-columns: repeat(5, minmax(0, 1fr));
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

    .stat-card-violet {
        background: linear-gradient(135deg, #f3efff 0%, #faf8ff 100%);
    }

    .stat-card-gold {
        background: linear-gradient(135deg, #fff7e8 0%, #fffcf5 100%);
    }

    .stat-card-red {
        background: linear-gradient(135deg, #fff0f0 0%, #fffafa 100%);
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

    .charts-grid {
        display: grid;
        grid-template-columns: 1.4fr 1fr 1fr;
        gap: 16px;
    }

    .chart-card {
        background: #fbfcfa;
        border: 1px solid #dfe4dd;
        border-radius: 20px;
        padding: 18px 20px;
        box-shadow: 0 10px 24px rgba(28, 42, 35, 0.05);
    }

    .chart-title {
        font-size: 13px;
        font-weight: 800;
        color: #3a6450;
        letter-spacing: 0.04em;
        margin-bottom: 14px;
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

    @media (max-width: 1024px) {
        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .charts-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    requestAnimationFrame(function () {
        if (typeof Chart === 'undefined') return;
        const dailyLabels = @json($chart['daily_labels']);
    const dailyCounts = @json($chart['daily']);
    const jurusanLabels = Object.keys(@json($chart['jurusan']));
    const jurusanCounts = Object.values(@json($chart['jurusan']));
    const statusLabels = Object.keys(@json($chart['status']));
    const statusCounts = Object.values(@json($chart['status']));

    Chart.defaults.font.family = 'Quicksand, sans-serif';
    Chart.defaults.color = '#647067';

    new Chart(document.getElementById('chartDaily'), {
        type: 'line',
        data: {
            labels: dailyLabels,
            datasets: [{
                label: 'Pendaftar',
                data: dailyCounts,
                borderColor: '#3a6450',
                backgroundColor: 'rgba(58, 100, 80, 0.12)',
                fill: true,
                tension: 0.35,
                pointRadius: 2,
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { maxTicksLimit: 10 } },
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });

    new Chart(document.getElementById('chartJurusan'), {
        type: 'doughnut',
        data: {
            labels: jurusanLabels,
            datasets: [{
                data: jurusanCounts,
                backgroundColor: ['#3a6450', '#4f8cc9', '#d9a441'],
                borderWidth: 0,
            }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });

    new Chart(document.getElementById('chartStatus'), {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusCounts,
                    backgroundColor: ['#7db88d', '#a78bfa', '#4f8cc9', '#e2564d'],
                    borderWidth: 0,
                }]
            },
            options: { plugins: { legend: { position: 'bottom' } } }
        });
    });
</script>

<script>
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
                    const p = document.getElementById('statDiproses'); if (p) p.textContent = data.stats.diproses;
                    const d = document.getElementById('statDiterima'); if (d) d.textContent = data.stats.diterima;
                    const r = document.getElementById('statDitolak'); if (r) r.textContent = data.stats.ditolak;
                }
                if (data.latest_id && latestKnownId && data.latest_id > latestKnownId) {
                    banner.style.display = 'block';
                }
                if (data.latest_id && !latestKnownId) {
                    latestKnownId = data.latest_id;
                }
            } catch (e) {}
        }
        setInterval(poll, 20000);
        poll();
    })();
</script>
@endsection