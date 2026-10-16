<template>
  <div class="search-page">
    <div class="search-header">
      <CareerSearchBar v-model="searchQuery" @search="goToFirstPage" />
    </div>

    <CareerFilterChips :chips="allChips" v-model="activeChip" />

    <!-- Bar hasil + urutan -->
    <div class="results-bar">
      <p class="results-count" aria-live="polite">
        Menampilkan <strong>{{ paginatedJobs.length }}</strong> dari
        <strong>{{ totalJobs }}</strong> lowongan
      </p>

      <div class="sort-group">
        <label class="sort-label" for="sort-select">
          <i class="fas fa-arrow-down-short-wide" aria-hidden="true"></i>
          Urutkan
        </label>
        <div class="sort-select-wrapper">
          <select id="sort-select" v-model="sortBy" class="sort-select">
            <option value="newest">Terbaru</option>
            <option value="company">Perusahaan</option>
            <option value="type">Tipe</option>
          </select>
          <i class="fas fa-chevron-down sort-arrow" aria-hidden="true"></i>
        </div>
      </div>
    </div>

    <!-- Skeleton saat load pertama -->
    <div v-if="loading && paginatedJobs.length === 0" class="jobs-grid" aria-hidden="true">
      <div v-for="n in 6" :key="n" class="skeleton-card">
        <div class="sk-head">
          <div class="sk sk-logo"></div>
          <div class="sk-head-lines">
            <div class="sk sk-line sk-line--md"></div>
            <div class="sk sk-line sk-line--sm"></div>
          </div>
        </div>
        <div class="sk sk-line sk-line--lg"></div>
        <div class="sk sk-line sk-line--md"></div>
        <div class="sk-tags">
          <div class="sk sk-tag"></div>
          <div class="sk sk-tag sk-tag--short"></div>
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
        <i class="fas" :class="error ? 'fa-triangle-exclamation' : 'fa-magnifying-glass'" aria-hidden="true"></i>
      </div>
      <h3 class="empty-title">{{ error ? 'Gagal memuat lowongan' : 'Tidak ada lowongan yang cocok' }}</h3>
      <p class="empty-desc">{{ error ? 'Pastikan Anda masih login, lalu muat ulang halaman.' : 'Coba ubah kata kunci pencarian atau hapus filter yang aktif.' }}</p>
      <button v-if="!error" type="button" class="empty-btn" @click="resetFilters">
        <i class="fas fa-rotate-left" aria-hidden="true"></i> Reset pencarian
      </button>
    </div>

    <!-- Pagination -->
    <nav v-if="meta.last_page > 1" class="pagination" aria-label="Navigasi halaman">
      <button
        type="button"
        class="page-btn"
        :disabled="!meta.prev_page || loading"
        aria-label="Halaman sebelumnya"
        @click="changePage(meta.current_page - 1)"
      >
        <i class="fas fa-chevron-left" aria-hidden="true"></i>
      </button>

      <template v-for="(p, i) in visiblePages" :key="i">
        <span v-if="typeof p === 'string'" class="page-ellipsis">{{ p }}</span>
        <button
          v-else
          type="button"
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
        type="button"
        class="page-btn"
        :disabled="!meta.next_page || loading"
        aria-label="Halaman berikutnya"
        @click="changePage(meta.current_page + 1)"
      >
        <i class="fas fa-chevron-right" aria-hidden="true"></i>
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

/* ------------------------------ state ------------------------------ */
const searchQuery = ref('')
const activeChip = ref('Semua')
const sortBy = ref('newest')
const currentPage = ref(1)

const paginatedJobs = ref([])
const totalJobs = ref(0)
const loading = ref(true)
const error = ref(false)
const applyJob = ref(null)
const bookmarkedIds = ref(new Set())
const meta = ref({
  current_page: 1,
  last_page: 1,
  per_page: 12,
  total: 0,
  prev_page: null,
  next_page: null,
})

const allChips = ['Semua', 'Magang', 'Kerja', 'BKK', 'RPL']
const TYPE_CHIPS = ['Magang', 'Kerja', 'BKK'] // chip yang dipetakan ke ?type=

let fetchId = 0
let searchTimer = null

/* ------------------------------ fetching ------------------------------ */
function buildParams() {
  const params = new URLSearchParams()
  params.set('page', currentPage.value)
  if (searchQuery.value) params.set('search', searchQuery.value)
  if (sortBy.value) params.set('sort', sortBy.value)

  if (activeChip.value && activeChip.value !== 'Semua') {
    const key = TYPE_CHIPS.includes(activeChip.value) ? 'type' : 'jurusan'
    params.set(key, activeChip.value)
  }
  return params
}

