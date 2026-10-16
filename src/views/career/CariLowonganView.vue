<template>
  <div class="search-page">
    <div class="search-header">
      <CareerSearchBar v-model="searchQuery" @search="goToFirstPage" />
    </div>

    <CareerFilterChips :chips="allChips" v-model="activeChip" />

    <div class="results-bar">
      <span class="results-count">
        Menampilkan <strong>{{ paginatedJobs.length }}</strong> dari
        <strong>{{ totalJobs }}</strong> lowongan
      </span>

      <div class="sort-group">
        <span class="sort-label">
          <i class="fas fa-arrow-down-short-wide"></i>
          Urutkan
        </span>
        <div class="sort-select-wrapper">
          <select v-model="sortBy" class="sort-select" @change="goToFirstPage">
            <option value="newest">Terbaru</option>
            <option value="company">Perusahaan</option>
            <option value="type">Tipe</option>
          </select>
          <i class="fas fa-chevron-down sort-arrow"></i>
        </div>
      </div>
    </div>

    <!-- Skeleton saat load pertama -->
    <div v-if="loading && paginatedJobs.length === 0" class="jobs-grid" aria-hidden="true">
      <div v-for="n in 6" :key="n" class="skeleton-card">
        <div class="sk sk-logo"></div>
        <div class="sk sk-line" style="width: 65%"></div>
        <div class="sk sk-line" style="width: 45%"></div>
        <div class="sk sk-line" style="width: 80%"></div>
        <div class="sk-tags">
          <div class="sk sk-tag"></div>
          <div class="sk sk-tag"></div>
        </div>
      </div>
    </div>

    <!-- Daftar lowongan -->
    <div v-else class="jobs-grid" :class="{ 'is-refreshing': loading }">
      <CareerJobCard
        v-for="job in paginatedJobs"
        :key="job.id"
        :job="job"
        :bookmarked="isBookmarked(job.id)"
        @apply="openApply"
        @toggle-bookmark="toggleBookmark"
      />
    </div>

    <!-- Empty state -->
    <div v-if="!loading && paginatedJobs.length === 0" class="empty-state">
      <div class="empty-icon">
        <i class="fas fa-magnifying-glass"></i>
      </div>
      <h3 class="empty-title">Tidak ada lowongan yang cocok</h3>
      <p class="empty-desc">Coba ubah kata kunci pencarian atau hapus filter yang aktif.</p>
      <button class="empty-btn" @click="resetFilters">
        <i class="fas fa-rotate-left"></i> Reset pencarian
      </button>
    </div>

    <!-- Pagination -->
    <nav v-if="meta.last_page > 1" class="pagination" aria-label="Navigasi halaman">
      <button
        class="page-btn"
        :disabled="!meta.prev_page || loading"
        aria-label="Halaman sebelumnya"
        @click="changePage(meta.current_page - 1)"
      >
        <i class="fas fa-chevron-left"></i>
      </button>

      <template v-for="(p, i) in visiblePages" :key="i">
        <span v-if="typeof p === 'string'" class="page-ellipsis">{{ p }}</span>
        <button
          v-else
          class="page-btn"
          :class="{ active: p === meta.current_page }"
          :disabled="loading"
          :aria-current="p === meta.current_page ? 'page' : undefined"
          @click="changePage(p)"
        >
          {{ p }}
        </button>
      </template>

      <button
        class="page-btn"
        :disabled="!meta.next_page || loading"
        aria-label="Halaman berikutnya"
        @click="changePage(meta.current_page + 1)"
      >
        <i class="fas fa-chevron-right"></i>
      </button>
    </nav>
  </div>

  <ApplyModal v-if="applyJob" :job="applyJob" @close="applyJob = null" />
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useAuthSession } from '@/composable/useAuthSession'
import { fetchJson } from '@/services/fetchJson'
import { getCsrfToken } from '@/services/csrf'
import CareerSearchBar from '@/components/career/CareerSearchBar.vue'
import CareerFilterChips from '@/components/career/CareerFilterChips.vue'
import CareerJobCard from '@/components/career/CareerJobCard.vue'
import ApplyModal from '@/components/career/ApplyModal.vue'

const { BACKEND } = useAuthSession()

