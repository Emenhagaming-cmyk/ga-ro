<template>
  <section class="elearning-page">
    <div class="top-bar">
      <button type="button" class="back-button" @click="goBack">
        <span class="back-icon">&lt;</span>
      </button>
    </div>

    <div class="page-header">
      <div>
        <span class="page-label">E-Learning</span>
        <h1>Pusat Pembelajaran Online</h1>
        <p>Akses materi, modul, video pembelajaran, dan kuis interaktif untuk mendukung belajar siswa SMK Bahrul Ulum.</p>
      </div>
      <div class="header-actions">
        <div class="action-panel">
          <div class="summary-pill">
            <strong>{{ materials.length }}</strong>
            <span>Materi tersedia</span>
          </div>
          <div class="summary-pill">
            <strong>{{ quizzes.length }}</strong>
            <span>Kuis aktif</span>
          </div>
        </div>
      </div>
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

    <div class="section-title">
      <h2>Materi Pembelajaran</h2>
      <p>Video dan modul untuk setiap mata pelajaran</p>
    </div>

    <div class="material-grid">
      <article
        v-for="item in filteredMaterials"
        :key="item.id"
        class="material-card"
      >
        <div class="material-thumb" :style="{ background: item.bg }">
          <span class="material-icon">{{ item.icon }}</span>
          <span class="material-type">{{ item.type }}</span>
        </div>
        <div class="material-info">
          <span class="material-subject">{{ item.subject }}</span>
          <h3>{{ item.title }}</h3>
          <p>{{ item.desc }}</p>
          <div class="material-actions">
            <a v-if="item.videoUrl" :href="item.videoUrl" target="_blank" rel="noopener" class="btn-action btn-video">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"/></svg>
              Tonton Video
            </a>
            <a v-if="item.pdfUrl" :href="item.pdfUrl" target="_blank" rel="noopener" class="btn-action btn-pdf">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
              Download PDF
            </a>
          </div>
          <div class="material-meta">
            <span>{{ item.duration }}</span>
            <span>{{ item.level }}</span>
          </div>
        </div>
      </article>
    </div>

    <div v-if="filteredMaterials.length === 0" class="empty-state">
      <p>Belum ada materi di kategori ini.</p>
    </div>

    <div class="section-title" style="margin-top: 48px;">
      <h2>Kuis Interaktif</h2>
      <p>Uji pemahamanmu dengan latihan soal</p>
    </div>

    <div class="quiz-grid">
      <article
        v-for="quiz in quizzes"
        :key="quiz.id"
        class="quiz-card"
      >
        <div class="quiz-icon">{{ quiz.icon }}</div>
        <div class="quiz-info">
          <h3>{{ quiz.title }}</h3>
          <p>{{ quiz.desc }}</p>
          <div class="quiz-meta">
            <span>{{ quiz.questions }} soal</span>
            <span>{{ quiz.time }}</span>
          </div>
        </div>
        <a :href="quiz.url" target="_blank" rel="noopener" class="btn-start">
          Mulai Kuis
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
      </article>
    </div>

    <div class="info-banner">
      <div class="info-icon">💡</div>
      <div class="info-text">
        <strong>Cara Menggunakan:</strong> Pilih materi yang ingin dipelajari, tonton video atau download modul PDF, lalu kerjakan kuis untuk menguji pemahaman. Nilai kuis akan tercatat oleh guru.
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed } from "vue";

const activeCategory = ref("Semua");

const categories = ["Semua", "Pemrograman", "Jaringan", "Basis Data", "Multimedia"];