async function fetchJobs() {
  const id = ++fetchId
  loading.value = true

  try {
    const res = await fetchJson(`${BACKEND}/lowongan?${buildParams()}`)
    if (!res.ok) throw new Error('gagal')
    const data = await res.json()
    if (id !== fetchId) return // respons lama — abaikan

    error.value = false
    paginatedJobs.value = data.data || []
    totalJobs.value = data.meta?.total || paginatedJobs.value.length
    meta.value = {
      current_page: data.meta?.current_page ?? 1,
      last_page: data.meta?.last_page ?? 1,
      per_page: data.meta?.per_page ?? 12,
      total: data.meta?.total ?? 0,
      prev_page: data.links?.prev ? new URL(data.links.prev, BACKEND).searchParams.get('page') : null,
      next_page: data.links?.next ? new URL(data.links.next, BACKEND).searchParams.get('page') : null,
    }
  } catch (e) {
    if (id !== fetchId) return
    console.error('Gagal memuat lowongan:', e)
    error.value = true
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
  } catch {
    bookmarkedIds.value = new Set()
  }
}

/* ------------------------------ bookmark ------------------------------ */
const isBookmarked = (id) => bookmarkedIds.value.has(id)

async function toggleBookmark(job) {
  const marked = isBookmarked(job.id)

  try {
    const res = marked
      ? await fetchJson(`${BACKEND}/bookmarks/${job.id}`, {
          method: 'DELETE',
          headers: { 'X-CSRF-TOKEN': await getCsrfToken() },
        })
      : await fetchJson(`${BACKEND}/bookmarks`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': await getCsrfToken(),
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ lowongan_id: job.id }),
        })

    if (res.ok) {
      marked ? bookmarkedIds.value.delete(job.id) : bookmarkedIds.value.add(job.id)
    }
  } catch {
    // senyap — error jaringan/auth, state tidak berubah
  }
}

/* ------------------------------ navigasi & filter ------------------------------ */
function scrollTop() {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

// dipanggil saat submit pencarian (enter / tombol) → fetch langsung tanpa debounce
function goToFirstPage() {
  clearTimeout(searchTimer)
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
  // fetch ditangani watcher di bawah — tidak perlu manual (menghindari double-fetch)
}

/* ------------------------------ reaksi filter (debounce) ------------------------------ */
// satu watcher untuk semua filter → sumber kebenaran tunggal
watch([searchQuery, activeChip, sortBy], () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    currentPage.value = 1
    fetchJobs()
    scrollTop()
  }, 350)
})

/* ------------------------------ pagination ------------------------------ */
// pola: 1 … (cur-1) cur (cur+1) … last
const visiblePages = computed(() => {
  const { last_page: last, current_page: cur } = meta.value
  if (last <= 7) return Array.from({ length: last }, (_, i) => i + 1)

  const pages = [1]
  if (cur > 3) pages.push('…')
  for (let i = Math.max(2, cur - 1); i <= Math.min(last - 1, cur + 1); i++) pages.push(i)
  if (cur < last - 2) pages.push('…')
  pages.push(last)
  return pages
})

/* ------------------------------ lifecycle ------------------------------ */
onMounted(() => {
  fetchJobs()
  fetchBookmarks()
})
</script>

<style scoped>
/* ============================================================
   Token desain lokal — ubah di satu tempat saja
   ============================================================ */
