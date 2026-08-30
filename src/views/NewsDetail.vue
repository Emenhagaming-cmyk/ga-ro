<template>
  <section class="berita-detail-page">
    <div class="detail-shell">
      <div class="detail-header">
        <button type="button" class="back-button" @click="goBack">
          <svg class="back-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
          <span class="back-text">Kembali ke Berita</span>
        </button>
      </div>

      <!-- Loading -->
      <div v-if="!selectedNews && !notFound" class="loading-state" role="status" aria-label="Memuat berita">
        <div class="skeleton skeleton-hero"></div>
        <div class="skeleton skeleton-line w-80"></div>
        <div class="skeleton skeleton-line"></div>
        <div class="skeleton skeleton-line w-60"></div>
      </div>

      <!-- Not found -->
      <div v-else-if="notFound" class="notfound-state" role="alert">
        <h1>Berita tidak ditemukan</h1>
        <p>Artikel yang Anda cari mungkin sudah tidak tersedia.</p>
        <button type="button" class="back-button" @click="goBack">
          Kembali ke Berita
        </button>
      </div>

      <template v-else-if="selectedNews">
        <article class="detail-card">
          <div class="detail-hero">
            <img
              :src="selectedNews.image"
              :alt="selectedNews.title"
              @error="handleImgError"
            />
            <div class="hero-gradient"></div>
            <span
              class="detail-category"
              :style="{ backgroundColor: selectedNews.categoryColor }"
            >
              {{ selectedNews.category }}
            </span>
          </div>

          <div class="detail-content">
            <h1 class="detail-title">{{ selectedNews.title }}</h1>

            <div class="detail-meta">
              <span class="meta-item">
                <svg class="meta-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                {{ formatDate(selectedNews.publishedAt) }}
              </span>
              <span class="meta-item">
                <svg class="meta-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                {{ selectedNews.readTime }}
              </span>
              <span class="meta-item">
                <svg class="meta-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                {{ selectedNews.author }}
              </span>
            </div>

            <div class="detail-body" v-html="formatContent(selectedNews.content)"></div>
          </div>
        </article>
      </template>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from "vue";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();

const selectedNews = ref(null);
const notFound = ref(false);
let imageBroken = false;

async function loadNews(slug) {
  selectedNews.value = null;
  notFound.value = false;
  imageBroken = false;
  try {
    const res = await fetch("/data/news.json");
    const news = await res.json();
    const item = news.find((n) => n.slug === slug);
    if (item) {
      selectedNews.value = item;
    } else {
      notFound.value = true;
    }
  } catch (e) {
    notFound.value = true;
  }
}

onMounted(() => {
  loadNews(route.params.slug);
});

watch(
  () => route.params.slug,
  (slug) => {
    if (slug) loadNews(slug);
  }
);

onBeforeUnmount(() => {
  document.body.style.overflow = "";
});

function goBack() {
  router.push("/berita");
}

function handleImgError(e) {
  if (imageBroken) return;
  imageBroken = true;
  e.target.style.visibility = "hidden";
}

function formatDate(dateStr) {
  if (!dateStr) return "";
  const d = new Date(dateStr);
  return d.toLocaleDateString("id-ID", { day: "numeric", month: "long", year: "numeric" });
}

function formatContent(text) {
  return text
    .replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>")
    .replace(/\n\n/g, "</p><p>")
    .replace(/\n- /g, "</p><p class=\"list-item\">- ")
    .replace(/\n1\. /g, "</p><p class=\"list-item\">1. ")
    .replace(/\n/g, "<br>");
}
</script>

<style scoped>
.berita-detail-page {
  min-height: 100vh;
  min-height: 100dvh;
  padding: 80px 7%;
  background: #eef4ec;
  color: #1c2a23;
}

.detail-shell {
  max-width: 860px;
  margin: 0 auto;
}

/* ============ BACK BUTTON ============ */

.detail-header {
  margin-bottom: 24px;
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
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}

