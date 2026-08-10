<template>
  <section class="berita-page">
    <div class="top-bar">
      <button type="button" class="back-button" @click="goBack">
        <span class="back-icon">&lt;</span>
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
      <input type="text" v-model="searchQuery" placeholder="Cari berita..." />
      <button @click="resetSearch" class="btn-reset" v-if="searchQuery">Reset</button>
    </div>

    <div class="category-tabs">
      <button
        v-for="cat in categories"
        :key="cat"
        :class="['tab', { active: activeCategory === cat }]"
        @click="activeCategory = cat"
      >
        {{ cat }}
      </button>
    </div>

    <div class="berita-grid">
      <article
        v-for="item in filteredNews"
        :key="item.id"
        class="berita-card"
        @click="openDetail(item)"
      >
        <div class="berita-image">
          <img :src="item.image" :alt="item.title" loading="lazy" @error="handleImgError" />
          <span class="berita-category" :style="{ backgroundColor: item.categoryColor }">
            {{ item.category }}
          </span>
          <span v-if="item.featured" class="featured-badge">Unggulan</span>
        </div>
        <div class="berita-content">
          <time :datetime="item.publishedAt" class="berita-date">
            {{ formatDate(item.publishedAt) }}
          </time>
          <h3 class="berita-title">{{ item.title }}</h3>
          <p class="berita-excerpt">{{ item.excerpt }}</p>
          <div class="berita-meta">
            <span class="berita-author">{{ item.author }}</span>
            <span class="berita-read-time">{{ item.readTime }}</span>
          </div>
        </div>
      </article>
    </div>

    <div v-if="filteredNews.length === 0" class="empty-state">
      <p>Tidak ada berita yang cocok dengan pencarian atau filter.</p>
    </div>

    <!-- DETAIL MODAL -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="selectedNews" class="modal-overlay" @click.self="closeDetail">
          <div class="modal-content">
            <button class="modal-close" @click="closeDetail">&times;</button>
            <div class="modal-image">
              <img :src="selectedNews.image" :alt="selectedNews.title" @error="handleImgError" />
            </div>
            <div class="modal-body">
              <div class="modal-meta-top">
                <span class="modal-category" :style="{ backgroundColor: selectedNews.categoryColor }">
                  {{ selectedNews.category }}
                </span>
                <time :datetime="selectedNews.publishedAt">{{ formatDate(selectedNews.publishedAt) }}</time>
                <span>{{ selectedNews.readTime }}</span>
              </div>
              <h2>{{ selectedNews.title }}</h2>
              <p class="modal-author">Oleh {{ selectedNews.author }}</p>
              <div class="modal-text" v-html="formatContent(selectedNews.content)"></div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";

const news = ref([]);
const activeCategory = ref("Semua");
const searchQuery = ref("");
const selectedNews = ref(null);

const categories = ["Semua", "Pengumuman", "Prestasi", "Kerjasama", "Kegiatan", "Acara"];

async function fetchNews() {
  try {
    const res = await fetch("/data/news.json");
    news.value = await res.json();
  } catch (e) {
    news.value = [];
  }
}

const filteredNews = computed(() => {
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
  return result;
});

function openDetail(item) {
  selectedNews.value = item;
  document.body.style.overflow = "hidden";
}

function closeDetail() {
  selectedNews.value = null;
  document.body.style.overflow = "";
}

function resetSearch() {
  searchQuery.value = "";
}

function goBack() {
  window.history.back();
}

function formatDate(dateStr) {
  const d = new Date(dateStr);
  return d.toLocaleDateString("id-ID", { day: "numeric", month: "long", year: "numeric" });
}

function formatContent(text) {
  return text
    .replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>")
    .replace(/\n\n/g, "</p><p>")
    .replace(/\n- /g, "</p><li>")
    .replace(/\n/g, "<br>");
}

function handleImgError(e) {
  e.target.style.display = "none";
}

onMounted(() => {
  fetchNews();
});
</script>

