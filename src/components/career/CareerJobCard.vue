<template>
  <article class="job-row">
    <div class="row-logo" :style="{ background: typeColor + '15', color: typeColor }">
      {{ job.company.charAt(0) }}
    </div>

    <div class="row-info">
      <h3 class="row-title">{{ job.title }}</h3>
      <span class="row-company" :style="{ color: typeColor }">{{ job.company }}</span>
    </div>

    <div class="row-meta">
      <div class="meta-item">
        <div class="meta-icon" :style="{ background: typeColor + '18', color: typeColor }">
          <i class="fas fa-briefcase"></i>
        </div>
        <div>
          <span class="meta-value">{{ job.type }}</span>
          <span class="meta-label">Tipe</span>
        </div>
      </div>
      <div class="meta-item">
        <div class="meta-icon meta-icon-loc">
          <i class="fas fa-location-dot"></i>
        </div>
        <div>
          <span class="meta-value">{{ job.location }}</span>
          <span class="meta-label">Lokasi</span>
        </div>
      </div>
    </div>

    <div class="row-actions">
      <button class="btn-lamar" @click.stop="$emit('apply', job)">
        <i class="fas fa-paper-plane"></i> Lamar
      </button>
      <button class="btn-bookmark" @click.stop="$emit('bookmark', job)">
        <i class="far fa-bookmark"></i>
      </button>
    </div>
  </article>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({ job: { type: Object, required: true } })
defineEmits(['apply', 'bookmark'])

const typeColor = computed(() => {
  switch (props.job.type) {
    case 'Magang': return '#3a6450'
    case 'Kerja': return '#4f8cc9'
    case 'BKK': return '#7c5cbf'
    default: return '#3a6450'
  }
})
</script>

<style scoped>
.job-row {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 18px 22px;
  background: #fff;
  border-radius: var(--radius-lg, 20px);
  border: 1px solid var(--border, #dfe4dd);
  box-shadow: var(--shadow-sm, 0 4px 12px rgba(35,55,42,.05));
  transition: background 0.2s, box-shadow 0.2s;
  cursor: pointer;
}
.job-row:hover {
  background: #f8faf6;
  box-shadow: var(--shadow, 0 12px 28px rgba(35,55,42,.07));
}

.row-logo {
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

.row-info {
  flex: 1;
  min-width: 0;
}

.row-title {
  font-size: 15px;
  font-weight: 800;
  color: var(--text, #1c2a23);
  margin: 0;
}

.row-company {
  font-size: 12px;
  font-weight: 700;
}

.row-meta {
  display: flex;
  gap: 24px;
  flex-shrink: 0;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 10px;
}

.meta-icon {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  flex-shrink: 0;
}

.meta-icon-loc {
  background: rgba(232, 101, 60, 0.12);
  color: #e8653c;
}

.meta-value {
  display: block;
  font-size: 13px;
  font-weight: 700;
  color: var(--text, #1c2a23);
}

.meta-label {
  font-size: 11px;
  color: var(--text-secondary, #647067);
}

.row-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}

.btn-lamar {
  padding: 10px 20px;
  border-radius: var(--radius-pill, 999px);
  border: 1.5px solid var(--primary, #3a6450);
  background: transparent;
  color: var(--primary, #3a6450);
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  font-family: inherit;
  transition: all 0.2s;
  white-space: nowrap;
}
.btn-lamar:hover {
  background: var(--primary, #3a6450);
  color: #fff;
}

.btn-bookmark {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  border: 1px solid var(--border, #dfe4dd);
  background: #fff;
  color: var(--text-secondary, #647067);
  font-size: 14px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}
.btn-bookmark:hover {
  color: var(--primary, #3a6450);
  border-color: var(--primary, #3a6450);
}

@media (max-width: 900px) {
  .row-meta {
    display: none;
  }
  .job-row {
    padding: 14px 16px;
  }
}

@media (max-width: 600px) {
  .row-actions {
    flex-direction: column;
    gap: 4px;
  }
  .btn-lamar {
    padding: 8px 14px;
    font-size: 12px;
  }
  .btn-bookmark {
    width: 32px;
    height: 32px;
    font-size: 12px;
  }
}
</style>
