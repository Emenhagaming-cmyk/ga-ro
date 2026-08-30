<template>
  <section id="spmb" class="spmb">
    <div class="spmb-shell">
      <div class="spmb-text" v-reveal>
        <span class="spmb-kicker">Penerimaan Peserta Didik Baru</span>
        <h2>
          Satu Langkah Menuju<br />
          <em>Masa Depan Digitalmu</em>
        </h2>
        <p>
          Daftar sebagai siswa SMK Bahrul Ulum — jurusan RPL
          dengan kurikulum link &amp; match bersama industri. Gratis biaya
          pendaftaran.
        </p>
        <div class="spmb-stats">
          <div class="stat-block">
            <span class="stat-num">{{ spmbStats.totalRegistered.toLocaleString('id-ID') }}</span>
            <span class="stat-label">Siswa Mendaftar</span>
          </div>
          <div class="stat-block">
            <span class="stat-num">1</span>
            <span class="stat-label">Jurusan</span>
          </div>
          <div class="stat-block">
            <span class="stat-num">1<small> jam</small></span>
            <span class="stat-label">Proses Biaya</span>
          </div>
        </div>
        <div class="spmb-chips">
          <span class="chip">Gelombang 1 · 2027</span>
          <span class="chip">Online &amp; Offline</span>
          <span class="chip chip-accent">Gratis Daftar</span>
        </div>
        <div class="spmb-actions">
          <a :href="spmbTarget()" class="btn-primary" @click.prevent="handleDaftarClick">
            Daftar Sekarang
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
          </a>
          <router-link to="/spmb-info" class="btn-outline" @click.stop="maybeNavigate('/spmb-info')">
            Info &amp; Biaya
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
          </router-link>
        </div>
        <p v-if="isSiswaLoggedIn()" class="spmb-hint">
          Anda sudah memiliki akun siswa — daftarkan melalui dashboard.
        </p>
      </div>

      <div class="spmb-visual" v-reveal="0.15">
        <div class="visual-wrap">
          <img src="/spmb.jpeg" alt="SPMB SMK Bahrul Ulum" loading="lazy" width="560" height="420" class="visual-img" />
          <div class="visual-card">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            <span>Kuota Terbatas<br /><strong>60 siswa / jurusan</strong></span>
          </div>
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
const { session, isSiswaLoggedIn, spmbTarget } = useAuthSession();
const { showToast } = useToast();

const spmbStats = { totalRegistered: 155, deadline: "15 September 2027" };

function maybeNavigate(href) {
  if (window.innerWidth > 900) router.push(href);
}

function handleDaftarClick() {
  if (isSiswaLoggedIn()) {
    showToast("Anda sudah terdaftar sebagai siswa");
    return;
  }
  window.location.href = spmbTarget();
}
</script>

<style scoped>
.spmb {
  --g900: #0f2a1a;
  --g700: #2d5a3d;
  --g600: #3a6450;
  --g400: #6aad80;
  padding: 96px 7%;
  background:
    radial-gradient(circle at 12% 20%, rgba(255, 255, 255, 0.06), transparent 40%),
    linear-gradient(150deg, #1a3a28 0%, #23432f 60%, #2d5a3d 100%);
  color: #fff;
  scroll-margin-top: 90px;
}

.spmb-shell {
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1.05fr 0.95fr;
  gap: 56px;
  align-items: center;
}

.spmb-kicker {
  display: inline-block;
  padding: 8px 16px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.18);
  font-size: 12px;
  font-weight: 800;
  letter-spacing: 0.04em;
  color: #d6e8dc;
}

.spmb-text h2 {
  margin: 20px 0 0;
  font-family: "Quicksand", sans-serif;
  font-size: clamp(30px, 4.2vw, 46px);
  font-weight: 800;
  letter-spacing: -0.03em;
  line-height: 1.1;
  color: #fff;
}

.spmb-text h2 em {
  font-style: normal;
  color: #a9d3b4;
  font-weight: 500;
}

.spmb-text > p {
  max-width: 500px;
  margin: 18px 0 0;
  color: rgba(255, 255, 255, 0.72);
  font-size: 15px;
  line-height: 1.75;
  font-weight: 500;
}

.spmb-stats {
  display: flex;
  gap: 36px;
  margin-top: 28px;
}

.stat-block {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.stat-num {
  font-family: "Quicksand", sans-serif;
  font-size: clamp(26px, 3.4vw, 38px);
  font-weight: 800;
  letter-spacing: -0.03em;
  line-height: 1;
  color: #fff;
  font-variant-numeric: tabular-nums;
}

.stat-num small {
  font-size: 0.55em;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.6);
}

.stat-label {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.55);
}

.spmb-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 26px;
}

.chip {
  padding: 6px 14px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.16);
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.8);
}

.chip-accent {
  background: rgba(106, 173, 128, 0.28);
  border-color: rgba(106, 173, 128, 0.5);
  color: #fff;
}

.spmb-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 14px;
  margin-top: 30px;
}

.btn-primary,
.btn-outline {
  display: inline-flex;
  align-items: center;
  gap: 9px;
  padding: 14px 26px;
  border-radius: 14px;
  font-family: "Quicksand", sans-serif;
  font-size: 14px;
  font-weight: 700;
  text-decoration: none;
  cursor: pointer;
  transition: transform 0.2s ease, background 0.2s ease, border-color 0.2s ease;
}

.btn-primary {
  background: #fff;
  color: #1a3a28;
}

.btn-primary:hover {
  background: #e8f0eb;
  transform: translateY(-2px);
}

.btn-outline {
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: rgba(255, 255, 255, 0.92);
}

.btn-outline:hover {
  background: rgba(255, 255, 255, 0.14);
  border-color: rgba(255, 255, 255, 0.3);
  transform: translateY(-2px);
}

.spmb-hint {
  margin: 14px 0 0 !important;
  font-size: 12px;
  color: rgba(255, 255, 255, 0.5);
  font-style: italic;
}

.spmb-visual {
  position: relative;
}

.visual-wrap {
  position: relative;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 30px 60px rgba(10, 30, 18, 0.45);
}

.visual-img {
  display: block;
  width: 100%;
  aspect-ratio: 4 / 3;
  object-fit: cover;
}

.visual-card {
  position: absolute;
  left: 16px;
  bottom: 16px;
  right: 16px;
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 18px;
  background: rgba(15, 42, 26, 0.72);
  backdrop-filter: blur(20px) saturate(160%);
  -webkit-backdrop-filter: blur(20px) saturate(160%);
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.15);
  border-radius: 16px;
  color: #eaf3ee;
  font-size: 12.5px;
  line-height: 1.45;
}

.visual-card svg {
  flex-shrink: 0;
  color: #a9d3b4;
}

.visual-card strong {
  color: #fff;
}

@media (max-width: 900px) {
  .spmb {
    padding: 64px 5%;
  }

  .spmb-shell {
    grid-template-columns: 1fr;
    gap: 36px;
    text-align: center;
  }

  .spmb-stats {
    justify-content: center;
    gap: 24px;
  }

  .spmb-chips,
  .spmb-actions {
    justify-content: center;
  }

  .spmb-text > p {
    margin-left: auto;
    margin-right: auto;
  }

  .spmb-visual {
    order: -1;
  }
}
</style>