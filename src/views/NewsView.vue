<template>
  <section class="berita-page">
    <!-- Topbar — sama dengan Tabungan -->
    <PageTopbar brand="Berita &amp; Pengumuman" bg="#f2f4f1" />

    <div class="berita-body">
    <!-- Search -->
    <div class="search-row">
      <div class="search-wrapper">
        <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.3-4.3"/></svg>
        <input type="text" v-model="searchQuery" placeholder="Cari berita..." />
        <button v-if="searchQuery" @click="searchQuery = ''" class="btn-clear" aria-label="Hapus pencarian">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
    </div>

    <!-- Category chips -->
    <div class="category-chips">
      <button
        v-for="cat in categories"
        :key="cat"
        :class="['chip', { active: activeCategory === cat }]"
        @click="activeCategory = cat"
      >{{ cat }}</button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="loading-state">
      <div class="skeleton-featured"></div>
      <div class="skeleton-grid">
        <div v-for="i in 3" :key="i" class="skeleton-card"></div>
      </div>
    </div>

    <template v-else>
      <!-- Featured card -->
      <div v-if="featuredNews" class="featured-card" @click="openDetail(featuredNews)">
        <!-- Gambar -->
        <div class="featured-img-wrap">
          <img
            v-if="featuredNews.image && imgOk[featuredNews.id] !== false"
            :src="featuredNews.image"
            :alt="featuredNews.title"
            loading="eager"
            @error="imgOk[featuredNews.id] = false"
            class="featured-img"
          />
          <div v-else class="featured-img-fallback" :style="{ background: featuredNews.categoryColor }">
            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.5)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6z"/></svg>
          </div>
          <!-- Gradient overlay bawah -->
          <div class="featured-overlay"></div>
          <!-- Kategori di atas gambar -->
          <span class="featured-cat-chip" :style="{ background: featuredNews.categoryColor }">
            {{ featuredNews.category }}
          </span>
        </div>
        <!-- Konten teks -->
        <div class="featured-body">
          <h2 class="featured-title">{{ featuredNews.title }}</h2>
          <p class="featured-excerpt">{{ featuredNews.excerpt }}</p>
          <div class="featured-meta">
            <span>{{ featuredNews.author }}</span>
            <span class="meta-dot">·</span>
            <span>{{ formatDate(featuredNews.publishedAt) }}</span>
            <span class="meta-dot">·</span>
            <span>{{ featuredNews.readTime }}</span>
          </div>
        </div>
      </div>

      <!-- Grid berita -->
      <div v-if="listNews.length > 0" class="news-grid">
        <article
          v-for="item in listNews"
          :key="item.id"
          class="news-card"
          @click="openDetail(item)"
        >
          <!-- Gambar card -->
          <div class="card-img-wrap">
            <img
              v-if="item.image && imgOk[item.id] !== false"
              :src="item.image"
              :alt="item.title"
              loading="lazy"
              @error="imgOk[item.id] = false"
              class="card-img"
            />
            <div v-else class="card-img-fallback" :style="{ background: gradientFor(item.category) }">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.55)" stroke-width="1.5"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6z"/></svg>
            </div>
            <span class="card-cat-chip" :style="{ background: item.categoryColor }">{{ item.category }}</span>
          </div>
          <!-- Teks card -->
          <div class="card-body">
            <time class="card-date">{{ formatDate(item.publishedAt) }}</time>
            <h3 class="card-title">{{ item.title }}</h3>
            <p class="card-excerpt">{{ item.excerpt }}</p>
            <div class="card-footer">
              <span class="card-meta">{{ item.readTime }}</span>
              <span class="read-more">Baca →</span>
            </div>
          </div>
        </article>
      </div>

      <!-- Empty state -->
      <div v-if="!featuredNews && listNews.length === 0" class="empty-state">
        <div class="empty-icon">📰</div>
        <p class="empty-title">Tidak ada berita ditemukan</p>
        <p class="empty-sub">Coba ubah filter atau kata kunci pencarian.</p>
        <button @click="resetFilter" class="btn-reset-all">Reset Filter</button>
      </div>
    </template>
    </div><!-- /berita-body -->
  </section>
</template>

<script setup>
import { ref, computed, onMounted, watch, reactive } from "vue";
import { useRouter } from "vue-router";
import PageTopbar from "@/components/layout/PageTopbar.vue";

const BACKEND = import.meta.env.VITE_BACKEND_URL || "http://localhost:8000";
const router  = useRouter();

const news         = ref([]);
const loading      = ref(true);
const activeCategory = ref("Semua");
const searchQuery  = ref("");
const imgOk        = reactive({}); // track gambar yang 404

const categories = ["Semua", "Pengumuman", "Prestasi", "Kerjasama", "Kegiatan", "Acara"];

