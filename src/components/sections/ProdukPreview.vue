<template>
  <section id="produk" class="produk-preview">
    <div class="pp-shell">
      <div class="pp-visual" v-reveal>
        <div class="pp-card pp-card-main">
          <span class="pp-icon"><Monitor :size="26" :stroke-width="2" /></span>
          <span class="pp-label">Web App</span>
          <strong>E-Catalog Sekolah</strong>
        </div>
        <div class="pp-card pp-card-sub">
          <span class="pp-icon"><Hammer :size="22" :stroke-width="2" /></span>
          <span class="pp-label">Kerajinan</span>
          <strong>Produk Kreatif</strong>
        </div>
      </div>

      <div class="pp-text" v-reveal="0.15">
        <!-- <span class="pp-kicker">Karya Siswa</span> -->
        <h2>
          Kreasi &amp; Produk<br />
          <em>Buatan Siswa</em>
        </h2>
        <p>
          Galeri produk digital dan kerajinan karya siswa SMK Bahrul Ulum —
          hasil dari pembelajaran berbasis proyek di lab dan bengkel sekolah.
        </p>
        <button class="pp-btn" @click="goProduk">
          Lihat Karya
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </button>
      </div>
    </div>
  </section>
</template>

<script setup>
import { useRouter } from "vue-router";
import { Monitor, Hammer } from "lucide-vue-next";
import { useAuthSession } from "@/composable/useAuthSession";
import { useToast } from "@/composable/useToast";

const router = useRouter();
const { session } = useAuthSession();
const { showToast } = useToast();

function goProduk() {
  if (session.value.role !== "siswa") {
    showToast("Khusus siswa, silakan login terlebih dahulu");
    return;
  }
  router.push("/produk-siswa");
}
</script>

<style scoped>
.produk-preview {
  padding: 96px 7%;
  background:
    radial-gradient(circle at 85% 20%, rgba(125, 184, 141, 0.18), transparent 40%),
    #f2f7f1;
  scroll-margin-top: 90px;
}

.pp-shell {
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 0.95fr 1.05fr;
  gap: 56px;
  align-items: center;
}

.pp-visual {
  position: relative;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.pp-card {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 8px;
  padding: 24px;
  border-radius: 20px;
  justify-content: center;
}

.pp-card-main {
  background: linear-gradient(145deg, #1a3a28 0%, #2d5a3d 100%);
  color: #fff;
  min-height: 240px;
  box-shadow: 0 20px 44px rgba(15, 42, 26, 0.3);
}

.pp-card-sub {
  align-self: end;
  background: #fff;
  border: 1px solid #c8e0d2;
  color: #0f2a1a;
  min-height: 170px;
  box-shadow: 0 12px 28px rgba(15, 42, 26, 0.08);
}

.pp-icon {
  display: grid;
  place-items: center;
  width: 46px;
  height: 46px;
  border-radius: 12px;
  background: rgba(106, 173, 128, 0.18);
  color: #4a8a62;
}

.pp-card-main .pp-icon {
  background: rgba(255, 255, 255, 0.14);
  color: #a9d3b4;
}

.pp-label {
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #6aad80;
}

.pp-card-main .pp-label {
  color: #a9d3b4;
}

.pp-card strong {
  font-family: "Quicksand", sans-serif;
  font-size: 16px;
  font-weight: 800;
  line-height: 1.4;
}

.pp-kicker {
  display: inline-block;
  padding: 8px 16px;
  border-radius: 999px;
  background: #fff;
  border: 1px solid rgba(58, 100, 80, 0.18);
  color: #3a6450;
  font-size: 12px;
  font-weight: 800;
  letter-spacing: 0.04em;
}

.pp-text h2 {
  margin: 20px 0 0;
  font-family: "Quicksand", sans-serif;
  font-size: clamp(30px, 4.2vw, 46px);
  font-weight: 800;
  letter-spacing: -0.03em;
  line-height: 1.1;
  color: #0f2a1a;
}

.pp-text h2 em {
  font-style: normal;
  color: #3a6450;
  font-weight: 500;
}

.pp-text > p {
  max-width: 480px;
  margin: 20px 0 0;
  color: #5d6d61;
  font-size: 15px;
  line-height: 1.75;
  font-weight: 500;
}

.pp-btn {
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

.pp-btn:hover {
  transform: translateY(-2px);
  background: #2d5a3d;
}

.pp-btn svg {
  transition: transform 0.2s ease;
}

.pp-btn:hover svg {
  transform: translateX(3px);
}

@media (max-width: 900px) {
  .produk-preview {
    padding: 64px 5%;
  }

  .pp-shell {
    grid-template-columns: 1fr;
    gap: 36px;
    text-align: center;
  }

  .pp-visual {
    order: -1;
  }

  .pp-text > p {
    margin-left: auto;
    margin-right: auto;
  }
}
</style>