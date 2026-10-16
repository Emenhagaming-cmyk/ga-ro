<template>
  <div class="timeline">
    <div class="timeline-item" v-for="(item, index) in steps" :key="item.key">
      <div class="timeline-step">
        <div class="step-indicator" :class="{ active: index === 0 }">
          <i :class="stepIcon(item.status)"></i>
        </div>
        <div class="step-content">
          <h4 class="step-title">{{ stepLabel(item.status) }}</h4>
          <span v-if="item.created_at" class="step-date">{{ formatDate(item.created_at) }}</span>
          <p v-if="item.catatan" class="step-note">{{ item.catatan }}</p>
          <p v-if="item.actionBy" class="step-actor">oleh {{ item.actionBy?.name || 'Admin' }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  histories: {
    type: Array,
    default: () => []
  }
})

// Satu baris per perubahan status asli (terbaru dulu). Histori kosong = lamaran
// baru masuk dan belum disentuh admin, jadi tampilkan satu baris "diproses".
const steps = computed(() => {
  const rows = props.histories
    .slice()
    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
    .map((h) => ({
      key: `h${h.id}`,
      status: h.status_to,
      created_at: h.created_at,
      catatan: h.catatan,
      actionBy: h.actionBy,
    }))

  if (rows.length === 0) {
    rows.push({ key: 'baru', status: 'pending', catatan: 'Menunggu ditinjau BKK sekolah.' })
  }

  return rows
})

function stepIcon(status) {
  switch (status) {
    case 'pending': return 'fas fa-hourglass'
    case 'diterima': return 'fas fa-check-circle'
    case 'ditolak': return 'fas fa-xmark-circle'
    case 'dibatalkan': return 'fas fa-ban'
    default: return 'fas fa-circle'
  }
}

function stepLabel(status) {
  const map = {
    pending: 'Dalam Proses',
    diterima: 'Diterima',
    ditolak: 'Ditolak',
    dibatalkan: 'Dibatalkan'
  }
  return map[status] || status
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>

<style scoped>
.timeline {
  margin: 16px 0;
}

.timeline-item {
  position: relative;
  padding-left: 40px;
  margin-bottom: 16px;
}

.timeline-item:not(:last-child)::before {
  content: '';
  position: absolute;
  top: 32px;
  left: 16px;
  width: 2px;
  height: calc(100% + 16px);
  background: #e5e7e6;
}

.timeline-step {
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.step-indicator {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f0f4ef;
  color: #647067;
  font-size: 14px;
  flex-shrink: 0;
  border: 2px solid transparent;
  transition: all 0.2s;
  z-index: 1;
}

.step-indicator.active {
  background: #e8f0e6;
  color: #3a6450;
  border-color: #3a6450;
  box-shadow: 0 0 0 4px rgba(58, 100, 80, 0.1);
}

.step-content {
  flex: 1;
}

.step-title {
  font-size: 14px;
  font-weight: 700;
  color: #1c2a23;
  margin: 0 0 4px;
}

.step-date {
  font-size: 11px;
  color: #96a098;
  margin-left: 8px;
}

.step-note {
  font-size: 12px;
  color: #647067;
  margin: 4px 0 0;
  line-height: 1.4;
}

.step-actor {
  font-size: 11px;
  color: #96a098;
  font-style: italic;
}
</style>