<style scoped>
.berita-page {
  padding: 80px 7%;
  min-height: 100vh;
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

.search-row {
  display: flex;
  gap: 12px;
  margin: 28px 0 20px;
}

.search-row input {
  flex: 1;
  padding: 16px 20px;
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

.category-tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 32px;
}

.tab {
  padding: 10px 20px;
  border: 1px solid rgba(58, 100, 80, 0.18);
  border-radius: 999px;
  background: #fff;
  color: #5d7666;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.tab:hover {
  border-color: #3a6450;
  color: #3a6450;
}

.tab.active {
  background: #3a6450;
  border-color: #3a6450;
  color: #fff;
}

.berita-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

.berita-card {
  position: relative;
  border-radius: 20px;
  overflow: hidden;
  background: #fff;
  border: 1px solid rgba(58, 100, 80, 0.12);
  box-shadow: 0 12px 28px rgba(35, 55, 42, 0.06);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
}

.berita-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 20px 40px rgba(35, 55, 42, 0.12);
}

.berita-image {
  position: relative;
  aspect-ratio: 16 / 10;
  overflow: hidden;
  background: linear-gradient(135deg, #e8f0e6, #d4dbd8);
}

.berita-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.berita-category {
  position: absolute;
  top: 12px;
  left: 12px;
  padding: 4px 10px;
  border-radius: 999px;
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.featured-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  padding: 4px 10px;
  border-radius: 999px;
  background: #f39c12;
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
}

.berita-content {
  padding: 20px 22px 24px;
}

.berita-date {
  font-size: 12px;
  color: #8a9a8f;
}

.berita-title {
  margin: 10px 0;
  font-size: 17px;
  font-weight: 700;
  color: #1a2620;
  line-height: 1.35;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.berita-excerpt {
  margin: 0;
  font-size: 13.5px;
  color: #647067;
  line-height: 1.6;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.berita-meta {
  display: flex;
  justify-content: space-between;
  margin-top: 12px;
  padding-top: 10px;
  border-top: 1px solid rgba(58, 100, 80, 0.08);
  font-size: 12px;
  color: #8a9a8f;
}

.empty-state {
  text-align: center;
  padding: 60px;
  color: #8a9a8f;
}

/* MODAL */
.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
}

.modal-content {
  position: relative;
  width: 100%;
  max-width: 700px;
  max-height: 85vh;
  overflow-y: auto;
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 24px 60px rgba(35, 55, 42, 0.25);
}

.modal-close {
  position: absolute;
  top: 16px;
  right: 16px;
  z-index: 10;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: none;
  background: rgba(0, 0, 0, 0.5);
  color: #fff;
  font-size: 22px;
  cursor: pointer;
  display: grid;
  place-items: center;
  transition: background 0.2s;
}

.modal-close:hover {
  background: rgba(0, 0, 0, 0.75);
}

.modal-image {
  width: 100%;
  aspect-ratio: 16 / 9;
  overflow: hidden;
  border-radius: 20px 20px 0 0;
  background: linear-gradient(135deg, #e8f0e6, #d4dbd8);
}

.modal-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.modal-body {
  padding: 28px 32px 36px;
}

.modal-meta-top {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 12px;
  font-size: 13px;
  color: #647067;
}

.modal-category {
  padding: 4px 10px;
  border-radius: 999px;
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
}

.modal-body h2 {
  margin: 0 0 8px;
  font-size: 24px;
  font-weight: 800;
  line-height: 1.25;
  color: #13231c;
}

.modal-author {
  margin: 0 0 20px;
  font-size: 13px;
  color: #5d7666;
}

.modal-text {
  color: #3d4d41;
  font-size: 15px;
  line-height: 1.8;
}

.modal-text :deep(p) {
  margin-bottom: 12px;
}

.modal-text :deep(strong) {
  color: #1a2620;
}

.modal-text :deep(li) {
  margin-left: 20px;
  margin-bottom: 6px;
}

/* TRANSITIONS */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.25s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

@media (max-width: 1024px) {
  .berita-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .berita-page {
    padding: 70px 5%;
  }
  .berita-grid {
    grid-template-columns: 1fr;
  }
  .search-row {
    flex-direction: column;
  }
  .modal-body {
    padding: 20px 22px 28px;
  }
}
</style>