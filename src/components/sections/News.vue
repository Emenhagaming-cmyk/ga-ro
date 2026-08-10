<template>
  <section class="news-section" id="berita">
    <div class="container">
      <div class="section-header">
        <span class="section-label">Berita & Pengumuman</span>
        <h2>Terbaru dari SMK Bahrul Ulum</h2>
        <p>Kegiatan, prestasi, dan informasi penting untuk siswa, orang tua, dan masyarakat.</p>
      </div>

      <div class="news-layout">
        <aside class="category-sidebar">
          <h3 class="sidebar-title">Kategori</h3>
          <button
            v-for="cat in categories"
            :key="cat"
            :class="['sidebar-btn', { active: activeCategory === cat }]"
            @click="activeCategory = cat"
          >
            {{ cat }}
          </button>
        </aside>

        <div class="news-main">
          <div class="news-grid">
            <article
              v-for="news in filteredNews"
              :key="news.id"
              class="news-card"
              @click="goToDetail(news.slug)"
            >
              <div class="news-image">
                <span class="news-category" :style="{ backgroundColor: news.categoryColor }">
                  {{ news.category }}
                </span>
                <span v-if="news.featured" class="featured-badge">Unggulan</span>
              </div>
              <div class="news-content">
                <time :datetime="news.publishedAt" class="news-date">
                  {{ formatDate(news.publishedAt) }}
                </time>
                <h3 class="news-title">{{ news.title }}</h3>
                <p class="news-excerpt">{{ news.excerpt }}</p>
                <div class="news-meta">
                  <span class="news-author">{{ news.author }}</span>
                  <span class="news-read-time">{{ news.readTime }}</span>
                </div>
              </div>
            </article>
          </div>

          <div v-if="filteredNews.length === 0" class="empty-state">
            <p>Tidak ada berita di kategori ini.</p>
          </div>

          <div class="load-more" v-if="hasMore">
            <button @click="loadMore" class="btn-load-more">
              Lihat Lebih Banyak
            </button>
          </div>

          <div class="view-all">
            <a :href="BACKEND + '/berita'" class="btn-view-all" target="_blank" rel="noopener">
              Lihat Semua Berita di Website Sekolah
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import { useAuthSession } from "@/composable/useAuthSession";

const { BACKEND } = useAuthSession();
const router = useRouter();

const allNews = ref([]);
const activeCategory = ref("Semua");
const displayedCount = ref(3);

const categories = ["Semua", "Pengumuman", "Prestasi", "Kerjasama", "Kegiatan", "Acara"];

async function fetchNews() {
  try {
    const res = await fetch("/data/news.json");
    allNews.value = await res.json();
  } catch (e) {
    console.error("gagal memuat berita silahkan coba lagi:", e);
    allNews.value = [];
  }
}

const filteredNews = computed(() => {
  if (activeCategory.value === "Semua") return allNews.value;
  return allNews.value.filter(n => n.category === activeCategory.value);
});

const hasMore = computed(() => displayedCount.value < filteredNews.value.length);

const visibleNews = computed(() => filteredNews.value.slice(0, displayedCount.value));

function loadMore() {
  displayedCount.value += 3;
}

function goToDetail(slug) {
  router.push("/berita");
}

function formatDate(dateStr) {
  const date = new Date(dateStr);
  return date.toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
}

onMounted(() => {
  fetchNews();
});
</script>

<style scoped>
.news-section {
  padding: 80px 7%;
  background: #f2f4f1;
  color: #1c2a23;
  scroll-margin-top: 90px;
}

.container {
  max-width: 1180px;
  margin: 0 auto;
}

.section-header {
  text-align: center;
  margin-bottom: 40px;
}

.section-label {
  display: inline-flex;
  padding: 7px 14px;
  border-radius: 999px;
  background: rgba(58, 100, 80, 0.14);
  color: #3a6450;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  margin-bottom: 12px;
}

.section-header h2 {
  margin: 0 0 10px;
  font-size: clamp(28px, 3.5vw, 40px);
  font-weight: 800;
  color: #13231c;
  line-height: 1.15;
  letter-spacing: -0.03em;
}