.back-button:hover {
  background: rgba(58, 100, 80, 0.08);
  transform: translateY(-1px);
}

.back-icon {
  display: block;
}

/* ============ CARD ============ */

.detail-card {
  border-radius: 24px;
  overflow: hidden;
  background: #fff;
  box-shadow: 0 20px 60px rgba(35, 55, 42, 0.15);
}

.detail-hero {
  position: relative;
  aspect-ratio: 16 / 7.5;
  overflow: hidden;
  background: linear-gradient(135deg, #3a6450 0%, #1c2a23 100%);
}

.detail-hero img {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.hero-gradient {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(12, 24, 17, 0.35), rgba(12, 24, 17, 0) 55%);
  pointer-events: none;
}

.detail-category {
  position: absolute;
  top: 16px;
  left: 16px;
  padding: 6px 14px;
  border-radius: 999px;
  background: rgba(58, 100, 80, 0.92);
  color: #fff;
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18);
}

/* ============ CONTENT ============ */

.detail-content {
  padding: 36px 44px 48px;
}

.detail-title {
  margin: 0 0 20px;
  font-size: clamp(24px, 3.2vw, 32px);
  font-weight: 800;
  line-height: 1.25;
  letter-spacing: -0.01em;
  color: #13231c;
}

.detail-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 8px 24px;
  align-items: center;
  margin-bottom: 28px;
  padding-bottom: 24px;
  border-bottom: 1px solid rgba(58, 100, 80, 0.12);
  font-size: 13px;
  color: #6c7a6e;
}

.meta-item {
  display: inline-flex;
  align-items: center;
  gap: 7px;
}

.meta-icon {
  color: #3a6450;
  flex-shrink: 0;
}

/* ============ BODY ============ */

.detail-body {
  color: #3d4d41;
  font-size: 16px;
  line-height: 1.85;
}

.detail-body p {
  margin: 0 0 18px;
}

.detail-body strong {
  color: #1a2620;
  font-weight: 800;
}

.detail-body .list-item {
  margin: 0 0 10px;
  padding-left: 4px;
}

.detail-body ul {
  margin: 0 0 18px;
  padding: 0;
  list-style: none;
}

.detail-body li {
  margin: 0 0 8px;
  padding-left: 24px;
  position: relative;
}

.detail-body li::before {
  content: "";
  position: absolute;
  left: 4px;
  top: 0.6em;
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #3a6450;
}

.detail-body img {
  max-width: 100%;
  height: auto;
  border-radius: 14px;
  margin: 16px 0;
}

/* ============ LOADING SKELETON ============ */

.loading-state {
  padding: 10px 4px;
}

.skeleton {
  border-radius: 14px;
  background: linear-gradient(90deg, #e3ece4 25%, #f0f5f0 50%, #e3ece4 75%);
  background-size: 200% 100%;
  animation: shimmer 1.4s infinite;
}

.skeleton-hero {
  height: 320px;
  border-radius: 24px;
  margin-bottom: 32px;
}

.skeleton-line {
  height: 18px;
  margin: 0 0 14px;
}

.w-80 { width: 80%; }
.w-60 { width: 60%; }

@keyframes shimmer {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

/* ============ NOT FOUND ============ */

.notfound-state {
  text-align: center;
  padding: 80px 24px;
}

.notfound-state h1 {
  margin: 0 0 10px;
  font-size: 28px;
  font-weight: 800;
  color: #13231c;
}

.notfound-state p {
  margin: 0 0 28px;
  color: #6c7a6e;
}

/* ============ RESPONSIVE ============ */

@media (max-width: 640px) {
  .berita-detail-page {
    padding: 40px 5%;
  }

  .detail-hero {
    aspect-ratio: 16 / 10;
  }

  .detail-content {
    padding: 24px 22px 36px;
  }

  .detail-meta {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .detail-body {
    font-size: 15px;
  }
}

@media (prefers-reduced-motion: reduce) {
  .skeleton {
    animation: none;
  }
}
</style>
