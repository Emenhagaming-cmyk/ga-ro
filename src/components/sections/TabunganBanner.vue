<script setup>
import { ref, watch } from "vue";
import { useRouter } from "vue-router";
import { useAuthSession } from "@/composable/useAuthSession";

const router = useRouter();
const { session, isSiswaLoggedIn, BACKEND } = useAuthSession();

const saldo = ref(0);

async function loadSaldo() {
  try {
    const res = await fetch(`${BACKEND}/tabungan`, { credentials: "include" });
    if (!res.ok) throw 0;
    const data = await res.json();
    saldo.value = data.saldo || 0;
  } catch {
    saldo.value = 0;
  }
}

// Re-fetch saat login siswa terdeteksi (didukung session cache instan) & saat kembali dari /tabungan
watch(isSiswaLoggedIn, (yes) => {
  if (yes) loadSaldo();
  else saldo.value = 0;
}, { immediate: true });

// bfcache: back dari /tabungan → landing tidak re-mount, revalidate saldo
window.addEventListener("pageshow", (e) => {
  if (e.persisted) loadSaldo();
});

const goTabungan = () => {
  if (isSiswaLoggedIn()) {
    router.push("/tabungan");
  } else {
    window.location.href = `${BACKEND}/login`;
  }
};
</script>

<template>
  <section id="tabungan" class="tabungan">
    <div class="tabungan-shell">
      <div class="tabungan-text" v-reveal>
        <!-- <span class="tabungan-kicker">✨ Menabung Jadi Seru</span> -->
        <h2>
          Kelola Tabunganmu,<br />
          <em>Tanam Kebaikan Setiap Hari</em>
        </h2>
        <p>
         Tabungan siswa membantu kamu menyimpan uang jajan dengan mudah.
          Lacak saldo, riwayat setoran dan tarik tabunganmu kapan saja semua
          dalam satu genggaman.
        </p>
        <!-- <ul class="tabungan-points">
          <li>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            Setor & tarik mudah tanpa biaya
          </li>
          <li>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            Riwayat transaksi transparan
          </li>
          <li>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            Amankan dari diri sendiri maupun teman
          </li>
        </ul> -->
        <button class="tabungan-btn" @click="goTabungan">
          Buka Tabungan Siswa
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </button>
        <p v-if="!isSiswaLoggedIn()" class="tabungan-hint">
          * Khusus siswa SMK Bahrul Ulum ya
        </p>
      </div>

      <div class="tabungan-visual" v-reveal="0.15">
        <div class="visual-blob"></div>
        <div class="visual-confetti confetti-1">✦</div>
        <div class="visual-confetti confetti-2">✿</div>
        <div class="visual-confetti confetti-3">•</div>
        <div class="visual-coin coin-1">$</div>
        <div class="visual-coin coin-2">$</div>
        <img src="/doodles/plant.png" alt="Ilustrasi tanaman tabungan" class="visual-plant" />
        <div class="visual-tag">
          <span class="visual-tag-num">Saldo Aktif</span>
          <span class="visual-tag-money">{{ "Rp" + Math.round(saldo).toLocaleString("id-ID") }}</span>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
* {
  box-sizing: border-box;
}