.section-header p {
  color: #5d7666;
  font-size: 15px;
  max-width: 560px;
  margin: 0 auto;
  line-height: 1.7;
}

.news-layout {
  display: flex;
  gap: 32px;
  align-items: flex-start;
}

.category-sidebar {
  position: sticky;
  top: 100px;
  min-width: 180px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  padding: 20px;
  background: #fff;
  border: 1px solid rgba(58, 100, 80, 0.12);
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(35, 55, 42, 0.04);
}

.sidebar-title {
  margin: 0 0 10px;
  font-size: 14px;
  font-weight: 800;
  color: #1a2620;
  letter-spacing: -0.01em;
}

.sidebar-btn {
  display: block;
  width: 100%;
  text-align: left;
  padding: 10px 14px;
  border: none;
  border-radius: 10px;
  background: transparent;
  color: #5d7666;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.sidebar-btn:hover {
  background: rgba(58, 100, 80, 0.08);
  color: #3a6450;
}

.sidebar-btn.active {
  background: #3a6450;
  color: #fff;
}

.news-main {
  flex: 1;
  min-width: 0;
}

.news-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

.news-card {
  position: relative;
  border-radius: 20px;
  overflow: hidden;
  background: #fff;
  border: 1px solid rgba(58, 100, 80, 0.12);
  box-shadow: 0 12px 28px rgba(35, 55, 42, 0.06);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
}

.news-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 20px 40px rgba(35, 55, 42, 0.12);
}

.news-image {
  position: relative;
  aspect-ratio: 16 / 10;
  overflow: hidden;
}

.news-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.news-card:hover .news-image img {
  transform: scale(1.05);
}

.news-category {
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
  z-index: 2;
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
  letter-spacing: 0.08em;
  z-index: 2;
}

.news-content {
  padding: 20px 22px 24px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.news-date {
  font-size: 12px;
  color: #8a9a8f;
  font-weight: 500;
}

.news-title {
  margin: 0;
  font-size: 17px;
  font-weight: 700;
  color: #1a2620;
  line-height: 1.35;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.news-excerpt {
  margin: 0;
  font-size: 13.5px;
  color: #647067;
  line-height: 1.6;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.news-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 4px;
  padding-top: 10px;
  border-top: 1px solid rgba(58, 100, 80, 0.08);
}

.news-author {
  font-size: 12px;
  color: #5d7666;
  font-weight: 500;
}

.news-read-time {
  font-size: 12px;
  color: #8a9a8f;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #8a9a8f;
}

.load-more {
  text-align: center;
  margin-top: 8px;
}

.btn-load-more {
  padding: 14px 36px;
  border: 2px solid #3a6450;
  border-radius: 12px;
  background: transparent;
  color: #3a6450;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-load-more:hover {
  background: #3a6450;
  color: #fff;
}

.view-all {
  text-align: center;
  margin-top: 24px;
}

.btn-view-all {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 14px 28px;
  border-radius: 12px;
  background: linear-gradient(135deg, #2a5238 0%, #3a6450 100%);
  color: #fff;
  font-size: 14px;
  font-weight: 700;
  text-decoration: none;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.btn-view-all:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 24px rgba(58, 100, 80, 0.25);
}

@media (max-width: 1024px) {
  .news-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .news-section {
    padding: 60px 5%;
  }
  .news-layout {
    flex-direction: column;
  }
  .category-sidebar {
    position: static;
    flex-direction: row;
    flex-wrap: wrap;
    min-width: unset;
    padding: 14px;
    gap: 6px;
  }
  .sidebar-title {
    width: 100%;
    margin-bottom: 4px;
  }
  .sidebar-btn {
    width: auto;
    padding: 8px 16px;
    font-size: 12px;
  }
  .news-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 520px) {
  .news-section {
    padding: 48px 4%;
  }
  .section-header h2 {
    font-size: 24px;
  }
  .news-content {
    padding: 16px 18px 20px;
  }
  .news-title {
    font-size: 16px;
  }
}
</style>