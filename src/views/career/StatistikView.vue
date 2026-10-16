<template>
  <div class="statistik-page">
    <div class="page-header">
      <h2>Statistik</h2>
      <p class="page-desc">Sebaran lowongan per jurusan dan tipe, plus rekap aktivitas lamaran Anda.</p>
    </div>

    <div v-if="loading" class="loading-state">
      <i class="fas fa-spinner fa-spin"></i> Memuat statistik...
    </div>

    <div v-else-if="error" class="error-state">
      <i class="fas fa-triangle-exclamation"></i>
      <p>Gagal memuat statistik. Pastikan Anda sudah login lalu muat ulang halaman.</p>
    </div>

    <template v-else>
      <div class="summary-cards">
        <div class="summary-card">
          <span class="summary-number">{{ lowongan.aktif }}</span>
          <span class="summary-label">Lowongan Aktif</span>
        </div>
        <div class="summary-card">
          <span class="summary-number">{{ lowongan.total }}</span>
          <span class="summary-label">Total Lowongan</span>
        </div>
        <div class="summary-card">
          <span class="summary-number">{{ saya.total }}</span>
          <span class="summary-label">Lamaran Saya</span>
        </div>
        <div class="summary-card">
          <span class="summary-number">{{ saya.diterima }}</span>
          <span class="summary-label">Diterima</span>
        </div>
      </div>

      <div class="panel-grid">
        <section class="panel">
          <h2 class="panel-title">Lowongan per Tipe</h2>
          <div v-if="perTipe.length === 0" class="panel-empty">Belum ada data.</div>
          <ul v-else class="bar-list">
            <li v-for="row in perTipe" :key="row.label" class="bar-row">
              <span class="bar-label">{{ row.label || 'Lainnya' }}</span>
              <span class="bar-track">
                <span
                  class="bar-fill"
                  :style="{ width: row.percent + '%', background: typeColor(row.label) }"
                ></span>
              </span>
              <span class="bar-value">{{ row.total }}</span>
            </li>
          </ul>
        </section>

        <section class="panel">
          <h2 class="panel-title">Lowongan per Jurusan</h2>
          <div v-if="perJurusan.length === 0" class="panel-empty">Belum ada data.</div>
          <ul v-else class="bar-list">
            <li v-for="row in perJurusan" :key="row.label" class="bar-row">
              <span class="bar-label">{{ row.label }}</span>
              <span class="bar-track">
                <span class="bar-fill" :style="{ width: row.percent + '%' }"></span>
              </span>
              <span class="bar-value">{{ row.total }}</span>
            </li>
          </ul>
        </section>
      </div>

      <div class="panel-grid">
        <section class="panel">
          <h2 class="panel-title">Lokasi Terbanyak</h2>
          <div v-if="perLokasi.length === 0" class="panel-empty">Belum ada data.</div>
          <ul v-else class="bar-list">
            <li v-for="row in perLokasi" :key="row.label" class="bar-row">
              <span class="bar-label">{{ row.label }}</span>
              <span class="bar-track">
                <span class="bar-fill fill-alt" :style="{ width: row.percent + '%' }"></span>
              </span>
              <span class="bar-value">{{ row.total }}</span>
            </li>
          </ul>
        </section>

        <section class="panel">
          <h2 class="panel-title">Model Kerja</h2>
          <div v-if="perArrangement.length === 0" class="panel-empty">Belum ada data.</div>
          <ul v-else class="bar-list">
            <li v-for="row in perArrangement" :key="row.label" class="bar-row">
              <span class="bar-label">{{ arrangementLabel(row.label) }}</span>
              <span class="bar-track">
                <span class="bar-fill fill-alt" :style="{ width: row.percent + '%' }"></span>
              </span>
              <span class="bar-value">{{ row.total }}</span>
            </li>
          </ul>
        </section>
      </div>

      <section class="panel">
        <h2 class="panel-title">Aktivitas Lamaran Saya</h2>
        <div class="status-cards">
          <div class="status-card">
            <span class="status-number">{{ saya.pending }}</span>
            <span class="status-label">Diproses</span>
          </div>
          <div class="status-card">
            <span class="status-number">{{ saya.diterima }}</span>
            <span class="status-label">Diterima</span>
          </div>
          <div class="status-card">
            <span class="status-number">{{ saya.ditolak }}</span>
            <span class="status-label">Ditolak</span>
          </div>
          <div class="status-card">
            <span class="status-number">{{ saya.dibatalkan }}</span>
            <span class="status-label">Dibatalkan</span>
          </div>
          <div class="status-card">
            <span class="status-number">{{ saya.bookmark }}</span>
            <span class="status-label">Tersimpan</span>
          </div>
        </div>

        <h3 class="sub-title">6 Bulan Terakhir</h3>
        <div class="tren-chart">
          <div v-for="row in tren" :key="row.bulan" class="tren-col">
            <span class="tren-value">{{ row.total }}</span>
            <div class="tren-bar-wrap">
              <div class="tren-bar" :style="{ height: trenHeight(row.total) + '%' }"></div>
            </div>
            <span class="tren-label">{{ monthLabel(row.bulan) }}</span>
          </div>
        </div>
      </section>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthSession } from '@/composable/useAuthSession'
