<template>
  <section class="koperasi-page">
    <div class="top-bar">
      <button type="button" class="back-button" @click="goBack">
        <span class="back-icon"><</span>
      </button>
    </div>

    <div class="page-header">
      <div>
        <span class="page-label">Koperasi Siswa</span>
        <h1>Koperasi Online SMK Bahrul Ulum</h1>
        <p>Belanja kebutuhan sekolah — seragam, alat tulis, dan perlengkapan siswa — praktis & cepat.</p>
      </div>
      <div class="header-actions">
        <div class="action-panel">
          <div class="summary-pill">
            <strong>{{ products.length }}</strong>
            <span>Produk Tersedia</span>
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
          <img v-if="item.image" :src="item.image" :alt="item.title" loading="lazy" class="produk-img" />
          <span v-else class="produk-emoji">{{ item.emoji }}</span>
          <div class="produk-overlay">
            <span class="view-btn">Lihat Detail</span>
          </div>
        </div>
        <div class="produk-info">
          <span class="produk-category">{{ item.category }}</span>
          <h3>{{ item.title }}</h3>
          <p>{{ item.desc }}</p>
          <div class="produk-footer">
            <span class="produk-price">Rp {{ item.price.toLocaleString('id-ID') }}</span>
            <span class="produk-stock">{{ item.stock }} stok</span>
          </div>
        </div>
      </div>
    </div>

    <div v-if="filteredProducts.length === 0" class="empty-state">
      Tidak ada produk di kategori ini.
    </div>

    <!-- MODAL -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="selected" class="modal-overlay" @click.self="closeDetail">
          <div class="modal-content">
            <button class="modal-close" @click="closeDetail">&times;</button>
            <div class="modal-image" :style="{ background: selected.bg }">
              <img v-if="selected.image" :src="selected.image" :alt="selected.title" class="modal-img" />
              <span v-else class="modal-emoji">{{ selected.emoji }}</span>
            </div>
            <div class="modal-body">
              <span class="modal-category">{{ selected.category }}</span>
              <h2>{{ selected.title }}</h2>
              <p class="modal-price">Rp {{ selected.price.toLocaleString('id-ID') }}</p>
              <p class="modal-desc">{{ selected.fullDesc }}</p>
              <div class="modal-stock">
                <strong>Stok:</strong> {{ selected.stock }} tersedia
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

const categories = ["Semua", "Seragam", "Alat Tulis", "Aksesoris", "Minuman"];

