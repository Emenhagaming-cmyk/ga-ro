<template>
  <div class="applications-page">
    <div v-if="loading" class="loading-state">
      <i class="fas fa-spinner fa-spin"></i> Memuat lamaran...
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
      <div v-for="lamaran in lamarans" :key="lamaran.id" class="lamaran-row">
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
          {{ new Date(lamaran.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}
        </div>
        <div class="lamaran-actions">
          <button v-if="lamaran.status === 'pending'" class="btn-cancel" @click="cancelLamaran(lamaran)">
            <i class="fas fa-xmark"></i> Batalkan
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthSession } from '@/composable/useAuthSession'

const { BACKEND } = useAuthSession()
const lamarans = ref([])
const loading = ref(true)

async function fetchLamarans() {
  loading.value = true
  try {
    const res = await fetch(`${BACKEND}/lamaran/saya`, { credentials: 'include' })
    lamarans.value = await res.json()
  } catch (e) {
    lamarans.value = []
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

async function cancelLamaran(lamaran) {
  if (!confirm('Batalkan lamaran ini?')) return
  try {
    const res = await fetch(`${BACKEND}/lamaran/${lamaran.id}`, {
      method: 'DELETE',
      credentials: 'include',
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

.lamaran-row {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 18px 22px;
  background: #fff;
  border-radius: 20px;
  border: 1px solid var(--color-border);
  box-shadow: var(--shadow-sm);
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
}

.lamaran-actions {
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
}

.btn-cancel:hover {
  background: rgba(220, 38, 38, 0.05);
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
</style>
