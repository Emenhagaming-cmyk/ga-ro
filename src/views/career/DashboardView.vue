<template>
  <div class="dashboard-page">
    <div class="dashboard-header">
      <h2>Dashboard Career Center</h2>
      <p class="dashboard-desc">Ringkasan aktivitas lowongan, lamaran, dan statistik karir Anda.</p>
    </div>

    <div v-if="loading" class="loading-state">
      <i class="fas fa-spinner fa-spin"></i> Memuat statistik...
    </div>

    <div v-else-if="error" class="error-state">
      <i class="fas fa-triangle-exclamation"></i>
      <p>Gagal memuat statistik. Pastikan Anda sudah login.</p>
    </div>

    <template v-else>
      <div class="stat-cards">
        <div class="stat-card">
          <i class="fas fa-file-lines"></i>
          <div class="stat-info">
            <span class="stat-number">{{ stats.total_lamaran }}</span>
            <span class="stat-label">Total Lamaran</span>
          </div>
        </div>
        <div class="stat-card">
          <i class="fas fa-clock"></i>
          <div class="stat-info">
            <span class="stat-number">{{ stats.lamaran_pending }}</span>
            <span class="stat-label">Sedang Diproses</span>
          </div>
        </div>
        <div class="stat-card">
          <i class="fas fa-check-circle"></i>
          <div class="stat-info">
            <span class="stat-number">{{ stats.lamaran_diterima }}</span>
            <span class="stat-label">Diterima</span>
          </div>
        </div>
        <div class="stat-card">
          <i class="fas fa-bookmark"></i>
          <div class="stat-info">
            <span class="stat-number">{{ stats.bookmark_count }}</span>
            <span class="stat-label">Lowongan Tersimpan</span>
          </div>
        </div>
      </div>

      <div class="upcoming-section">
        <h2>Deadline Terdekat</h2>
        <div
          v-if="stats.upcoming_deadline"
          class="upcoming-card"
        >
          <div class="upcoming-logo" :style="{ background: typeColor(stats.upcoming_deadline.type) + '15', color: typeColor(stats.upcoming_deadline.type) }">
            {{ stats.upcoming_deadline.company?.charAt(0) || '?' }}
          </div>
          <div class="upcoming-info">
            <h3>{{ stats.upcoming_deadline.title }}</h3>
            <span class="upcoming-company">{{ stats.upcoming_deadline.company }}</span>
          </div>
          <div class="upcoming-deadline">
            <span class="deadline-label">Deadline</span>
            <span class="deadline-date">{{ formatDate(stats.upcoming_deadline.deadline) }}</span>
          </div>
        </div>
        <div v-else class="upcoming-empty">
          <i class="fas fa-calendar-xmark"></i>
          <p>Tidak ada lowongan tersimpan dengan deadline mendatang.</p>
          <router-link to="/career-center/search" class="btn-browse">
            <i class="fas fa-magnifying-glass"></i> Cari Lowongan
          </router-link>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthSession } from '@/composable/useAuthSession'
import { fetchJson } from '@/services/fetchJson'

const { BACKEND } = useAuthSession()

const stats = ref({
  total_lamaran: 0,
  lamaran_pending: 0,
  lamaran_diterima: 0,
  bookmark_count: 0,
  upcoming_deadline: null,
})
const loading = ref(true)
const error = ref(null)

async function fetchDashboard() {
  loading.value = true
  error.value = null
  try {
    const res = await fetchJson(`${BACKEND}/career/dashboard`)
    if (res.ok) {
      stats.value = await res.json()
    } else {
      error.value = true
    }
  } catch (e) {
    error.value = true
  } finally {
    loading.value = false
  }
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
}

function typeColor(type) {
  switch (type) {
    case 'Magang': return '#3a6450'
    case 'Kerja': return '#4f8cc9'
    case 'BKK': return '#7c5cbf'
    default: return '#3a6450'
  }
}

onMounted(fetchDashboard)
</script>

<style scoped>
.dashboard-page {
  padding: 0;
}

.dashboard-header h2 {
  font-size: 24px;
  font-weight: 800;
  color: #1c2a23;
  margin: 0 0 6px;
}

.dashboard-desc {
  font-size: 13px;
  color: #647067;
  margin: 0 0 24px;
}

.stat-cards {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-bottom: 32px;
}

.stat-card {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 20px;
  background: #fff;
  border-radius: 20px;
  border: 1px solid #e5e7e6;
  box-shadow: 0 4px 12px rgba(35, 55, 42, 0.05);
}

.stat-card i {
  width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: #e8f0e6;
  color: #3a6450;
  font-size: 18px;
}

.stat-info {
  display: flex;
  flex-direction: column;
}

.stat-number {
  font-size: 24px;
  font-weight: 800;
  color: #1c2a23;
  line-height: 1;
}

.stat-label {
  font-size: 12px;
  color: #647067;
  font-weight: 600;
}

.upcoming-section {
  background: #fff;
  border-radius: 20px;
  border: 1px solid #e5e7e6;
  padding: 24px;
  box-shadow: 0 4px 12px rgba(35, 55, 42, 0.05);
}

.upcoming-section h2 {
  font-size: 18px;
  font-weight: 800;
  color: #1c2a23;
  margin: 0 0 16px;
}

.upcoming-card {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px;
  background: #f8faf6;
  border-radius: 16px;
  border: 1px solid #e5e7e6;
}

.upcoming-logo {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  font-weight: 800;
  flex-shrink: 0;
}

.upcoming-info h3 {
  font-size: 15px;
  font-weight: 800;
  color: #1c2a23;
  margin: 0;
}

.upcoming-company {
  font-size: 12px;
  color: #647067;
  font-weight: 700;
}

.upcoming-deadline {
  margin-left: auto;
  text-align: right;
  flex-shrink: 0;
}

.deadline-label {
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #96a098;
}

.deadline-date {
  font-size: 14px;
  font-weight: 800;
  color: #e8653c;
}

.upcoming-empty {
  text-align: center;
  padding: 40px 20px;
  color: #647067;
}

.upcoming-empty i {
  font-size: 32px;
  margin-bottom: 12px;
  opacity: 0.3;
}

.btn-browse {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 22px;
  border-radius: 999px;
  background: #3a6450;
  color: #fff;
  text-decoration: none;
  font-size: 13px;
  font-weight: 700;
  margin-top: 12px;
}

.empty-state,
.loading-state,
.error-state {
  text-align: center;
  padding: 80px 20px;
  color: #647067;
}

.loading-state i,
.error-state i {
  font-size: 32px;
  margin-bottom: 12px;
  opacity: 0.3;
}

@media (max-width: 768px) {
  .stat-cards {
    grid-template-columns: repeat(2, 1fr);
  }

  .upcoming-card {
    flex-direction: column;
    text-align: center;
  }

  .upcoming-deadline {
    text-align: center;
    margin-left: 0;
    margin-top: 12px;
  }
}
</style>