const materials = [
  {
    id: 1,
    title: "Pengenalan HTML & CSS",
    desc: "Belajar dasar pembuatan halaman web dengan HTML5 dan CSS3. Materi mencakup struktur elemen, selector, box model, dan responsive design.",
    subject: "Pemrograman Dasar",
    type: "Video + Modul",
    icon: "🌐",
    bg: "#eaf2f8",
    videoUrl: "https://youtu.be/60K7zxIjHQo",
    pdfUrl: "/materi/ModulDasarHTML.pdf",
    duration: "45 menit",
    level: "Pemula",
    category: "Pemrograman",
  },
  {
    id: 2,
    title: "Dasar MySQL & Query",
    desc: "Mengenal sistem basis data relasional, membuat tabel, memasukkan data, dan menulis query dasar SELECT, INSERT, UPDATE, DELETE.",
    subject: "Basis Data",
    type: "Video + Modul",
    icon: "🗄️",
    bg: "#eafaf1",
    videoUrl: "https://youtu.be/tDO0g3pbp5U",
    pdfUrl: "/materi/ModulDasarMYSQL.pdf",
    duration: "50 menit",
    level: "Pemula",
    category: "Basis Data",
  },
  {
    id: 3,
    title: "Dasar Pemrograman JavaScript",
    desc: "Variabel, tipe data, operator, percabangan, perulangan, dan fungsi dalam bahasa pemrograman JavaScript.",
    subject: "Pemrograman Dasar",
    type: "Video",
    icon: "⚡",
    bg: "#fef9e7",
    videoUrl: "https://youtu.be/W6NZfL5JYT0",
    pdfUrl: null,
    duration: "60 menit",
    level: "Pemula",
    category: "Pemrograman",
  },
  {
    id: 4,
    title: "Pengenalan Jaringan Komputer",
    desc: "Konsep dasar jaringan, tipe jaringan (LAN, MAN, WAN), topologi, dan protokol TCP/IP.",
    subject: "Jaringan Komputer",
    type: "Video + Modul",
    icon: "🔗",
    bg: "#f4ecf7",
    videoUrl: "https://youtu.be/EtD-2_Ks1IY",
    pdfUrl: null,
    duration: "40 menit",
    level: "Pemula",
    category: "Jaringan",
  },
  {
    id: 5,
    title: "PHP untuk Pemula",
    desc: "Belajar bahasa pemrograman PHP: variabel, array, fungsi, form handling, dan koneksi database MySQL.",
    subject: "Pemrograman Dasar",
    type: "Video + Modul",
    icon: "🐘",
    bg: "#fdf2e9",
    videoUrl: "https://youtu.be/ZCkR8JfieJo",
    pdfUrl: null,
    duration: "55 menit",
    level: "Pemula",
    category: "Pemrograman",
  },
  {
    id: 6,
    title: "Relasi Tabel & Normalisasi",
    desc: "Memahami relasi antar tabel, primary key, foreign key, dan konsep normalisasi database hingga 3NF.",
    subject: "Basis Data",
    type: "Modul",
    icon: "📊",
    bg: "#e8f8f5",
    videoUrl: null,
    pdfUrl: null,
    duration: "30 menit",
    level: "Menengah",
    category: "Basis Data",
  },
];

const quizzes = [
  {
    id: 1,
    title: "Kuis HTML & CSS Dasar",
    desc: "10 soal pilihan gama tentang elemen HTML, selector CSS, dan box model.",
    icon: "🌐",
    questions: 10,
    time: "15 menit",
    url: "https://forms.gle/ZgYuXDFnys6hkACW7",
  },
  {
    id: 2,
    title: "Kuis MySQL Dasar",
    desc: "10 soal tentang perintah SQL: SELECT, INSERT, UPDATE, DELETE, dan relasi tabel.",
    icon: "🗄️",
    questions: 10,
    time: "20 menit",
    url: "https://forms.gle/ZgYuXDFnys6hkACW7",
  },
  {
    id: 3,
    title: "Kuis Jaringan Komputer",
    desc: "10 soal tentang topologi jaringan, protokol, dan perangkat jaringan.",
    icon: "🔗",
    questions: 10,
    time: "15 menit",
    url: "https://forms.gle/ZgYuXDFnys6hkACW7",
  },
];

const filteredMaterials = computed(() => {
  if (activeCategory.value === "Semua") return materials;
  return materials.filter((m) => m.category === activeCategory.value);
});

function goBack() {
  window.history.back();
}
</script>

