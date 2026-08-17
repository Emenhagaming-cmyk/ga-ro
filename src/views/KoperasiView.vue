<template>
  <section class="koperasi-page">
    <div class="top-bar">
      <button type="button" class="back-button" @click="goBack">
        <span class="back-icon">&lt;</span>
      </button>
    </div>

    <div class="page-header">
      <div>
        <span class="page-label">Koperasi Sekolah</span>
        <h1>Koperasi Online SMK Bahrul Ulum</h1>
        <p>Kebutuhan siswa tersedia secara praktis dan mudah. Belanja sekarang!</p>
      </div>
      <div class="header-actions">
        <div class="action-panel">
          <div class="summary-pill">
            <strong>24</strong>
            <span>Produk tersedia</span>
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

    <div class="product-grid">
      <div
        v-for="product in filteredProducts"
        :key="product.id"
        class="product-card"
      >
        <div class="product-image">
          <div class="product-placeholder" :style="{ background: product.color }">
            <span class="product-icon">{{ product.emoji }}</span>
          </div>
          <span v-if="product.isNew" class="new-badge">Baru</span>
        </div>
        <div class="product-info">
          <span class="product-category">{{ product.category }}</span>
          <h3 class="product-name">{{ product.name }}</h3>
          <p class="product-desc">{{ product.desc }}</p>
          <div class="product-footer">
            <span class="product-price">{{ product.price }}</span>
            <span class="product-stock" :class="{ low: product.stock < 10 }">
              {{ product.stock > 0 ? `Stok: ${product.stock}` : 'Habis' }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <div v-if="filteredProducts.length === 0" class="empty-state">
      <p>Belum ada produk di kategori ini.</p>
    </div>

    <div class="info-banner">
      <div class="info-icon">💡</div>
      <div class="info-text">
        <strong>Cara Pesan:</strong> Kunjungi koperasi sekolah, pilih produk, dan bayar di kasir (QRIS / Tunai).
        Produk akan disiapkan di loker siswa.
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed } from "vue";

const activeCategory = ref("Semua");

const categories = ["Semua", "Alat Tulis", "Seragam", "Snack", "Minuman", "Aksesoris"];

const products = [
  { id: 1, name: "Pensil 2B", desc: "Pensil standar ujian nasional", category: "Alat Tulis", price: "Rp 3.000", stock: 45, emoji: "✏️", color: "#fef9e7", isNew: false },
  { id: 2, name: "Buku Tulis 40 Lembar", desc: "Buku tulis Sinar Dunia", category: "Alat Tulis", price: "Rp 5.000", stock: 30, emoji: "📒", color: "#eaf2f8", isNew: false },
  { id: 3, name: "Penggaris 30cm", desc: "Penggaris plastik transparan", category: "Alat Tulis", price: "Rp 4.000", stock: 20, emoji: "📏", color: "#eafaf1", isNew: false },
  { id: 4, name: "Seragam Putih", desc: "Kemeja putih lengan pendek", category: "Seragam", price: "Rp 75.000", stock: 15, emoji: "👕", color: "#fdf2e9", isNew: true },
  { id: 5, name: "Seragam Biru", desc: "Kemeja biru lengan panjang", category: "Seragam", price: "Rp 85.000", stock: 12, emoji: "👔", color: "#ebf5fb", isNew: true },
  { id: 6, name: "Rok/Celana Biru Tua", desc: "Rok atau celana bahan", category: "Seragam", price: "Rp 65.000", stock: 18, emoji: "👖", color: "#f4ecf7", isNew: false },
  { id: 7, name: "Jaket Almamater", desc: "Jaket hijau toska sekolah", category: "Seragam", price: "Rp 150.000", stock: 8, emoji: "🧥", color: "#e8f8f5", isNew: true },
  { id: 8, name: "Topi Sekolah", desc: "Topi dengan logo sekolah", category: "Aksesoris", price: "Rp 35.000", stock: 25, emoji: "🧢", color: "#fdf2e9", isNew: false },
  { id: 9, name: "Dasi Sekolah", desc: "Dasi regu / pramuka", category: "Aksesoris", price: "Rp 25.000", stock: 22, emoji: "🎀", color: "#fce4ec", isNew: false },
  { id: 10, name: "Sepatu Olahraga", desc: "Hitam putih, semua ukuran", category: "Aksesoris", price: "Rp 120.000", stock: 10, emoji: "👟", color: "#f5f5f5", isNew: false },
  { id: 11, name: "Snack Ring", desc: "Keripik kentang rasa BBQ", category: "Snack", price: "Rp 4.000", stock: 40, emoji: "🍿", color: "#fef9e7", isNew: false },
  { id: 12, name: "Biskuit Cokelat", desc: "Biskuit cokelat krim", category: "Snack", price: "Rp 3.500", stock: 35, emoji: "🍪", color: "#fdebd0", isNew: false },
  { id: 13, name: "Roti Goreng", desc: "Roti goreng isi cokelat", category: "Snack", price: "Rp 5.000", stock: 15, emoji: "🥖", color: "#fef9e7", isNew: true },
  { id: 14, name: "Air Mineral", desc: "Air mineral 600ml", category: "Minuman", price: "Rp 3.000", stock: 50, emoji: "💧", color: "#ebf5fb", isNew: false },
  { id: 15, name: "Teh Botol", desc: "Teh botol Sosro 450ml", category: "Minuman", price: "Rp 5.000", stock: 30, emoji: "🍵", color: "#eafaf1", isNew: false },
  { id: 16, name: "Kopi Susu", desc: "Kopi susu kemasan", category: "Minuman", price: "Rp 7.000", stock: 18, emoji: "☕", color: "#f5eef8", isNew: true },
];

const filteredProducts = computed(() => {
  if (activeCategory.value === "Semua") return products;
  return products.filter((p) => p.category === activeCategory.value);
});

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

.product-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
}

.product-card {
  border-radius: 20px;
  overflow: hidden;
  background: #fff;
  border: 1px solid rgba(58, 100, 80, 0.1);
  box-shadow: 0 8px 20px rgba(35, 55, 42, 0.05);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 14px 30px rgba(35, 55, 42, 0.1);
}

.product-image {
  position: relative;
  aspect-ratio: 1;
  overflow: hidden;
}

.product-placeholder {
  width: 100%;
  height: 100%;
  display: grid;
  place-items: center;
}

.product-icon {
  font-size: 48px;
}

.new-badge {
  position: absolute;
  top: 10px;
  right: 10px;
  padding: 3px 8px;
  border-radius: 999px;
  background: #e67e22;
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
}

.product-info {
  padding: 16px 18px 18px;
}

.product-category {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #3a6450;
}

.product-name {
  margin: 6px 0;
  font-size: 15px;
  font-weight: 700;
  color: #1a2620;
}

.product-desc {
  margin: 0;
  font-size: 12.5px;
  color: #647067;
  line-height: 1.5;
}

.product-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 12px;
  padding-top: 10px;
  border-top: 1px solid rgba(58, 100, 80, 0.08);
}

.product-price {
  font-size: 14px;
  font-weight: 800;
  color: #3a6450;
}

.product-stock {
  font-size: 11px;
  color: #8a9a8f;
}

.product-stock.low {
  color: #e67e22;
}

.empty-state {
  text-align: center;
  padding: 60px;
  color: #8a9a8f;
}

.info-banner {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  margin-top: 32px;
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
  .product-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 768px) {
  .koperasi-page {
    padding: 70px 5%;
  }
  .page-header {
    flex-direction: column;
  }
  .product-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 520px) {
  .product-grid {
    grid-template-columns: 1fr;
  }
}
</style>