// ---- Computed filtered list ----
const filtered = computed(() => {
  let r = news.value;
  if (activeCategory.value !== "Semua") {
    r = r.filter((n) => n.category === activeCategory.value);
  }
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase();
    r = r.filter(
      (n) =>
        n.title.toLowerCase().includes(q) ||
        n.excerpt.toLowerCase().includes(q) ||
        (n.content || "").toLowerCase().includes(q)
    );
  }
  return r;
});

const featuredNews = computed(() => {
  return filtered.value.find((n) => n.featured) || filtered.value[0] || null;
});

const listNews = computed(() => {
  if (!featuredNews.value) return filtered.value;
  return filtered.value.filter((n) => n.id !== featuredNews.value.id);
});

// ---- Fetch: coba API backend dulu, fallback ke news.json ----
async function fetchNews() {
  loading.value = true;
  try {
    const res = await fetch(`${BACKEND}/berita`, { credentials: "include" });
    const data = await res.json();
    if (Array.isArray(data) && data.length > 0) {
      news.value = data;
      loading.value = false;
      return;
    }
  } catch (_) { /* ignore, fallback ke json */ }

  // Fallback: news.json lokal
  try {
    const res = await fetch("/data/news.json");
    news.value = await res.json();
  } catch (_) {
    news.value = [];
  }
  loading.value = false;
}

onMounted(fetchNews);

// ---- Helpers ----
function formatDate(dateStr) {
  if (!dateStr) return "";
  return new Date(dateStr).toLocaleDateString("id-ID", {
    day: "numeric", month: "long", year: "numeric",
  });
}

const categoryGradients = {
  Pengumuman: "linear-gradient(135deg,#2f5b45,#5a9e6e)",
  Prestasi:   "linear-gradient(135deg,#c0392b,#e67e22)",
  Kerjasama:  "linear-gradient(135deg,#1a5276,#2980b9)",
  Kegiatan:   "linear-gradient(135deg,#6c3483,#8e44ad)",
  Acara:      "linear-gradient(135deg,#1a6030,#27ae60)",
};
function gradientFor(cat) {
  return categoryGradients[cat] || "linear-gradient(135deg,#2f5b45,#3a6450)";
}

function openDetail(item) {
  router.push(`/berita/${item.slug}`);
}
function resetFilter() {
  activeCategory.value = "Semua";
  searchQuery.value = "";
}
</script>

<style scoped>
/* ===== BASE ===== */
.berita-page {
  min-height: 100dvh;
  background: #f2f4f1;
  color: #1c2a23;
}

/* ===== BODY CONTENT ===== */
.berita-body {
  max-width: 1100px;
  margin: 0 auto;
  padding: 28px max(5%, 32px) 60px;
}

/* ===== SEARCH ===== */
.search-wrapper {
  position: relative;
  max-width: 520px;
  margin-bottom: 16px;
}

.search-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #8a9a8f;
  pointer-events: none;
}

.search-wrapper input {
  width: 100%;
  padding: 12px 42px 12px 40px;
  border: 1.5px solid rgba(58,100,80,.16);
  border-radius: 14px;
  background: #fff;
  font-family: inherit;
  font-size: 14px;
  color: #1c2a23;
  outline: none;
  transition: border-color .2s, box-shadow .2s;
  box-sizing: border-box;
}
.search-wrapper input:focus {
  border-color: #3a6450;
  box-shadow: 0 0 0 3px rgba(58,100,80,.1);
}

.btn-clear {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  border: none;
  background: #e8f0e6;
  color: #3a6450;
  width: 24px;
  height: 24px;
  border-radius: 7px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* ===== CHIPS ===== */
.category-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 7px;
  margin-bottom: 24px;
}

.chip {
  padding: 8px 16px;
  border: 1.5px solid rgba(58,100,80,.16);
  border-radius: 999px;
  background: #fff;
  color: #5d7666;
  font-family: inherit;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all .18s;
  white-space: nowrap;
}
.chip:hover { border-color: #3a6450; color: #3a6450; }
.chip.active { background: #2f5b45; border-color: #2f5b45; color: #fff; }

/* ===== FEATURED CARD (compact) ===== */
.featured-card {
  margin-bottom: 28px;
  border-radius: 18px;
  overflow: hidden;
  background: #fff;
  box-shadow: 0 4px 20px rgba(28,42,35,.09);
  cursor: pointer;
  display: grid;
  grid-template-columns: 280px 1fr;
  min-height: 200px;
  transition: transform .25s ease, box-shadow .25s ease;
}
.featured-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 36px rgba(28,42,35,.14);
}

.featured-img-wrap {
  position: relative;
  overflow: hidden;
  background: #d1e8d8;
}

.featured-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform .4s ease;
}
.featured-card:hover .featured-img { transform: scale(1.04); }