import { fetchJson } from '@/services/fetchJson'

const { BACKEND } = useAuthSession()

const data = ref(null)
const loading = ref(true)
const error = ref(false)

const lowongan = computed(() => data.value?.lowongan ?? { total: 0, aktif: 0 })
const saya = computed(() => data.value?.saya ?? {})
const tren = computed(() => data.value?.tren ?? [])

// pluck() backend mengirim objek {label: jumlah} → jadikan array + hitung lebar bar.
function toBars(source) {
  const rows = Object.entries(source || {}).map(([label, total]) => ({
    label,
    total: Number(total) || 0,
  }))
  rows.sort((a, b) => b.total - a.total)
  const max = rows.reduce((m, r) => Math.max(m, r.total), 0)
  return rows.map((r) => ({ ...r, percent: max > 0 ? Math.round((r.total / max) * 100) : 0 }))
}

const perTipe = computed(() => toBars(data.value?.lowongan?.per_tipe))
const perJurusan = computed(() => toBars(data.value?.lowongan?.per_jurusan).slice(0, 8))
const perLokasi = computed(() => toBars(data.value?.lowongan?.per_lokasi))
const perArrangement = computed(() => toBars(data.value?.lowongan?.per_arrangement))

const trenMax = computed(() => tren.value.reduce((m, r) => Math.max(m, r.total), 0))

function trenHeight(total) {
  if (trenMax.value === 0) return 0
  // minimal 6% supaya bar bertotal > 0 tetap terlihat
  return Math.max(6, Math.round((total / trenMax.value) * 100))
}

const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']

function monthLabel(ym) {
  const [year, month] = String(ym).split('-')
  const idx = Number(month) - 1
  if (!year || Number.isNaN(idx) || !MONTHS[idx]) return ym
  return `${MONTHS[idx]} ${year.slice(2)}`
}

function typeColor(type) {
  switch (type) {
    case 'Magang': return '#3a6450'
    case 'Kerja': return '#4f8cc9'
    case 'BKK': return '#7c5cbf'
    default: return '#3a6450'
  }
}

function arrangementLabel(value) {
  switch (value) {
    case 'onsite': return 'Onsite'
    case 'hybrid': return 'Hybrid'
    case 'remote': return 'Remote'
    default: return value || 'Lainnya'
  }
}

async function fetchStatistik() {
  loading.value = true
  error.value = false
  try {
    const res = await fetchJson(`${BACKEND}/career/statistik`)
    if (!res.ok) throw new Error('gagal')
    data.value = await res.json()
  } catch (e) {
    error.value = true
  } finally {
    loading.value = false
  }
}

onMounted(fetchStatistik)
</script>