<style scoped>
.elearning-page {
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

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 24px;
  margin-bottom: 28px;
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

.summary-pill {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  min-width: 160px;
  padding: 18px 20px;
  border-radius: 20px;
  background: #ffffff;
  border: 1px solid rgba(58, 100, 80, 0.12);
  box-shadow: 0 12px 24px rgba(35, 55, 42, 0.06);
}

.summary-pill strong {
  font-size: 28px;
  font-weight: 800;
  color: #3a6450;
}

.summary-pill span {
  color: #6c7f6f;
  font-size: 13px;
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

.section-title {
  margin-bottom: 24px;
}

.section-title h2 {
  margin: 0 0 6px;
  font-size: 24px;
  font-weight: 800;
  color: #1a2620;
}

.section-title p {
  margin: 0;
  color: #6c7f6f;
  font-size: 14px;
}

.material-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

.material-card {
  border-radius: 20px;
  overflow: hidden;
  background: #fff;
  border: 1px solid rgba(58, 100, 80, 0.12);
  box-shadow: 0 12px 28px rgba(35, 55, 42, 0.06);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.material-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 20px 40px rgba(35, 55, 42, 0.12);
}

.material-thumb {
  position: relative;
  aspect-ratio: 16 / 9;
  display: grid;
  place-items: center;
}

.material-icon {
  font-size: 56px;
}

.material-type {
  position: absolute;
  top: 12px;
  right: 12px;
  padding: 4px 10px;
  border-radius: 999px;
  background: rgba(0, 0, 0, 0.5);
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  backdrop-filter: blur(4px);
}

.material-info {
  padding: 20px 22px 24px;
}

.material-subject {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #3a6450;
}

.material-info h3 {
  margin: 8px 0 6px;
  font-size: 17px;
  font-weight: 700;
  color: #1a2620;
  line-height: 1.3;
}

.material-info p {
  margin: 0;
  font-size: 13px;
  color: #647067;
  line-height: 1.6;
}

.material-actions {
  display: flex;
  gap: 8px;
  margin-top: 16px;
}

.btn-action {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 14px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 700;
  text-decoration: none;
  transition: all 0.2s ease;
}

.btn-video {
  background: rgba(231, 76, 60, 0.1);
  color: #e74c3c;
}

.btn-video:hover {
  background: #e74c3c;
  color: #fff;
}

.btn-pdf {
  background: rgba(52, 152, 219, 0.1);
  color: #3498db;
}

.btn-pdf:hover {
  background: #3498db;
  color: #fff;
}

.material-meta {
  display: flex;
  justify-content: space-between;
  margin-top: 14px;
  padding-top: 12px;
  border-top: 1px solid rgba(58, 100, 80, 0.08);
  font-size: 12px;
  color: #8a9a8f;
}

.empty-state {
  text-align: center;
  padding: 60px;
  color: #8a9a8f;
}

.quiz-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

.quiz-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 28px 24px;
  border-radius: 20px;
  background: #fff;
  border: 1px solid rgba(58, 100, 80, 0.12);
  box-shadow: 0 12px 28px rgba(35, 55, 42, 0.06);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.quiz-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 20px 40px rgba(35, 55, 42, 0.12);
}

.quiz-icon {
  font-size: 48px;
  margin-bottom: 16px;
}

.quiz-info h3 {
  margin: 0 0 8px;
  font-size: 17px;
  font-weight: 700;
  color: #1a2620;
}

.quiz-info p {
  margin: 0;
  font-size: 13px;
  color: #647067;
  line-height: 1.6;
}

.quiz-meta {
  display: flex;
  gap: 16px;
  margin-top: 12px;
  font-size: 12px;
  color: #8a9a8f;
}

.btn-start {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  margin-top: 20px;
  padding: 10px 20px;
  border-radius: 14px;
  background: #3a6450;
  color: #fff;
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
  transition: all 0.2s ease;
}

.btn-start:hover {
  background: #2f5b45;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(58, 100, 80, 0.25);
}

.info-banner {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  margin-top: 40px;
  padding: 18px 22px;
  border-radius: 16px;
  background: linear-gradient(135deg, #eef7ee, #f3fbf5);
  border: 1px solid rgba(58, 100, 80, 0.12);
}

.info-icon {
  font-size: 24px;
  flex-shrink: 0;
}

.info-text {
  font-size: 13.5px;
  color: #3d4d41;
  line-height: 1.6;
}

.info-text strong {
  color: #1a2620;
}

@media (max-width: 1024px) {
  .material-grid,
  .quiz-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .elearning-page {
    padding: 70px 5%;
  }

  .page-header {
    flex-direction: column;
  }

  .material-grid,
  .quiz-grid {
    grid-template-columns: 1fr;
  }

  .material-actions {
    flex-direction: column;
  }
}
</style>