const searchQuery = ref('')
const activeChip = ref('')
const sortBy = ref('newest')
const currentPage = ref(1)
const paginatedJobs = ref([])
const totalJobs = ref(0)
const loading = ref(true)
const applyJob = ref(null)
const meta = ref({ current_page: 1, last_page: 1, per_page: 12, total: 0, prev_page: null, next_page: null })
const bookmarkedIds = ref(new Set())

const allChips = ['Semua', 'Magang', 'Kerja', 'BKK', 'RPL']

let fetchId = 0
let searchTimer = null

function buildParams() {
  const params = new URLSearchParams()
  params.set('page', currentPage.value)
  if (searchQuery.value) params.set('search', searchQuery.value)
  if (sortBy.value) params.set('sort', sortBy.value)
  if (activeChip.value && activeChip.value !== 'Semua') {
    if (['Magang', 'Kerja', 'BKK'].includes(activeChip.value)) params.set('type', activeChip.value)
    else params.set('jurusan', activeChip.value)
  }
  return params
}

async function fetchJobs() {
  const id = ++fetchId
  loading.value = true
  try {
    const res = await fetchJson(`${BACKEND}/lowongan?${buildParams()}`)
    const data = await res.json()
    if (id !== fetchId) return // respons lama, abaikan

    paginatedJobs.value = data.data || []
    totalJobs.value = data.meta?.total || paginatedJobs.value.length
    meta.value = {
      current_page: data.meta?.current_page || 1,
      last_page: data.meta?.last_page || 1,
      per_page: data.meta?.per_page || 12,
      total: data.meta?.total || 0,
      prev_page: data.links?.prev ? new URL(data.links.prev, BACKEND).searchParams.get('page') : null,
      next_page: data.links?.next ? new URL(data.links.next, BACKEND).searchParams.get('page') : null,
    }
  } catch (e) {
    if (id !== fetchId) return
    paginatedJobs.value = []
    totalJobs.value = 0
  } finally {
    if (id === fetchId) loading.value = false
  }
}

async function fetchBookmarks() {
  try {
    const res = await fetchJson(`${BACKEND}/bookmarks`)
    const data = await res.json()
    bookmarkedIds.value = new Set(data.map((b) => b.lowongan_id))
  } catch (e) {
    bookmarkedIds.value = new Set()
  }
}

function isBookmarked(lowonganId) {
  return bookmarkedIds.value.has(lowonganId)
}