.search-page {
  --sp-primary: #3a6450;
  --sp-primary-dark: #2c5040;
  --sp-primary-soft: #eef4f0;
  --sp-ring: rgb(58 100 80 / 15%);
  --sp-text: var(--color-text, #24312a);
  --sp-text-muted: var(--color-text-secondary, #647067);
  --sp-border: var(--color-border, #e5e7e6);
  --sp-surface: #fff;
  --sp-radius: 14px;
}

/* ---------- Search header ---------- */
.search-header {
  margin-bottom: 18px;
}

/* ---------- Results bar ---------- */
.results-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px 16px;
  padding: 14px 18px;
  margin: 20px 0 22px;
  background: var(--sp-surface);
  border: 1px solid var(--sp-border);
  border-radius: var(--sp-radius);
  box-shadow: 0 1px 2px rgb(20 30 24 / 4%);
}

.results-count {
  margin: 0;
  font-size: 13.5px;
  color: var(--sp-text-muted);
}

.results-count strong {
  color: var(--sp-primary);
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
  color: var(--sp-text-muted);
  white-space: nowrap;
  cursor: pointer;
}

.sort-select-wrapper {
  position: relative;
}

.sort-select {
  appearance: none;
  width: 100%;
  padding: 9px 34px 9px 14px;
  border: 1px solid var(--sp-border);
  border-radius: 10px;
  background: var(--sp-surface);
  font: inherit;
  font-size: 13px;
  font-weight: 600;
  color: var(--sp-text);
  cursor: pointer;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.sort-select:hover {
  border-color: var(--sp-primary);
}

.sort-select:focus-visible {
  outline: none;
  border-color: var(--sp-primary);
  box-shadow: 0 0 0 3px var(--sp-ring);
}

.sort-arrow {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 11px;
  color: var(--sp-text-muted);
  pointer-events: none;
}

/* ---------- Jobs grid ---------- */
.jobs-grid {
  display: flex;
  flex-direction: column;
  gap: 12px;
  transition: opacity 0.25s ease;
}

.jobs-grid > * {
  animation: fade-up 0.35s ease both;
}

.jobs-grid.is-refreshing {
  opacity: 0.45;
  pointer-events: none;
}

@keyframes fade-up {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* ---------- Skeleton (meniru struktur kartu asli) ---------- */
.skeleton-card {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 20px;
  background: var(--sp-surface);
  border: 1px solid var(--sp-border);
  border-radius: var(--sp-radius);
}

.sk {
  background: linear-gradient(90deg, #eef1ef 25%, #f7f9f8 50%, #eef1ef 75%);
  background-size: 200% 100%;
  border-radius: 8px;
  animation: shimmer 1.4s infinite;
}

.sk-head {
  display: flex;
  align-items: center;
  gap: 12px;
}

.sk-head-lines {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.sk-logo {
  flex-shrink: 0;
  width: 48px;
  height: 48px;
  border-radius: 12px;
}

.sk-line {
  height: 12px;
}

.sk-line--lg { width: 85%; }
.sk-line--md { width: 60%; }
.sk-line--sm { width: 40%; }

.sk-tags {
  display: flex;
  gap: 8px;
  margin-top: 2px;
}

.sk-tag {
  width: 72px;
  height: 24px;
  border-radius: 999px;
}

.sk-tag--short {
  width: 52px;
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
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 56px 24px;
  margin-top: 8px;
  background: var(--sp-surface);
  border: 1.5px dashed var(--sp-border);
  border-radius: 18px;
}

.empty-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 76px;
  height: 76px;
  margin-bottom: 18px;
  border-radius: 50%;
  background: var(--sp-primary-soft);
}

.empty-icon i {
  font-size: 26px;
  color: var(--sp-primary);
  opacity: 0.65;
}

.empty-title {
  margin: 0 0 6px;
  font-size: 17px;
  font-weight: 800;
  color: var(--sp-text);
}

.empty-desc {
  max-width: 420px;
  margin: 0 0 22px;
  font-size: 14px;
  line-height: 1.6;
  color: var(--sp-text-muted);
}

.empty-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 11px 22px;
  border: none;
  border-radius: 10px;
  background: var(--sp-primary);
  color: #fff;
  font: inherit;
  font-size: 13.5px;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
}

.empty-btn:hover {
  background: var(--sp-primary-dark);
  box-shadow: 0 6px 14px rgb(58 100 80 / 25%);
}

.empty-btn:active {
  transform: scale(0.97);
}

.empty-btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px var(--sp-ring);
}

/* ---------- Pagination ---------- */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
  gap: 6px;
  margin-top: 30px;
}

.page-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 38px;
  height: 38px;
  padding: 0 10px;
  border: 1px solid var(--sp-border);
  border-radius: 10px;
  background: var(--sp-surface);
  font: inherit;
  font-size: 13.5px;
  font-weight: 700;
  color: var(--sp-primary);
  cursor: pointer;
  transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
}

.page-btn:hover:not(:disabled):not(.active) {
  border-color: var(--sp-primary);
  background: var(--sp-primary-soft);
}

.page-btn.active {
  border-color: var(--sp-primary);
  background: var(--sp-primary);
  color: #fff;
  box-shadow: 0 4px 10px rgb(58 100 80 / 25%);
  cursor: default;
}

.page-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.page-btn:focus-visible {
  outline: none;
  border-color: var(--sp-primary);
  box-shadow: 0 0 0 3px var(--sp-ring);
}

.page-ellipsis {
  min-width: 24px;
  text-align: center;
  font-weight: 700;
  color: var(--sp-text-muted);
  user-select: none;
}

/* ---------- Responsive ---------- */
@media (max-width: 640px) {
  .results-bar {
    padding: 12px 14px;
  }

  .sort-group {
    width: 100%;
  }

  .sort-select-wrapper {
    flex: 1;
  }

  .empty-state {
    padding: 44px 18px;
  }

  .page-btn {
    min-width: 36px;
    height: 36px;
  }
}

/* ---------- Aksesibilitas: hormati preferensi reduksi motion ---------- */
@media (prefers-reduced-motion: reduce) {
  .jobs-grid > *,
  .sk {
    animation: none;
  }
}
</style>