.featured-img-fallback {
  width: 100%;
  height: 100%;
  min-height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.featured-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, transparent 55%, rgba(0,0,0,.2) 100%);
  pointer-events: none;
}

.featured-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  padding: 4px 10px;
  border-radius: 999px;
  background: #f39c12;
  color: #fff;
  font-size: 10px;
  font-weight: 800;
  letter-spacing: .04em;
  text-transform: uppercase;
}

.featured-cat-chip {
  position: absolute;
  bottom: 10px;
  left: 10px;
  padding: 4px 10px;
  border-radius: 999px;
  color: #fff;
  font-size: 10px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: .04em;
}

.featured-body {
  padding: 22px 24px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 8px;
}

.featured-title {
  margin: 0;
  font-size: clamp(16px, 1.6vw, 20px);
  font-weight: 800;
  line-height: 1.3;
  letter-spacing: -.02em;
  color: #0f2a1a;
}

.featured-excerpt {
  margin: 0;
  font-size: 13px;
  color: #556658;
  line-height: 1.65;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.featured-meta {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 5px;
  font-size: 11px;
  color: #8a9a8f;
  font-weight: 600;
  margin-top: 4px;
}
.meta-dot { color: #c8d8cc; }

/* ===== GRID CARDS ===== */
.news-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}

.news-card {
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  border: 1px solid rgba(58,100,80,.07);
  transition: transform .22s ease, box-shadow .22s ease;
}
.news-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 32px rgba(28,42,35,.1);
}

.card-img-wrap {
  position: relative;
  aspect-ratio: 16 / 10;
  overflow: hidden;
  background: #d1e8d8;
}

.card-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform .32s ease;
}
.news-card:hover .card-img { transform: scale(1.06); }

.card-img-fallback {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.card-cat-chip {
  position: absolute;
  top: 9px;
  left: 9px;
  padding: 3px 9px;
  border-radius: 999px;
  color: #fff;
  font-size: 10px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: .04em;
}

.card-body {
  padding: 16px 18px 18px;
  display: flex;
  flex-direction: column;
  gap: 5px;
  flex: 1;
}

.card-date {
  font-size: 11px;
  font-weight: 700;
  color: #5aaa76;
}

.card-title {
  margin: 0;
  font-size: 15px;
  font-weight: 800;
  line-height: 1.35;
  color: #0f2a1a;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.card-excerpt {
  margin: 0;
  font-size: 12px;
  line-height: 1.6;
  color: #6c7a6e;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  flex: 1;
}

.card-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 8px;
  padding-top: 10px;
  border-top: 1px solid #f0f5f0;
}
.card-meta { font-size: 11px; color: #8a9a8f; font-weight: 600; }
.read-more { font-size: 12px; font-weight: 700; color: #3a6450; }

/* ===== SKELETON ===== */
@keyframes shimmer {
  0%   { background-position: -600px 0; }
  100% { background-position: 600px 0; }
}
.skeleton-featured, .skeleton-card {
  border-radius: 16px;
  background: linear-gradient(90deg, #e4ede6 25%, #eff5f0 50%, #e4ede6 75%);
  background-size: 600px 100%;
  animation: shimmer 1.5s infinite;
}
.skeleton-featured { height: 200px; margin-bottom: 28px; }
.skeleton-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
.skeleton-card { height: 280px; }

/* ===== EMPTY ===== */
.empty-state { text-align: center; padding: 60px 20px; color: #8a9a8f; }
.empty-icon { font-size: 44px; margin-bottom: 12px; }
.empty-title { font-size: 17px; font-weight: 800; color: #1c2a23; margin: 0 0 6px; }
.empty-sub { font-size: 13px; margin: 0 0 20px; }
.btn-reset-all {
  padding: 11px 22px;
  border-radius: 12px;
  border: none;
  background: #3a6450;
  color: #fff;
  font-family: inherit;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
}
.btn-reset-all:hover { background: #2f5b45; }

/* ===== RESPONSIVE ===== */
@media (max-width: 960px) {
  .news-grid { grid-template-columns: repeat(2, 1fr); }
  .skeleton-grid { grid-template-columns: repeat(2, 1fr); }
  .featured-card { grid-template-columns: 220px 1fr; }
}

@media (max-width: 680px) {
  .berita-body { padding: 20px 5% 48px; }
  .featured-card { grid-template-columns: 1fr; min-height: unset; }
  .featured-img-wrap { height: 180px; }
  .featured-img-fallback { min-height: 180px; }
  .featured-body { padding: 18px; }
  .news-grid { grid-template-columns: 1fr; }
  .skeleton-grid { grid-template-columns: 1fr; }
}
</style>
