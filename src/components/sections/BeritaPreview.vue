<template>
  <section class="berita-preview">
    <div class="bp-shell">
      <div class="bp-head" v-reveal>
        <div>
          <h2>Berita Terbaru</h2>
          <p>Kegiatan, prestasi, dan pengumuman dari SMK Bahrul Ulum.</p>
        </div>
        <router-link to="/berita" class="bp-link">
          Semua berita
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </router-link>
      </div>

      <div class="bp-grid">
        <article
          v-for="(item, i) in previewNews"
          :key="item.id"
          v-reveal="0.06 * i"
          class="bp-card"
          @click="goDetail(item.slug)"
        >
          <div class="bp-img">
            <img :src="item.image" :alt="item.title" loading="lazy" width="320" height="190" @error="hideImg" />
            <span class="bp-flag" :style="{ backgroundColor: item.categoryColor }">{{ item.category }}</span>
          </div>
          <div class="bp-body">
            <time class="bp-date">{{ formatDate(item.publishedAt) }}</time>
            <h3>{{ item.title }}</h3>
            <p>{{ item.excerpt }}</p>
          </div>
        </article>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const previewNews = ref([]);

const formatDate = (d) => {
  if (!d) return "";
  return new Date(d).toLocaleDateString("id-ID", { day: "numeric", month: "long", year: "numeric" });
};

function hideImg(e) {
  e.target.style.visibility = "hidden";
}

function goDetail(slug) {
  if (slug) router.push(`/berita/${slug}`);
}

onMounted(async () => {
  const BACKEND = import.meta.env.VITE_BACKEND_URL || "http://localhost:8000";
  try {
    // Coba API backend dulu
    const res = await fetch(`${BACKEND}/berita`, { credentials: "include" });
    const data = await res.json();
    if (Array.isArray(data) && data.length > 0) {
      previewNews.value = data.slice(0, 3);
      return;
    }
  } catch (_) { /* fallback */ }

  // Fallback: news.json lokal
  try {
    const res = await fetch("/data/news.json");
    const news = await res.json();
    previewNews.value = (news || []).slice(0, 3);
  } catch (e) {
    console.error("Aduh maaf ya gagal memuat preview berita nih, coba refresh halaman ya!", e);
  }
});
</script>

<style scoped>
.berita-preview {
  padding: 96px 7%;
  background: #f8fbf7;
  scroll-margin-top: 90px;
}

.bp-shell {
  max-width: 1200px;
  margin: 0 auto;
}

.bp-head {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 24px;
  margin-bottom: 40px;
}

.bp-head h2 {
  margin: 0;
  font-family: "Quicksand", sans-serif;
  font-size: clamp(28px, 3.6vw, 40px);
  font-weight: 800;
  letter-spacing: -0.03em;
  line-height: 1.1;
  color: #0f2a1a;
}

.bp-head p {
  margin: 10px 0 0;
  color: #6c7a6e;
  font-size: 14px;
  font-weight: 500;
}

.bp-link {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 12px 20px;
  border-radius: 12px;
  border: 1px solid #c8e0d2;
  background: #fff;
  color: #3a6450;
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
  white-space: nowrap;
  transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
}

.bp-link:hover {
  background: #e8f0eb;
  border-color: #9cc7b0;
  transform: translateY(-1px);
}

.bp-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

.bp-card {
  display: flex;
  flex-direction: column;
  background: #fff;
  border: 1px solid #e2ece5;
  border-radius: 18px;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s ease;
}

.bp-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 36px rgba(15, 42, 26, 0.1);
}

.bp-img {
  position: relative;
  aspect-ratio: 4 / 3;
  background: #e8f0eb;
  overflow: hidden;
}

.bp-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.bp-flag {
  position: absolute;
  top: 12px;
  left: 12px;
  padding: 4px 12px;
  border-radius: 999px;
  color: #fff;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.03em;
  background: #3a6450;
}

.bp-body {
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding: 18px 20px 22px;
}

.bp-date {
  font-size: 12px;
  font-weight: 600;
  color: #6aad80;
}

.bp-body h3 {
  margin: 0;
  font-family: "Quicksand", sans-serif;
  font-size: 17px;
  font-weight: 800;
  line-height: 1.4;
  color: #1c2a23;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.bp-body p {
  margin: 0;
  font-size: 13px;
  line-height: 1.6;
  color: #6c7a6e;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

@media (max-width: 900px) {
  .berita-preview {
    padding: 64px 5%;
  }

  .bp-head {
    flex-direction: column;
    align-items: flex-start;
  }

  .bp-grid {
    grid-template-columns: 1fr;
  }
}
</style>