.tabungan {
  position: relative;
  padding: 96px 7%;
  background: linear-gradient(160deg, #eaf3ea 0%, #f8fbf7 55%, #f2f7f1 100%);
  overflow: hidden;
  scroll-margin-top: 90px;
}

.tabungan-shell {
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1.05fr 0.95fr;
  gap: 48px;
  align-items: center;
}

/* ─── Teks ─── */
.tabungan-kicker {
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

.tabungan-text h2 {
  margin: 20px 0 0;
  font-family: "Quicksand", sans-serif;
  font-size: clamp(30px, 4.2vw, 46px);
  font-weight: 800;
  letter-spacing: -0.03em;
  line-height: 1.1;
  color: #0f2a1a;
}

.tabungan-text h2 em {
  font-style: normal;
  color: #3a6450;
  font-weight: 500;
}

.tabungan-text p {
  max-width: 520px;
  margin: 20px 0 0;
  color: #5d6d61;
  font-size: 15px;
  line-height: 1.75;
  font-weight: 500;
}

.tabungan-points {
  list-style: none;
  margin: 24px 0 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.tabungan-points li {
  display: flex;
  align-items: center;
  gap: 10px;
  color: #33443a;
  font-size: 14px;
  font-weight: 600;
}

.tabungan-points li svg {
  flex-shrink: 0;
  padding: 3px;
  border-radius: 50%;
  background: #3a6450;
  color: #fff;
  box-sizing: content-box;
}

.tabungan-btn {
  margin-top: 32px;
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

.tabungan-btn:hover {
  transform: translateY(-2px);
  background: #2d5a3d;
}

.tabungan-btn svg {
  transition: transform 0.2s ease;
}

.tabungan-btn:hover svg {
  transform: translateX(3px);
}

.tabungan-hint {
  margin: 14px 0 0;
  font-size: 13px;
  color: #8a9890;
  font-style: italic;
}

/* ─── Visual ─── */
.tabungan-visual {
  position: relative;
  display: grid;
  place-items: center;
  min-height: 380px;
}

.visual-blob {
  position: absolute;
  width: 320px;
  height: 320px;
  border-radius: 48% 52% 60% 40% / 55% 40% 60% 45%;
  background: linear-gradient(135deg, #7db88d 0%, #a9d3b4 100%);
  opacity: 0.55;
  animation: blobfloat 8s ease-in-out infinite;
}

.visual-plant {
  position: relative;
  width: 300px;
  max-width: 78%;
  animation: floaty 5s ease-in-out infinite;
}

.visual-confetti {
  position: absolute;
  color: #3a6450;
  font-size: 26px;
  animation: twinkle 3s ease-in-out infinite;
}

.confetti-1 { top: 12%; left: 12%; }
.confetti-2 { top: 26%; right: 14%; animation-delay: 1s; color: #a9c25a; }
.confetti-3 { bottom: 16%; left: 22%; animation-delay: 0.5s; color: #d4a04a; font-size: 18px; }

.visual-coin {
  position: absolute;
  width: 46px;
  height: 46px;
  border-radius: 50%;
  display: grid;
  place-items: center;
  background: radial-gradient(circle at 30% 30%, #ffe28a, #f0a020);
  border: 3px dashed #fff;
  color: #aa5d00;
  font-weight: 800;
  font-size: 18px;
  box-shadow: 0 8px 20px rgba(240, 160, 32, 0.35);
}

.coin-1 { bottom: 22%; right: 10%; animation: floaty 4s ease-in-out infinite 0.4s; }
.coin-2 { top: 18%; left: 8%; width: 34px; height: 34px; font-size: 14px; animation: floaty 4.5s ease-in-out infinite; }

.visual-tag {
  position: absolute;
  bottom: 6%;
  left: 50%;
  transform: translateX(-50%);
  background: #0f2a1a;
  color: #fff;
  border-radius: 16px;
  padding: 12px 22px;
  text-align: center;
  box-shadow: 0 16px 34px rgba(15, 42, 26, 0.35);
}

.visual-tag-num {
  display: block;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #9cc7b0;
}

.visual-tag-money {
  display: block;
  margin-top: 2px;
  font-size: 22px;
  font-weight: 800;
  letter-spacing: -0.02em;
}

@keyframes floaty {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-12px); }
}

@keyframes blobfloat {
  0%, 100% { transform: translate(0, 0) scale(1); }
  33% { transform: translate(8px, -10px) scale(1.03); }
  66% { transform: translate(-6px, 6px) scale(0.98); }
}

@keyframes twinkle {
  0%, 100% { opacity: 0.5; transform: scale(0.9) rotate(0deg); }
  50% { opacity: 1; transform: scale(1.1) rotate(12deg); }
}

/* ─── Responsive ─── */
@media (max-width: 900px) {
  .tabungan-shell {
    grid-template-columns: 1fr;
    gap: 32px;
    text-align: center;
  }

  .tabungan-points {
    align-items: center;
  }

  .tabungan-text p {
    margin-left: auto;
    margin-right: auto;
  }

  .tabungan-visual {
    min-height: 300px;
    order: -1;
  }

  .visual-plant {
    width: 230px;
  }

  .visual-blob {
    width: 240px;
    height: 240px;
  }
}
</style>