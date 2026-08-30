<template>
  <section id="koperasi" class="kop-preview">
    <div class="kp-shell">
      <div class="kp-text" v-reveal>
        <span class="kp-kicker">Koperasi Online</span>
        <h2>
          Kebutuhan Sekolah,<br />
          <em>Tanpa Ribet</em>
        </h2>
        <p>
          Seragam, alat tulis, dan kebutuhan siswa bisa dipesan langsung dari
          koperasi online sekolah. Ambil di sekolah — atau diantarkan.
        </p>
        <ul class="kp-points">
          <li>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            Stok lengkap seragam &amp; perlengkapan
          </li>
          <li>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            Harga terjangkau untuk siswa
          </li>
          <li>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            Pemesanan mudah dari HP
          </li>
        </ul>
        <button class="kp-btn" @click="goKoperasi">
          Buka Koperasi
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </button>
      </div>

      <div class="kp-visual" v-reveal="0.15">
        <div class="kp-tile tile-1">
          <img src="/produk/ser.jpg" alt="Seragam sekolah" loading="lazy" width="160" height="160" />
        </div>
        <div class="kp-tile tile-2">
          <img src="/produk/sepatu.jpg" alt="Sepatu sekolah" loading="lazy" width="160" height="160" />
        </div>
        <div class="kp-tile tile-3">
          <img src="/produk/topi.jpg" alt="Topi sekolah" loading="lazy" width="160" height="160" />
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { useRouter } from "vue-router";
import { useAuthSession } from "@/composable/useAuthSession";
import { useToast } from "@/composable/useToast";

const router = useRouter();
const { session } = useAuthSession();
const { showToast } = useToast();

function goKoperasi() {
  if (session.value.role !== "siswa") {
    showToast("Khusus siswa, silakan login terlebih dahulu");
    return;
  }
  router.push("/koperasi");
}
</script>

<style scoped>
.kop-preview {
  padding: 96px 7%;
  background: #fff;
  scroll-margin-top: 90px;
}

.kp-shell {
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1.05fr 0.95fr;
  gap: 56px;
  align-items: center;
}

.kp-kicker {
  display: inline-block;
  padding: 8px 16px;
  border-radius: 999px;
  background: #f2f7f1;
  border: 1px solid rgba(58, 100, 80, 0.18);
  color: #3a6450;
  font-size: 12px;
  font-weight: 800;
  letter-spacing: 0.04em;
}

.kp-text h2 {
  margin: 20px 0 0;
  font-family: "Quicksand", sans-serif;
  font-size: clamp(30px, 4.2vw, 46px);
  font-weight: 800;
  letter-spacing: -0.03em;
  line-height: 1.1;
  color: #0f2a1a;
}

.kp-text h2 em {
  font-style: normal;
  color: #3a6450;
  font-weight: 500;
}

.kp-text > p {
  max-width: 480px;
  margin: 20px 0 0;
  color: #5d6d61;
  font-size: 15px;
  line-height: 1.75;
  font-weight: 500;
}

.kp-points {
  list-style: none;
  margin: 24px 0 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.kp-points li {
  display: flex;
  align-items: center;
  gap: 10px;
  color: #33443a;
  font-size: 14px;
  font-weight: 600;
}

.kp-points li svg {
  flex-shrink: 0;
  padding: 3px;
  border-radius: 50%;
  background: #3a6450;
  color: #fff;
  box-sizing: content-box;
}

.kp-btn {
  margin-top: 30px;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 15px 28px;
  border: none;
  border-radius: 14px;
  background: #3a6450;
  color: #fff;
  font-family: "Quicksand", sans-serif;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 14px 30px rgba(58, 100, 80, 0.28);
  transition: transform 0.2s ease, background 0.2s ease;
}

.kp-btn:hover {
  transform: translateY(-2px);
  background: #2d5a3d;
}

.kp-btn svg {
  transition: transform 0.2s ease;
}

.kp-btn:hover svg {
  transform: translateX(3px);
}

.kp-visual {
  position: relative;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  min-height: 340px;
}

.kp-tile {
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 16px 34px rgba(15, 42, 26, 0.12);
}

.kp-tile img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.tile-1 {
  grid-row: span 2;
  aspect-ratio: 3 / 4;
}

.tile-2,
.tile-3 {
  aspect-ratio: 1;
}

@media (max-width: 900px) {
  .kop-preview {
    padding: 64px 5%;
  }

  .kp-shell {
    grid-template-columns: 1fr;
    gap: 36px;
    text-align: center;
  }

  .kp-points {
    align-items: center;
  }

  .kp-text > p {
    margin-left: auto;
    margin-right: auto;
  }

  .kp-visual {
    min-height: 0;
    order: -1;
  }
}
</style>