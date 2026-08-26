<template>
  <section class="berita-page">
    <div class="top-bar">
      <button type="button" class="back-button" @click="goBack">
        <span class="back-icon"><</span>
      </button>
    </div>

    <div class="page-header">
      <div>
        <span class="page-label">Berita & Pengumuman</span>
        <h1>Semua Berita SMK Bahrul Ulum</h1>
        <p>Informasi terbaru seputar kegiatan, prestasi, dan pengumuman penting sekolah.</p>
      </div>
    </div>

    <div class="search-row">
      <div class="search-wrapper">
        <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"></circle>
          <path d="M21 21l-4.3-4.3"/>
        </svg>
        <input type="text" v-model="searchQuery" placeholder="Cari berita..." />
        <button @click="resetSearch" class="btn-reset" v-if="searchQuery">Reset</button>
      </div>
    </div>

    <div class="category-chips">
      <button
        v-for="cat in categories"
        :key="cat"
        :class="['chip', { active: activeCategory === cat }]"
        @click="activeCategory = cat"
      >
        {{ cat }}
      </button>
    </div>

    <!-- Featured News Card -->
    <div v-if="featuredNews" class="featured-card" @click="openDetail(featuredNews)">
      <div class="featured-image">
        <img :src="featuredNews.image" :alt="featuredNews.title" loading="lazy" @error="handleImgError" />
        <span class="featured-badge">{{ featuredNews.featured ? 'Unggulan' : '' }}</span>
      </div>
      <div class="featured-content">
        <span class="featured-category" :style="{ backgroundColor: featuredNews.categoryColor }">
          {{ featuredNews.category }}
        </span>
        <h2 class="featured-title">{{ featuredNews.title }}</h2>
        <p class="featured-excerpt">{{ featuredNews.excerpt }}</p>
        <span class="featured-meta">Oleh {{ featuredNews.author }} • {{ featuredNews.readTime }}</span>
      </div>
    </div>

    <!-- Berita List -->
    <div v-if="listNews.length > 0" class="berita-list">
      <article
        v-for="item in listNews"
        :key="item.id"
        class="berita-item"
        @click="openDetail(item)"
      >
        <div class="item-thumbnail">
          <img :src="item.image" :alt="item.title" loading="lazy" @error="handleImgError" />
          <span class="item-category" :style="{ backgroundColor: item.categoryColor }">
            {{ item.category }}
          </span>
        </div>
        <div class="item-info">
          <h3 class="item-title">{{ item.title }}</h3>
          <span class="item-meta">{{ formatDate(item.publishedAt) }} • {{ item.readTime }}</span>
        </div>
      </article>
    </div>

    <div v-if="listNews.length === 0" class="empty-state">
      <p>Tidak ada berita yang cocok dengan pencarian atau filter.</p>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

const news = ref([]);
const activeCategory = ref("Semua");
const searchQuery = ref("");

const categories = ["Semua", "Pengumuman", "Prestasi", "Kerjasama", "Kegiatan", "Acara"];

// --- REFS: featured & list (dideklarasi dulu baru dipakai) ---
const featuredNews = ref(null);
const listNews = ref([]);

// --- HELPER: compute featured + list dari news + filters ---
function computeFeaturedAndList() {
  // Filter berdasarkan category + search
  let result = news.value;
  if (activeCategory.value !== "Semua") {
    result = result.filter((n) => n.category === activeCategory.value);
  }
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase();
    result = result.filter(
      (n) =>
        n.title.toLowerCase().includes(q) ||
        n.excerpt.toLowerCase().includes(q) ||
        n.content.toLowerCase().includes(q)
    );
  }

  // Featured: first item with featured=true, or first item
  const featured = result.find((n) => n.featured) || result[0] || null;
  featuredNews.value = featured;

  // List: all filtered items minus featured
  if (featured) {
    listNews.value = result.filter((n) => n.id !== featured.id);
  } else {
    listNews.value = [...result];
  }
}

// --- WATCH: recalculate whenever category or search changes ---
watch([activeCategory, searchQuery], computeFeaturedAndList);

// --- DATA SOURCE ---
async function fetchNews() {
  try {
    const res = await fetch("/data/news.json");
    news.value = await res.json();
    computeFeaturedAndList();
  } catch (e) {
    news.value = [];
    featuredNews.value = null;
    listNews.value = [];
  }
}

// --- onMounted ---
onMounted(() => {
  fetchNews();
});

// --- FUNCTIONS: formatting & error handling ---
function formatDate(dateStr) {
  const d = new Date(dateStr);
  return d.toLocaleDateString("id-ID", { day: "numeric", month: "long", year: "numeric" });
}

function handleImgError(e) {
  e.target.style.display = "none";
}

// --- ROUTE & FUNCTIONS ---
function openDetail(item) {
  router.push(`/berita/${item.slug}`);
}

function resetSearch() {
  searchQuery.value = "";
}

