<template>
  <div class="applications-page">
    <div v-if="loading" class="loading-state">
      <i class="fas fa-spinner fa-spin"></i> Memuat lamaran...
    </div>

    <div v-else-if="error" class="empty-state">
      <i class="fas fa-triangle-exclamation"></i>
      <h3>Gagal memuat lamaran</h3>
      <p>Pastikan Anda masih login, lalu muat ulang halaman.</p>
    </div>

    <div v-else-if="lamarans.length === 0" class="empty-state">
      <i class="fas fa-file-lines"></i>
      <h3>Belum ada lamaran</h3>
      <p>Anda belum mengirim lamaran ke perusahaan manapun.</p>
      <router-link to="/career-center/search" class="btn-browse">
        <i class="fas fa-magnifying-glass"></i> Cari Lowongan
      </router-link>
    </div>

    <div v-else class="lamaran-list">
      <div v-for="lamaran in lamarans" :key="lamaran.id" class="lamaran-card">
        <div class="lamaran-row" @click="toggleExpand(lamaran.id)">
          <div class="lamaran-logo" :style="{ background: getColor(lamaran.lowongan?.type) + '15', color: getColor(lamaran.lowongan?.type) }">
            {{ (lamaran.lowongan?.company || '?').charAt(0) }}
          </div>
          <div class="lamaran-info">
            <h4>{{ lamaran.lowongan?.title || 'Lowongan dihapus' }}</h4>
            <span class="lamaran-company" :style="{ color: getColor(lamaran.lowongan?.type) }">{{ lamaran.lowongan?.company || '-' }}</span>
          </div>
          <div class="lamaran-status">
            <span class="status-badge" :class="'status-' + lamaran.status">{{ statusLabel(lamaran.status) }}</span>
          </div>
          <div class="lamaran-date">
            {{ formatDate(lamaran.created_at) }}
          </div>
          <div class="lamaran-actions" @click.stop>
            <button v-if="lamaran.status === 'pending'" class="btn-cancel" @click="cancelLamaran(lamaran)">
              <i class="fas fa-xmark"></i> Batalkan
            </button>
            <button class="btn-expand" :class="{ open: expandedId === lamaran.id }" type="button">
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>

        <div v-if="expandedId === lamaran.id" class="lamaran-detail">
          <LamaranTimeline :histories="lamaran.status_histories || []" />
          <div v-if="lamaran.catatan_admin" class="admin-note">
            <strong>Catatan Admin:</strong> {{ lamaran.catatan_admin }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthSession } from '@/composable/useAuthSession'
import { getCsrfToken } from '@/services/csrf'
import { fetchJson } from '@/services/fetchJson'
import LamaranTimeline from '@/components/career/LamaranTimeline.vue'

const { BACKEND } = useAuthSession()
const lamarans = ref([])
const loading = ref(true)
const error = ref(false)
const expandedId = ref(null)

async function fetchLamarans() {
  loading.value = true
  error.value = false
  try {
    const res = await fetchJson(`${BACKEND}/lamaran/saya`)
    if (!res.ok) throw new Error('gagal')
    const data = await res.json()
    lamarans.value = data.map(l => ({
      ...l,
      status_histories: l.status_histories || []
    }))
  } catch (e) {
    lamarans.value = []
    error.value = true
  } finally {
    loading.value = false
  }
}

function getColor(type) {
  if (type === 'Magang') return '#3a6450'
  if (type === 'Kerja') return '#4f8cc9'
  if (type === 'BKK') return '#7c5cbf'
  return '#3a6450'
}

function statusLabel(s) {
  return { pending: 'Diproses', diterima: 'Diterima', ditolak: 'Ditolak', dibatalkan: 'Dibatalkan' }[s] || s
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

function toggleExpand(id) {
  expandedId.value = expandedId.value === id ? null : id
}

async function cancelLamaran(lamaran) {
  if (!confirm('Batalkan lamaran ini?')) return
  try {
    const res = await fetchJson(`${BACKEND}/lamaran/${lamaran.id}`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': await getCsrfToken() },
    })
    if (res.ok) fetchLamarans()
  } catch (e) {}
}

onMounted(fetchLamarans)
</script>

<style scoped>
.lamaran-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.lamaran-card {
  background: #fff;
  border-radius: 20px;
  border: 1px solid var(--color-border);
  box-shadow: var(--shadow-sm);
  overflow: hidden;
}

.lamaran-row {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 18px 22px;
  cursor: pointer;
  transition: background 0.2s;
}

.lamaran-row:hover {
  background: #f8faf6;
}

.lamaran-logo {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  font-weight: 800;
  flex-shrink: 0;
}

.lamaran-info {
  flex: 1;
  min-width: 0;
}

.lamaran-info h4 {
  font-size: 15px;
  font-weight: 800;
  color: var(--color-text);
  margin: 0;
}

.lamaran-company {
  font-size: 12px;
  font-weight: 700;
}

.lamaran-status {
  flex-shrink: 0;
}

.status-badge {
  padding: 5px 14px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
}

.status-pending {
  background: rgba(243, 156, 18, 0.1);
  color: #f39c12;
}

.status-diterima {
  background: rgba(42, 82, 56, 0.1);
  color: #2a5238;
}

.status-ditolak {
  background: rgba(220, 38, 38, 0.1);
  color: #dc2626;
}

.status-dibatalkan {
  background: rgba(100, 112, 103, 0.1);
  color: #647067;
}

.lamaran-date {
  font-size: 13px;
  color: var(--color-text-secondary);
  flex-shrink: 0;
  white-space: nowrap;
}

.lamaran-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}

.btn-cancel {
  padding: 7px 14px;
  border-radius: 10px;
  border: 1px solid rgba(220, 38, 38, 0.2);
  background: transparent;
  color: #dc2626;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-cancel:hover {
  background: rgba(220, 38, 38, 0.05);
}

.btn-expand {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  border: 1px solid var(--color-border);
  background: #fff;
  color: #647067;
  font-size: 12px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.btn-expand:hover {
  background: #f0f4ef;
}

.btn-expand.open {
  color: #3a6450;
  transform: rotate(180deg);
}

.lamaran-detail {
  padding: 16px 22px;
  border-top: 1px solid #e5e7e6;
}

.admin-note {
  margin-top: 16px;
  padding: 12px 16px;
  background: #fffbeb;
  border: 1px solid #fde6a1;
  border-radius: 12px;
  font-size: 13px;
  color: #92400e;
}

.empty-state {
  text-align: center;
  padding: 80px 20px;
  color: var(--color-text-secondary);
}

.empty-state i {
  font-size: 48px;
  margin-bottom: 16px;
  opacity: 0.2;
}

.empty-state h3 {
  font-size: 20px;
  color: var(--color-text);
  margin-bottom: 8px;
}

.btn-browse {
  display: inline-flex;
  gap: 8px;
  padding: 12px 24px;
  border-radius: 16px;
  background: var(--color-primary);
  color: #fff;
  text-decoration: none;
  font-weight: 700;
  font-size: 14px;
}

.loading-state {
  text-align: center;
  padding: 60px;
  color: var(--color-text-secondary);
}

@media (max-width: 600px) {
  .lamaran-row {
    flex-wrap: wrap;
    gap: 8px;
    padding: 14px 16px;
  }

  .lamaran-date {
    display: none;
  }

  .lamaran-info {
    flex: 1 0 100%;
  }
}
</style>
