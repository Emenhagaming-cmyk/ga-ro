<template>
  <div class="search-page">
    <CareerSearchBar v-model="searchQuery" @search="fetchJobs" />
    <CareerFilterChips :chips="allChips" v-model="activeChip" />

    <div class="results-bar">
      <span class="results-count">Menampilkan {{ filteredJobs.length }} Lowongan</span>
      <div class="sort-group">
        <span class="sort-label">Urutkan:</span>
        <select v-model="sortBy" class="sort-select" @change="fetchJobs">
          <option value="newest">Terbaru</option>
          <option value="company">Perusahaan</option>
          <option value="type">Tipe</option>
        </select>
      </div>
    </div>

    <div class="jobs-list">
      <CareerJobCard
        v-for="job in filteredJobs"
        :key="job.id"
        :job="job"
        @apply="openApply"
      />
    </div>

    <div v-if="filteredJobs.length === 0 && !loading" class="empty-state">
      <i class="fas fa-magnifying-glass"></i>
      <p>Tidak ada lowongan yang cocok.</p>
    </div>

    <div v-if="loading" class="loading-state">
      <i class="fas fa-spinner fa-spin"></i> Memuat lowongan...
    </div>
  </div>

  <ApplyModal v-if="applyJob" :job="applyJob" @close="applyJob = null" />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthSession } from '@/composable/useAuthSession'
import CareerSearchBar from '@/components/career/CareerSearchBar.vue'
import CareerFilterChips from '@/components/career/CareerFilterChips.vue'
import CareerJobCard from '@/components/career/CareerJobCard.vue'
import ApplyModal from '@/components/career/ApplyModal.vue'

const { BACKEND } = useAuthSession()

const searchQuery = ref('')
const activeChip = ref('')
const sortBy = ref('newest')
const jobs = ref([])
const loading = ref(true)
const applyJob = ref(null)

const allChips = ['Semua', 'Magang', 'Kerja', 'BKK', 'RPL', 'TKJ', 'AKL']

async function fetchJobs() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (searchQuery.value) params.set('search', searchQuery.value)
    if (sortBy.value) params.set('sort', sortBy.value)
    if (activeChip.value && activeChip.value !== 'Semua') {
      if (['Magang', 'Kerja', 'BKK'].includes(activeChip.value)) params.set('type', activeChip.value)
      else params.set('jurusan', activeChip.value)
    }
    const res = await fetch(`${BACKEND}/lowongan?${params}`, { credentials: 'include' })
    jobs.value = await res.json()
  } catch (e) {
    jobs.value = []
  } finally {
    loading.value = false
  }
}

const filteredJobs = computed(() => {
  let result = [...jobs.value]
  if (activeChip.value && activeChip.value !== 'Semua') {
    const chip = activeChip.value
    if (['Magang', 'Kerja', 'BKK'].includes(chip)) {
      result = result.filter(j => j.type === chip)
    } else {
      result = result.filter(j => j.jurusan === chip)
    }
  }
  return result
})

function openApply(job) {
  applyJob.value = job
}

onMounted(fetchJobs)
</script>

<style scoped>
.results-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  flex-wrap: wrap;
  gap: 12px;
}

.results-count {
  font-size: 14px;
  font-weight: 700;
  color: var(--color-text-secondary);
}

.sort-group {
  display: flex;
  align-items: center;
  gap: 8px;
}

.sort-label {
  font-size: 13px;
  color: var(--color-text-secondary);
}

.sort-select {
  padding: 8px 14px;
  border-radius: 10px;
  border: 1px solid var(--color-border);
  background: #fff;
  font-size: 13px;
  font-weight: 600;
  font-family: inherit;
}

.jobs-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.empty-state,
.loading-state {
  text-align: center;
  padding: 60px 20px;
  color: var(--color-text-secondary);
}

.empty-state i {
  font-size: 48px;
  margin-bottom: 16px;
  opacity: 0.3;
}

.loading-state i {
  font-size: 20px;
  margin-right: 8px;
}
</style>