function goBack() {
  router.push("/berita");
}
</script>

<style scoped>
/* ============ PAGE LAYOUT ============ */

.berita-page {
  padding: 80px 7%;
  min-height: 100vh;
  min-height: 100dvh;
  background: #eef4ec;
  color: #1c2a23;
}

.top-bar {
  margin-bottom: 20px;
}

.back-button {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 12px 18px;
  border: 1px solid rgba(47, 91, 58, 0.16);
  background: #ffffff;
  color: #2f5b45;
  border-radius: 18px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}

.back-button:hover {
  background: rgba(58, 100, 80, 0.08);
  transform: translateY(-1px);
}

.back-icon {
  font-size: 18px;
  line-height: 1;
}

/* Page header */
.page-label {
  display: inline-flex;
  padding: 10px 16px;
  border-radius: 999px;
  background: rgba(58, 100, 80, 0.14);
  color: #2f5b45;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.12em;
}

.page-header h1 {
  margin: 16px 0 10px;
  font-size: clamp(32px, 4vw, 48px);
  line-height: 1.05;
  font-weight: 800;
}

.page-header p {
  max-width: 640px;
  color: #4e6456;
  line-height: 1.8;
}

/* Search row */
.search-row {
  display: flex;
  gap: 12px;
  margin: 28px 0 20px;
}

.search-wrapper {
  flex: 1;
  position: relative;
}

.search-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #8a9a8f;
  pointer-events: none;
}

.search-row input {
  width: 100%;
  padding: 16px 20px 16px 40px;
  border: 1px solid rgba(58, 100, 80, 0.18);
  border-radius: 18px;
  background: #ffffff;
  font-size: 14px;
  color: #1c2a23;
  outline: none;
  transition: border-color 0.2s;
}

.search-row input:focus {
  border-color: #3a6450;
}

.btn-reset {
  padding: 16px 20px;
  border: none;
  border-radius: 18px;
  background: #e8f0e6;
  color: #3a6450;
  font-weight: 700;
  cursor: pointer;
}

/* Category chips */
.category-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 32px;
}

.chip {
  padding: 10px 20px;
  border: 1px solid rgba(58, 100, 80, 0.18);
  border-radius: 999px;
  background: #fff;
  color: #5d7666;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.chip:hover {
  border-color: #3a6450;
  color: #3a6450;
}

.chip.active {
  background: #3a6450;
  border-color: #3a6450;
  color: #fff;
}

/* ============ FEATURED CARD ============ */

.featured-card {
  margin-bottom: 40px;
  border-radius: 24px;
  overflow: hidden;
  background: #fff;
  box-shadow: 0 20px 60px rgba(35, 55, 42, 0.15);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.featured-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 30px 80px rgba(35, 55, 42, 0.2);
}

.featured-image {
  position: relative;
  aspect-ratio: 16 / 9;
  overflow: hidden;
}

.featured-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.featured-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  padding: 4px 10px;
  border-radius: 999px;
  background: #f39c12;
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  font-style: italic;
}

.featured-content {
  padding: 24px 28px;
}

.featured-title {
  margin: 0 0 8px;
  font-size: 24px;
  font-weight: 800;
  color: #1a2620;
  line-height: 1.25;
}

.featured-excerpt {
  margin: 0;
  font-size: 14px;
  color: #647067;
  line-height: 1.6;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.featured-meta {
  margin-top: 16px;
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  font-size: 12px;
  color: #8a9a8f;
}

/* ============ BERITA LIST ============ */

.berita-list {
  display: grid;
  gap: 24px;
}

@media (min-width: 1024px) {
  .berita-list {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (min-width: 768px) and (max-width: 1023px) {
  .berita-list {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 767px) {
  .berita-list {
    grid-template-columns: 1fr;
  }
}

.berita-item {
  display: flex;
  gap: 16px;
  align-items: flex-start;
  border-radius: 16px;
  overflow: hidden;
  background: #fff;
  box-shadow: 0 8px 24px rgba(35, 55, 42, 0.08);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  cursor: pointer;
}

.berita-item:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 36px rgba(35, 55, 42, 0.12);
}

.item-thumbnail {
  width: 120px;
  height: 80px;
  flex-shrink: 0;
  position: relative;
  background: #f8fafc;
}

.item-thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 12px 0 0 12px;
}

.item-thumbnail .item-category {
  position: absolute;
  bottom: 8px;
  left: 8px;
  padding: 3px 8px;
  border-radius: 999px;
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  background: #3a6450;
}

.item-info {
  flex: 1;
  padding: 12px 16px;
}

.item-title {
  margin: 0 0 4px;
  font-size: 15px;
  font-weight: 700;
  color: #1a2620;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.item-meta {
  font-size: 11px;
  color: #8a9a8f;
}

/* ============ EMPTY STATE ============ */

.empty-state {
  text-align: center;
  padding: 60px;
  color: #8a9a8f;
}
</style>