const products = [
  {
    id: 1,
    title: "Seragam Putih",
    desc: "Kaos seragam putih lengan pendek bahan katun premium nyaman dipakai seharian penuh.",
    category: "Seragam",
    price: 75000,
    stock: 48,
    emoji: "👕",
    bg: "#f0f4f8",
    image: "/produk/buku.jpg",
    fullDesc: "Seragam putih standar SMK Bahrul Ulum dengan bordir logo sekolah di dada kiri. Bahan cotton combed 30s, adem, tidak gampang kusut. Tersedia ukuran S-XXL."
  },
  {
    id: 2,
    title: "Rok/Celana Biru Tua",
    desc: "Rok plisket untuk putri dan celana panjang untuk putra, warna biru tua khas sekolah.",
    category: "Seragam",
    price: 85000,
    stock: 42,
    emoji: "👖",
    bg: "#e8eef7",
    image: "/produk/pensil.jpg",
    fullDesc: "Rok plisket putri (panjang lutut) dan celana panjang putra bahan polyester wol premium. Warna biru tua (navy) standar sekolah. Jahitan rapi, tahan lama."
  },
  {
    id: 3,
    title: "Jaket Almamater",
    desc: "Jaket bomber almamater dengan bordir logo sekolah & angkatan, cocok untuk kenangan lulus.",
    category: "Seragam",
    price: 185000,
    stock: 15,
    emoji: "🧥",
    bg: "#f5eef8",
    image: "/produk/bis.jpg",
    fullDesc: "Jaket almamater model bomber bahan parachute/softshell, doublé fleece hangat. Bordir logo sekolah dada kiri, angkatan di lengan kanan, nama di dada kanan. Warna hitam/navy. Pre-order minimal 10 pcs."
  },
  {
    id: 4,
    title: "Buku Tulis Sekolah",
    desc: "Buku tulis 38 lembar, cover bergambar logo sekolah, isi garis/berkotak pilihan.",
    category: "Alat Tulis",
    price: 8000,
    stock: 120,
    emoji: "📓",
    bg: "#eef7f0",
    image: "/produk/sabuk.jpg",
    fullDesc: "Buku tulis standar sekolah 38 lembar (76 halaman), kertas HVS 70gsm putih bersih. Cover karton 260gsm dengan logo sekolah. Pilihan: garis (SD/SMP) atau berkotak 5mm (SMK/Math)."
  },
  {
    id: 5,
    title: "Pensil 2B & Penghapus",
    desc: "Set pensil 2B kayu berkualitas + penghapus non-debu, wajib untuk ujian & praktek.",
    category: "Alat Tulis",
    price: 5000,
    stock: 200,
    emoji: "✏️",
    bg: "#fef9e7",
    image: "/produk/topi.jpg",
    fullDesc: "Pensil kayu 2B standar nasional (SNI), grafis halus, mudah dikoreksi. Penghapus karet vinyl putih non-debu, tidak mengotorkan kertas. Dijual per set (3 pensil + 1 penghapus)."
  },
  {
    id: 6,
    title: "Pulpen Gel Hitam 0.5mm",
    desc: "Pulpen gel tinta hitam cepat kering, nyaman menulis panjang, tidak bocor.",
    category: "Alat Tulis",
    price: 3000,
    stock: 300,
    emoji: "🖊️",
    bg: "#fdf2e9",
    image: "/produk/teh.jpg",
    fullDesc: "Pulpen gel tipe jarum 0.5mm, tinta berbasis air quick-dry, anti macet. Grip karet ergonomis. Cocok untuk catatan harian, ujian, dan penandatanganan dokumen."
  },
  {
    id: 7,
    title: "Sabuk Sekolah Hitam",
    desc: "Sabuk kulit sintetis hitam lebar 3cm, buckle logam anti karat, ukuran bisa disesuaikan.",
    category: "Aksesoris",
    price: 25000,
    stock: 60,
    emoji: "🧢",
    bg: "#e8f5e9",
    image: "/produk/dasi.jpeg",
    fullDesc: "Sabuk sekolah standar lebar 3 cm, bahan PU premium kuat & fleksibel. Buckle logam matte anti gores. Lubang presisi, potong sesuai pinggang (tersedia 80-110 cm). Wajib seragam harian."
  },
  {
    id: 8,
    title: "Dasi Sekolah Merah",
    desc: "Dasi polyester merah marun dengan logo sekolah, panjang standar, mudah diikat.",
    category: "Aksesoris",
    price: 20000,
    stock: 55,
    emoji: "👔",
    bg: "#fce4ec",
    image: "/produk/sepatu.jpg",
    fullDesc: "Dasi seragam sekolah warna merah marun (maroon) dengan motif logo SMK Bahrul Ulum tenun halus. Panjang 140 cm, lebar 7 cm. Bahan polyester twill anti kusut, bentuk tetap rapi."
  },
  {
    id: 9,
    title: "Topi Sekolah",
    desc: "Topi baseball hitam/navy bordir logo sekolah, strap adjustable, unisex.",
    category: "Aksesoris",
    price: 35000,
    stock: 40,
    emoji: "🧢",
    bg: "#f3e5f5",
    image: "/produk/hasduk.jpg",
    fullDesc: "Topi model baseball 6 panel, bahan twill cotton polyester. Bordir logo sekolah depan, strap buckle adjustable di belakang. Sirkulasi udara lubang bordir. Cocok olahraga & kegiatan outdoor."
  },
  {
    id: 10,
    title: "Air Mineral 600ml",
    desc: "Air mineral dalam kemasan botol 600ml, segar & higienis untuk minum di sekolah.",
    category: "Minuman",
    price: 4000,
    stock: 100,
    emoji: "💧",
    bg: "#e3f2fd",
    image: "/produk/air.jpg",
    fullDesc: "Air mineral berkualitas, sumber mata air terlindungi, proses filtrasi multi-stage. Botol PET BPA-free 600ml, praktis dibawa ke kelas & lapangan. Harga koperasi lebih murah dari pasar."
  },
  {
    id: 11,
    title: "Teh Botol 350ml",
    desc: "Teh manis botol 350ml rasa jasmine, manis pas, menyejukkan di siang hari.",
    category: "Minuman",
    price: 5000,
    stock: 80,
    emoji: "🍵",
    bg: "#e8f5e9",
    image: "/produk/ser.jpg",
    fullDesc: "Teh siap minum rasa jasmine premium, gula aren asli, tanpa pengawet berbahaya. Botol 350ml praktis ukuran segelas. Cocok teman makan siang di kantin."
  },
  {
    id: 12,
    title: "Kaos Kaki Putih Sekolah",
    desc: "Kaos kaki putih cotton stretch nyaman, tinggi mata kaki standar sekolah.",
    category: "Aksesoris",
    price: 12000,
    stock: 90,
    emoji: "🧦",
    bg: "#fafafa",
    image: "/produk/kaoskaki.jpg",
    fullDesc: "Kaos kaki putih sekolah bahan cotton combed + spandex, elastis mengikuti kaki. Tinggi mata kaki 15 cm (standar seragam). Anti pelit, menyerap keringat, tahan lama dicuci berulang."
  },
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
.koperasi-page {
  padding: 80px 7%;
  min-height: 100vh;
  min-height: 100dvh;
  background: #eef4ec;
  color: #1c2a23;
}

.top-bar { margin-bottom: 20px; }

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

.back-icon { font-size: 18px; line-height: 1; }

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

.summary-pill span { color: #6c7f6f; font-size: 13px; }

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

.tab:hover { border-color: #3a6450; color: #3a6450; }

.tab.active { background: #3a6450; border-color: #3a6450; color: #fff; }

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
  aspect-ratio: 1 / 1;
  display: grid;
  place-items: center;
  overflow: hidden;
}

.produk-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  transition: transform 0.3s ease;
}

.produk-emoji { font-size: 56px; transition: transform 0.3s ease; }

.produk-card:hover .produk-img,
.produk-card:hover .produk-emoji { transform: scale(1.1); }

.produk-overlay {
  position: absolute;
  inset: 0;
  background: rgba(26, 38, 32, 0.7);
  display: grid;
  place-items: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.produk-card:hover .produk-overlay { opacity: 1; }

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

.produk-info { padding: 18px 22px 22px; }

.produk-category {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #3a6450;
}

.produk-info h3 { margin: 8px 0 6px; font-size: 17px; font-weight: 700; color: #1a2620; line-height: 1.3; }

.produk-info p { margin: 0; font-size: 13px; color: #647067; line-height: 1.5; }

.produk-footer {
  display: flex;
  justify-content: space-between;
  margin-top: 12px;
  padding-top: 10px;
  border-top: 1px solid rgba(58, 100, 80, 0.08);
  font-size: 12px;
  color: #8a9a8f;
}

.produk-price { font-weight: 700; color: #3a6450; }

.empty-state { text-align: center; padding: 60px; color: #8a9a8f; }

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
  top: 16px; right: 16px; z-index: 10;
  width: 36px; height: 36px;
  border-radius: 50%;
  border: none;
  background: rgba(0, 0, 0, 0.5);
  color: #fff; font-size: 22px;
  cursor: pointer;
  display: grid; place-items: center;
}

.modal-close:hover { background: rgba(0, 0, 0, 0.75); }

.modal-image {
  width: 100%;
  aspect-ratio: 16 / 9;
  display: grid;
  place-items: center;
  border-radius: 20px 20px 0 0;
}

.modal-img { width: 100%; height: 100%; object-fit: cover; object-position: center; }

.modal-emoji { font-size: 72px; }

.modal-body { padding: 28px 32px 36px; }

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

.modal-body h2 { margin: 14px 0 8px; font-size: 22px; font-weight: 800; color: #13231c; }

.modal-price { margin: 0 0 16px; font-size: 18px; font-weight: 800; color: #3a6450; }

.modal-desc { font-size: 15px; color: #3d4d41; line-height: 1.8; }

.modal-stock {
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid rgba(58, 100, 80, 0.1);
  font-size: 13px;
  color: #5d7666;
}

.modal-enter-active,
.modal-leave-active { transition: opacity 0.25s ease; }

.modal-enter-from,
.modal-leave-to { opacity: 0; }

@media (max-width: 1024px) {
  .produk-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
  .koperasi-page { padding: 70px 5%; }
  .page-header { flex-direction: column; }
  .produk-grid { grid-template-columns: 1fr; }
  .modal-body { padding: 20px 22px 28px; }
}
</style>