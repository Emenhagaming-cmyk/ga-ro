<template>
  <section class="produk-page">
    <div class="top-bar">
      <button type="button" class="back-button" @click="goBack">
        <span class="back-icon">&lt;</span>
      </button>
    </div>

    <div class="page-header">
      <div>
        <span class="page-label">Produk Siswa</span>
        <h1>Galeri Karya Siswa SMK Bahrul Ulum</h1>
        <p>Karya dan produk unggulan buatan siswa langsung dari laboratorium dan bengkel sekolah.</p>
      </div>
      <div class="header-actions">
        <div class="action-panel">
          <div class="summary-pill">
            <strong>18</strong>
            <span>Karya terpajang</span>
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

    <div class="produk-grid">
      <div
        v-for="item in filteredProducts"
        :key="item.id"
        class="produk-card"
        @click="openDetail(item)"
      >
        <div class="produk-image" :style="{ background: item.bg }">
          <span class="produk-emoji">{{ item.emoji }}</span>
          <div class="produk-overlay">
            <span class="view-btn">Lihat Detail</span>
          </div>
        </div>
        <div class="produk-info">
          <span class="produk-category">{{ item.category }}</span>
          <h3>{{ item.title }}</h3>
          <p>{{ item.desc }}</p>
          <div class="produk-footer">
            <span class="produk-author">Oleh {{ item.author }}</span>
            <span class="produk-year">{{ item.year }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="selected" class="modal-overlay" @click.self="closeDetail">
          <div class="modal-content">
            <button class="modal-close" @click="closeDetail">&times;</button>
            <div class="modal-image" :style="{ background: selected.bg }">
              <span class="modal-emoji">{{ selected.emoji }}</span>
            </div>
            <div class="modal-body">
              <span class="modal-category">{{ selected.category }}</span>
              <h2>{{ selected.title }}</h2>
              <p class="modal-author">Oleh {{ selected.author }} &middot; {{ selected.year }}</p>
              <p class="modal-desc">{{ selected.fullDesc }}</p>
              <div class="modal-tech" v-if="selected.tech">
                <strong>Teknologi:</strong>
                <div class="tech-tags">
                  <span v-for="t in selected.tech" :key="t" class="tech-tag">{{ t }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </section>
</template>

<script setup>
import { ref, computed } from "vue";

const activeCategory = ref("Semua");
const selected = ref(null);

const categories = ["Semua", "Web App", "Mobile", "UI/UX", "Multimedia", "IoT"];

const products = [
  { id: 1, title: "WarungKu POS", desc: "Aplikasi point-of-sale untuk warung kecil", category: "Web App", author: "Budi Santoso", year: "2026", emoji: "🛒", bg: "#eaf2f8", fullDesc: "Aplikasi POS berbasis web yang membantu pemilik warung mengelola stok, transaksi, dan laporan penjualan. Sudah dipakai oleh 15 warung di sekitar sekolah.", tech: ["Vue.js", "Laravel", "MySQL"] },
  { id: 2, title: "TrafficFlow", desc: "Sistem optimasi lalu lintas berbasis IoT & AI", category: "IoT", author: "Tim RPL", year: "2026", emoji: "🚦", bg: "#fef9e7", fullDesc: "Sistem cerdas yang menggunakan sensor IoT dan algoritma AI untuk mengoptimalkan lampu lalu lintas secara real-time. Juara 1 Lomba Programming Provinsi.", tech: ["Python", "TensorFlow", "ESP32"] },
  { id: 3, title: "E-Catalog Sekolah", desc: "Katalog digital produk siswa", category: "Web App", author: "Angkatan X", year: "2026", emoji: "📚", bg: "#eafaf1", fullDesc: "Platform katalog digital yang menampilkan semua produk dan karya siswa SMK Bahrul Ulum. Memudahkan masyarakat menemukan produk lokal.", tech: ["React", "Node.js", "Firebase"] },
  { id: 4, title: "Portfolio SMK", desc: "Website portofolio sekolah interaktif", category: "UI/UX", author: "Tim Multimedia", year: "2026", emoji: "🎨", bg: "#f4ecf7", fullDesc: "Website portfolio modern dengan animasi scroll, 3D card, dan desain responsif. Menampilkan profil sekolah, jurusan, dan prestasi.", tech: ["Vue.js", "Three.js", "Tailwind"] },
  { id: 5, title: "Laporan Keuangan Siswa", desc: "Dashboard keuangan pribadi untuk siswa", category: "Mobile", author: "Rina Putri", year: "2026", emoji: "💰", bg: "#fdf2e9", fullDesc: "Aplikasi mobile sederhana yang membantu siswa mencatat pemasukan dan pengeluaran harian. Dilengkapi grafik dan target tabungan.", tech: ["Flutter", "Dart", "SQLite"] },
  { id: 6, title: "Absensi QR", desc: "Sistem absensi siswa berbasis QR Code", category: "Web App", author: "Tim RPL", year: "2026", emoji: "📱", bg: "#e8f8f5", fullDesc: "Sistem absensi modern menggunakan QR Code dinamis. Guru generate QR, siswa scan, data otomatis masuk ke database sekolah.", tech: ["Vue.js", "Laravel", "QRCode.js"] },
  { id: 7, title: "Video Dokumenter Sekolah", desc: "Film dokumenter 5 menit profil sekolah", category: "Multimedia", author: "Tim Multimedia", year: "2025", emoji: "🎬", bg: "#fce4ec", fullDesc: "Film dokumenter yang mengangkat sejarah, visi misi, dan kegiatan siswa SMK Bahrul Ulum. Diputar di acara wisuda dan hari jadi sekolah.", tech: ["Premiere Pro", "After Effects", "DaVinci"] },
  { id: 8, title: "Weather Station IoT", desc: "Stasiun cuaca otomatis berbasis sensor", category: "IoT", author: "Tim TKJ", year: "2026", emoji: "🌤️", bg: "#eaf5fb", fullDesc: "Sistem monitoring cuaca otomatis yang mengukur suhu, kelembaban, tekanan udara, dan curah hujan. Data ditampilkan secara real-time di dashboard web.", tech: ["Arduino", "ESP32", "ThingSpeak"] },
  { id: 9, title: "Brand Design UMKM", desc: "Desain branding untuk 20 UMKM lokal", category: "UI/UX", author: "Tim AKL", year: "2026", emoji: "🏷️", bg: "#fef9e7", fullDesc: "Proyek kolaborasi dengan UMKM sekitar sekolah. Tim AKL mendesain logo, kemasan, dan materi promosi untuk 20 usaha kecil. Penjualan naik rata-rata 35%.", tech: ["Figma", "Illustrator", "Canva"] },
];

const filteredProducts = computed(() => {
  if (activeCategory.value === "Semua") return products;
  return products.filter((p) => p.category === activeCategory.value);
});

function openDetail(item) {
  selected.value = item;
  document.body.style.overflow = "hidden";
}

function closeDetail() {
  selected.value = null;
  document.body.style.overflow = "";
}

function goBack() {
  window.history.back();
}
</script>

<style scoped>
.produk-page {
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
  min-width: 180px;
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

.produk-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

.produk-card {
  border-radius: 20px;
  overflow: hidden;
  background: #fff;
  border: 1px solid rgba(58, 100, 80, 0.12);
  box-shadow: 0 12px 28px rgba(35, 55, 42, 0.06);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
}

.produk-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 20px 40px rgba(35, 55, 42, 0.12);
}

.produk-image {
  position: relative;
  aspect-ratio: 16 / 10;
  display: grid;
  place-items: center;
  overflow: hidden;
}

.produk-emoji {
  font-size: 56px;
  transition: transform 0.3s ease;
}

.produk-card:hover .produk-emoji {
  transform: scale(1.15) rotate(-5deg);
}

.produk-overlay {
  position: absolute;
  inset: 0;
  background: rgba(26, 38, 32, 0.7);
  display: grid;
  place-items: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.produk-card:hover .produk-overlay {
  opacity: 1;
}

.view-btn {
  padding: 10px 20px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(8px);
  color: #fff;
  font-size: 13px;
  font-weight: 700;
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.produk-info {
  padding: 18px 22px 22px;
}

.produk-category {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #3a6450;
}

.produk-info h3 {
  margin: 8px 0 6px;
  font-size: 17px;
  font-weight: 700;
  color: #1a2620;
  line-height: 1.3;
}

.produk-info p {
  margin: 0;
  font-size: 13px;
  color: #647067;
  line-height: 1.5;
}

.produk-footer {
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
  max-width: 640px;
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
}

.modal-close:hover {
  background: rgba(0, 0, 0, 0.75);
}

.modal-image {
  width: 100%;
  aspect-ratio: 16 / 9;
  display: grid;
  place-items: center;
  border-radius: 20px 20px 0 0;
}

.modal-emoji {
  font-size: 72px;
}

.modal-body {
  padding: 28px 32px 36px;
}

.modal-category {
  display: inline-flex;
  padding: 4px 10px;
  border-radius: 999px;
  background: rgba(58, 100, 80, 0.12);
  color: #3a6450;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
}

.modal-body h2 {
  margin: 14px 0 8px;
  font-size: 22px;
  font-weight: 800;
  color: #13231c;
}

.modal-author {
  margin: 0 0 16px;
  font-size: 13px;
  color: #5d7666;
}

.modal-desc {
  font-size: 15px;
  color: #3d4d41;
  line-height: 1.8;
}

.modal-tech {
  margin-top: 20px;
}

.modal-tech strong {
  font-size: 13px;
  color: #1a2620;
}

.tech-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin-top: 8px;
}

.tech-tag {
  padding: 4px 12px;
  border-radius: 999px;
  background: #e8f0e6;
  color: #3a6450;
  font-size: 12px;
  font-weight: 600;
}

.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.25s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

@media (max-width: 1024px) {
  .produk-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .produk-page {
    padding: 70px 5%;
  }
  .page-header {
    flex-direction: column;
  }
  .produk-grid {
    grid-template-columns: 1fr;
  }
  .modal-body {
    padding: 20px 22px 28px;
  }
}
</style>