<template>
  <div class="bookmarks-page">
    <div class="page-header">
      <h2>Lowongan Tersimpan</h2>
      <p class="page-desc">Lowongan yang Anda simpan akan muncul di sini.</p>
    </div>

    <div v-if="loading" class="loading-state">
      <i class="fas fa-spinner fa-spin"></i> Memuat bookmark...
    </div>

    <div v-else-if="error" class="empty-state">
      <i class="fas fa-triangle-exclamation"></i>
      <h3>Gagal memuat bookmark</h3>
      <p>Pastikan Anda masih login, lalu muat ulang halaman.</p>
    </div>

    <div v-else-if="bookmarks.length === 0" class="empty-state">
      <i class="fas fa-bookmark"></i>
      <h3>Belum ada lowongan tersimpan</h3>
      <p>Jadwalkan pencarian Anda nanti dengan menyimpan lowongan ke bookmark.</p>
      <router-link to="/career-center/search" class="btn-browse">
        <i class="fas fa-magnifying-glass"></i> Cari Lowongan
      </router-link>
    </div>

    <div v-else class="bookmarks-list">
      <div
        v-for="bookmark in bookmarks"
        :key="bookmark.id"
        class="bookmark-row"
      >
        <div
          class="bookmark-logo"
          :style="{ background: getColor(bookmark.lowongan?.type) + '15', color: getColor(bookmark.lowongan?.type) }"
        >
          {{ (bookmark.lowongan?.company || '?').charAt(0) }}
        </div>
        <div class="bookmark-info">
          <h4>{{ bookmark.lowongan?.title || 'Lowongan dihapus' }}</h4>
          <span
            class="bookmark-company"
            :style="{ color: getColor(bookmark.lowongan?.type) }"
            >{{ bookmark.lowongan?.company || '-' }}</span
          >
        </div>
        <div class="bookmark-meta">
          <span class="bookmark-type">{{ bookmark.lowongan?.type || '-' }}</span>
          <span class="bookmark-location">{{ bookmark.lowongan?.location || '-' }}</span>
        </div>
        <div class="bookmark-date">
          Disimpan {{ formatDate(bookmark.created_at) }}
        </div>
        <div class="bookmark-actions">
          <button class="btn-apply" @click="applyJob(bookmark.lowongan)">
            <i class="fas fa-paper-plane"></i> Lamar
          </button>
          <button class="btn-unbookmark" @click="removeBookmark(bookmark)">
            <i class="fas fa-bookmark"></i> Hapus
          </button>
        </div>
      </div>
    </div>

    <ApplyModal v-if="applyJobSelected" :job="applyJobSelected" @close="applyJobSelected = null" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthSession } from '@/composable/useAuthSession'
import { fetchJson } from '@/services/fetchJson'
import { getCsrfToken } from '@/services/csrf'
import ApplyModal from '@/components/career/ApplyModal.vue'

const { BACKEND } = useAuthSession()
const bookmarks = ref([])
const loading = ref(true)
const error = ref(false)
const applyJobSelected = ref(null)

async function fetchBookmarks() {
  loading.value = true
  error.value = false
  try {
    const res = await fetchJson(`${BACKEND}/bookmarks`)
    if (!res.ok) throw new Error('gagal')
    bookmarks.value = await res.json()
  } catch (e) {
    bookmarks.value = []
    error.value = true
  } finally {
    loading.value = false
  }
}

async function removeBookmark(bookmark) {
  try {
    await fetchJson(`${BACKEND}/bookmarks/${bookmark.lowongan?.id}`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': await getCsrfToken() },
    })
    bookmarks.value = bookmarks.value.filter(b => b.id !== bookmark.id)
  } catch (e) {
    // error handled silently
  }
}

function applyJob(lowongan) {
  applyJobSelected.value = lowongan
}

function getColor(type) {
  if (type === 'Magang') return '#3a6450'
  if (type === 'Kerja') return '#4f8cc9'
  if (type === 'BKK') return '#7c5cbf'
  return '#3a6450'
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
}

onMounted(fetchBookmarks)
</script>

<style scoped>
.bookmarks-page {
  padding: 0;
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

.bookmarks-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.bookmark-row {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 18px 22px;
  background: #fff;
  border-radius: 20px;
  border: 1px solid var(--color-border);
  box-shadow: var(--shadow-sm);
}

.bookmark-logo {
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

.bookmark-info {
  flex: 1;
  min-width: 0;
}

.bookmark-info h4 {
  font-size: 15px;
  font-weight: 800;
  color: #1c2a23;
  margin: 0;
}

.bookmark-company {
  font-size: 12px;
  font-weight: 700;
}

.bookmark-meta {
  flex-shrink: 0;
  text-align: right;
  font-size: 12px;
}

.bookmark-type {
  display: block;
  font-weight: 700;
  color: #1c2a23;
}

.bookmark-location {
  display: block;
  color: #647067;
}

.bookmark-date {
  font-size: 12px;
  color: #647067;
  flex-shrink: 0;
  white-space: nowrap;
  margin: 0 16px;
}

.bookmark-actions {
  display: flex;
  gap: 8px;
  flex-shrink: 0;
}

.btn-apply {
  padding: 8px 16px;
  border-radius: 999px;
  border: 1.5px solid #3a6450;
  background: #3a6450;
  color: #fff;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-apply:hover {
  background: #2a5238;
}

.btn-unbookmark {
  padding: 8px 14px;
  border-radius: 10px;
  border: 1px solid #e5e7e6;
  background: #fff;
  color: #647067;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: all 0.2s;
}

.btn-unbookmark:hover {
  color: #3a6450;
  border-color: #3a6450;
  background: rgba(58, 100, 80, 0.06);
}

.empty-state {
  text-align: center;
  padding: 80px 20px;
  color: #647067;
}

.empty-state i {
  font-size: 48px;
  margin-bottom: 16px;
  opacity: 0.2;
}

.empty-state h3 {
  font-size: 20px;
  color: #1c2a23;
  margin-bottom: 8px;
}

.btn-browse {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  border-radius: 16px;
  background: #3a6450;
  color: #fff;
  text-decoration: none;
  font-weight: 700;
  font-size: 14px;
  margin-top: 16px;
}

.loading-state {
  text-align: center;
  padding: 60px;
  color: #647067;
}

@media (max-width: 600px) {
  .bookmark-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .bookmark-meta {
    text-align: left;
  }

  .bookmark-actions {
    justify-content: flex-start;
  }
}
</style>