async function toggleBookmark(job) {
  try {
    if (isBookmarked(job.id)) {
      const res = await fetchJson(`${BACKEND}/bookmarks/${job.id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': await getCsrfToken() },
      })
      if (res.ok) bookmarkedIds.value.delete(job.id)
    } else {
      const res = await fetchJson(`${BACKEND}/bookmarks`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': await getCsrfToken(),
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ lowongan_id: job.id }),
      })
      if (res.ok) bookmarkedIds.value.add(job.id)
    }
  } catch (e) {
    // silent — network/auth error, state tidak berubah
  }
}

function scrollTop() {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

function goToFirstPage() {
  currentPage.value = 1
  fetchJobs()
  scrollTop()
}

function changePage(page) {
  if (loading.value) return
  if (page < 1 || page > meta.value.last_page || page === meta.value.current_page) return
  currentPage.value = page
  fetchJobs()
  scrollTop()
}

function resetFilters() {
  searchQuery.value = ''
  activeChip.value = 'Semua'
  sortBy.value = 'newest'
  currentPage.value = 1
  fetchJobs()
}

// nomor halaman yang tampil: 1 … (cur-1) cur (cur+1) … last
const visiblePages = computed(() => {
  const last = meta.value.last_page
  const cur = meta.value.current_page
  if (last <= 7) return Array.from({ length: last }, (_, i) => i + 1)

  const pages = [1]
  if (cur > 3) pages.push('…')
  for (let i = Math.max(2, cur - 1); i <= Math.min(last - 1, cur + 1); i++) pages.push(i)
  if (cur < last - 2) pages.push('…')
  pages.push(last)
  return pages
})

watch(activeChip, () => {
  currentPage.value = 1
  fetchJobs()
})

// debounce 400ms agar tidak fetch di setiap ketikan
watch(searchQuery, () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    currentPage.value = 1
    fetchJobs()
  }, 400)
})

onMounted(() => {
  fetchJobs()
  fetchBookmarks()
})
</script>

<style scoped>
.search-header {
  margin-bottom: 16px;
}

/* ---------- Results bar ---------- */
.results-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  padding: 14px 18px;
  margin: 20px 0;
  background: #fff;
  border: 1px solid var(--color-border, #e5e7e6);
  border-radius: 14px;
}

.results-count {
  font-size: 13.5px;
  color: var(--color-text-secondary, #647067);
}

.results-count strong {
  color: var(--color-text, #24312a);
  font-weight: 800;
}

.sort-group {
  display: flex;
  align-items: center;
  gap: 10px;
}

.sort-label {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  font-weight: 600;
  color: var(--color-text-secondary, #647067);
}

.sort-select-wrapper {
  position: relative;
}

.sort-select {
  appearance: none;
  padding: 9px 36px 9px 14px;
  border-radius: 10px;
  border: 1px solid var(--color-border, #e5e7e6);
  background: #fff;
  font-size: 13px;
  font-weight: 600;
  font-family: inherit;
  color: var(--color-text, #24312a);
  cursor: pointer;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.sort-select:hover {
  border-color: #3a6450;
}

.sort-select:focus {
  outline: none;
  border-color: #3a6450;
  box-shadow: 0 0 0 3px rgba(58, 100, 80, 0.12);
}

.sort-arrow {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 11px;
  color: var(--color-text-secondary, #647067);
  pointer-events: none;
}

/* ---------- Jobs grid ---------- */
.jobs-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 16px;
  transition: opacity 0.25s ease;
}

.jobs-grid > * {
  animation: fadeUp 0.3s ease both;
}

.jobs-grid.is-refreshing {
  opacity: 0.45;
  pointer-events: none;
}

@keyframes fadeUp {
  from {
    opacity: 0;
    transform: translateY(8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* ---------- Skeleton ---------- */
.skeleton-card {
  background: #fff;
  border: 1px solid var(--color-border, #e5e7e6);
  border-radius: 16px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.sk {
  background: linear-gradient(90deg, #eef1ef 25%, #f7f9f8 50%, #eef1ef 75%);
  background-size: 200% 100%;
  animation: shimmer 1.4s infinite;
  border-radius: 8px;
}

.sk-logo {
  width: 48px;
  height: 48px;
  border-radius: 12px;
}

.sk-line {
  height: 12px;
}

.sk-tags {
  display: flex;
  gap: 8px;
  margin-top: 4px;
}

.sk-tag {
  width: 70px;
  height: 24px;
  border-radius: 999px;
}

@keyframes shimmer {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}

/* ---------- Empty state ---------- */
.empty-state {
  text-align: center;
  padding: 70px 20px;
}

.empty-icon {
  width: 76px;
  height: 76px;
  margin: 0 auto 20px;
  border-radius: 50%;
  background: #eef4f0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.empty-icon i {
  font-size: 28px;
  color: #3a6450;
  opacity: 0.6;
}

.empty-title {
  font-size: 17px;
  font-weight: 800;
  color: var(--color-text, #24312a);
  margin: 0 0 6px;
}

.empty-desc {
  font-size: 14px;
  color: var(--color-text-secondary, #647067);
  margin: 0 0 20px;
}

.empty-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  border: none;
  border-radius: 10px;
  background: #3a6450;
  color: #fff;
  font-size: 13.5px;
  font-weight: 700;
  font-family: inherit;
  cursor: pointer;
  transition: background 0.2s, transform 0.15s;
}

.empty-btn:hover {
  background: #2e5140;
}

.empty-btn:active {
  transform: scale(0.97);
}

/* ---------- Pagination ---------- */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 6px;
  margin-top: 28px;
  flex-wrap: wrap;
}

.page-btn {
  min-width: 38px;
  height: 38px;
  padding: 0 10px;
  border-radius: 10px;
  border: 1px solid var(--color-border, #e5e7e6);
  background: #fff;
  color: #3a6450;
  font-size: 13.5px;
  font-weight: 700;
  font-family: inherit;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.page-btn:hover:not(:disabled):not(.active) {
  border-color: #3a6450;
  background: #f2f7f4;
}

.page-btn.active {
  background: #3a6450;
  border-color: #3a6450;
  color: #fff;
  cursor: default;
}

.page-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.page-ellipsis {
  min-width: 24px;
  text-align: center;
  color: var(--color-text-secondary, #647067);
  font-weight: 700;
}
</style>