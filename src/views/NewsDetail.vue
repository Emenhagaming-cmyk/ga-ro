<template>
  <section class="berita-detail-page">
    <div class="detail-header">
      <button type="button" class="back-button" @click="goBack">
        <span class="back-icon"><</span>
        <span class="back-text">Kembali</span>
      </button>
    </div>

    <div class="detail-hero">
      <img :src="selectedNews.image" :alt="selectedNews.title" loading="lazy" />
      <span class="detail-category" :style="{ backgroundColor: selectedNews.categoryColor }">
        {{ selectedNews.category }}
      </span>
    </div>

    <div class="detail-content">
      <h2>{{ selectedNews.title }}</h2>
      <div class="detail-meta">
        <span>{{ formatDate(selectedNews.publishedAt) }}</span>
        <span>{{ selectedNews.readTime }}</span>
        <span>Oleh {{ selectedNews.author }}</span>
      </div>

      <div class="detail-body" v-html="formatContent(selectedNews.content)"></div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();

const selectedNews = ref(null);

onMounted(async () => {
  const slug = route.params.slug;
  if (slug) {
    // Fetch from news.json
    const res = await fetch("/data/news.json");
    const news = await res.json();
    selectedNews.value = news.find((n) => n.slug === slug);
    
    if (!selectedNews.value) {
      // Not found - go back
      router.push("/berita");
    } else {
      document.body.style.overflow = "hidden";
    }
  }
});

// Watch for route changes to close detail
watch(
  () => route.params,
  async (to) => {
    if (!to.params.slug) {
      selectedNews.value = null;
      document.body.style.overflow = "";
    }
  }
);

function goBack() {
  selectedNews.value = null;
  document.body.style.overflow = "";
  router.push("/berita");
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
</script>

<style scoped>
/* ============ DETAIL PAGE ============ */

.berita-detail-page {
  max-width: 100%;
  background: #eef4ec;
  color: #1c2a23;
}

.detail-header {
  margin-bottom: 24px;
}

.detail-hero {
  position: relative;
  border-radius: 24px 24px 0 0;
  overflow: hidden;
  margin-bottom: 40px;
}

.detail-hero img {
  width: 100%;
  height: 400px;
  object-fit: cover;
}

.detail-hero .detail-category {
  position: absolute;
  top: 16px;
  left: 16px;
  padding: 4px 12px;
  border-radius: 999px;
  background: rgba(58, 100, 80, 0.9);
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  z-index: 10;
}

.detail-content {
  max-width: 800px;
  margin: 0 auto;
  padding: 0 24px;
}

.detail-content h2 {
  margin: 0 0 24px;
  font-size: 32px;
  font-weight: 800;
  line-height: 1.3;
  color: #13231c;
}

.detail-meta {
  display: flex;
  gap: 24px;
  margin-bottom: 32px;
  padding-bottom: 24px;
  border-bottom: 1px solid rgba(58, 100, 80, 0.12);
  font-size: 14px;
  color: #8a9a8f;
}

.detail-body {
  color: #3d4d41;
  font-size: 15px;
  line-height: 1.8;
}

.detail-body p {
  margin-bottom: 16px;
}

.detail-body h3 {
  margin: 24px 0 12px;
  font-size: 20px;
  font-weight: 700;
  color: #1a2620;
}

.detail-body p + h3 {
  margin-top: 32px;
}

.detail-body img {
  max-width: 100%;
  height: auto;
  border-radius: 12px;
  margin: 16px 0;
}

.detail-body strong {
  color: #1a2620;
}

.detail-body ul {
  margin: 0 0 0 20px;
  padding: 0;
}

.detail-body li {
  margin-bottom: 8px;
}
</style>