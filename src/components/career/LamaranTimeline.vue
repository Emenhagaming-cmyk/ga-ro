<template>
  <div class="timeline">
    <div class="timeline-item" v-for="(item, index) in timelineSteps" :key="index">
      <div class="timeline-step">
        <div class="step-indicator" :class="{ active: isCurrent(item.status) }">
          <i :class="stepIcon(item.status)"></i>
        </div>
        <div class="step-content">
          <h4 class="step-title">{{ stepLabel(item.status) }}</h4>
          <span class="step-status">{{ statusLabel(item.status) }}</span>
          <span class="step-date">{{ formatDate(item.created_at) }}</span>
          <p v-if="item.catatan" class="step-note">{{ item.catatan }}</p>
          <p v-if="item.actionBy" class="step-actor">oleh {{ item.actionBy?.name || 'Admin' }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  histories: {
    type: Array,
    default: () => []
  }
})

const timelineSteps = [
  { status: 'pending', label: 'Lamaran Terkirim', icon: 'fa-paper-plane' },
  { status: 'pending', label: 'Diproses', icon: 'fa-clock' },
  { status: 'diterima', label: 'Diterima', icon: 'fa-check-circle' },
  { status: 'ditolak', label: 'Ditolak', icon: 'fa-xmark-circle' },
  { status: 'dibatalkan', label: 'Dibatalkan', icon: 'fa-ban' },
]

function stepIcon(status) {
  switch (status) {
    case 'pending': return 'fas fa-hourglass'
    case 'diterima': return 'fas fa-check-circle'
    case 'ditolak': return 'fas fa-xmark-circle'
    case 'dibatalkan': return 'fas fa-ban'
    default: return 'fas fa-circle'
  }
}

function statusLabel(status) {
  const map = {
    pending: 'Pending',
    diterima: 'Diterima',
    ditolak: 'Ditolak',
    dibatalkan: 'Dibatalkan'
  }
  return map[status] || status
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

function isCurrent(status) {
  if (!props.histories || props.histories.length === 0) return false
  const latest = props.histories[0]
  return latest.status_to === status
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

.step-status {
  display: inline-block;
  padding: 2px 10px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 700;
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