<style scoped>
.statistik-page {
  padding: 4px 0;
}

.page-header h2 {
  font-size: 24px;
  font-weight: 800;
  color: #1c2a23;
  margin: 0 0 6px;
}

.page-desc {
  font-size: 13px;
  color: #647067;
  margin: 0 0 24px;
}

.summary-cards {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 14px;
  margin-bottom: 20px;
}

.summary-card {
  background: #fff;
  border: 1px solid var(--color-border, #e5e7e6);
  border-radius: 18px;
  padding: 18px 20px;
  box-shadow: 0 4px 12px rgba(35, 55, 42, 0.05);
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.summary-number {
  font-size: 26px;
  font-weight: 800;
  color: #1c2a23;
  line-height: 1.1;
}

.summary-label {
  font-size: 12px;
  font-weight: 600;
  color: #647067;
}

.panel-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.panel {
  background: #fff;
  border: 1px solid var(--color-border, #e5e7e6);
  border-radius: 20px;
  padding: 20px 22px;
  box-shadow: 0 4px 12px rgba(35, 55, 42, 0.05);
  margin-bottom: 16px;
}

.panel-title {
  font-size: 15px;
  font-weight: 800;
  color: #1c2a23;
  margin: 0 0 14px;
}

.sub-title {
  font-size: 13px;
  font-weight: 800;
  color: #1c2a23;
  margin: 22px 0 12px;
}

.panel-empty {
  font-size: 13px;
  color: #8a9a8f;
  padding: 8px 0 4px;
}

.bar-list {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.bar-row {
  display: grid;
  grid-template-columns: 96px 1fr 34px;
  align-items: center;
  gap: 10px;
  font-size: 12.5px;
}

.bar-label {
  font-weight: 700;
  color: #3a4a40;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.bar-track {
  height: 10px;
  border-radius: 999px;
  background: #eef2ef;
  overflow: hidden;
}

.bar-fill {
  display: block;
  height: 100%;
  border-radius: 999px;
  background: #3a6450;
  transition: width 0.4s ease;
}

.bar-fill.fill-alt {
  background: #4f8cc9;
}

.bar-value {
  text-align: right;
  font-weight: 800;
  color: #1c2a23;
}

.status-cards {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 12px;
}

.status-card {
  background: #f8faf6;
  border: 1px solid #eef2ef;
  border-radius: 16px;
  padding: 14px 16px;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.status-number {
  font-size: 20px;
  font-weight: 800;
  color: #3a6450;
  line-height: 1.1;
}

.status-label {
  font-size: 11.5px;
  font-weight: 600;
  color: #647067;
}

.tren-chart {
  display: flex;
  align-items: flex-end;
  gap: 10px;
}

.tren-col {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  min-width: 0;
}

.tren-value {
  font-size: 12px;
  font-weight: 800;
  color: #1c2a23;
}

.tren-bar-wrap {
  width: 100%;
  height: 120px;
  display: flex;
  align-items: flex-end;
  background: #f8faf6;
  border-radius: 10px;
  overflow: hidden;
}

.tren-bar {
  width: 100%;
  background: linear-gradient(180deg, #4f8cc9, #3a6450);
  border-radius: 10px 10px 0 0;
  transition: height 0.4s ease;
}

.tren-label {
  font-size: 11px;
  font-weight: 700;
  color: #8a9a8f;
  white-space: nowrap;
}

.loading-state,
.error-state {
  text-align: center;
  padding: 70px 20px;
  color: #647067;
}

.error-state i {
  font-size: 32px;
  display: block;
  margin-bottom: 12px;
  opacity: 0.35;
}

@media (max-width: 900px) {
  .summary-cards {
    grid-template-columns: repeat(2, 1fr);
  }

  .panel-grid {
    grid-template-columns: 1fr;
  }

  .status-cards {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 600px) {
  .bar-row {
    grid-template-columns: 74px 1fr 30px;
  }
}
</style>
