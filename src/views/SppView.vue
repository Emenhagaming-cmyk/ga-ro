<script setup>
import { ref, computed, onMounted } from "vue";
import { ChevronLeft, RefreshCw, Wallet, CheckCircle2, Clock, Receipt } from "lucide-vue-next";
import { useAuthSession } from "@/composable/useAuthSession";

const { session, BACKEND } = useAuthSession();

const bills = ref([]);
const loading = ref(true);
const error = ref("");

const fmt = (n) => "Rp" + Math.round(n).toLocaleString("id-ID");

const namaSiswa = computed(() => session.value.name || "Siswa");

const ringkasan = computed(() => {
  let total = 0;
  let terbayar = 0;
  for (const b of bills.value) {
    total += b.nominal;
    terbayar += b.terbayar;
  }
  return { total, terbayar, sisa: Math.max(total - terbayar, 0) };
});

async function loadData() {
  loading.value = true;
  error.value = "";
  try {
    const res = await fetch(`${BACKEND}/spp`, { credentials: "include" });
    if (!res.ok) throw new Error("Gagal memuat tagihan SPP.");
    bills.value = (await res.json()).bills || [];
  } catch (e) {
    error.value = e.message;
  } finally {
    loading.value = false;
  }
}

const periodeLabel = (p) => {
  const [y, m] = String(p).split("-");
  const bulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
  return bulan[Number(m) - 1] + " " + y;
};

function goBack() {
  window.history.back();
}

onMounted(loadData);
</script>

<template>
  <section class="spp-page">
    <header class="spp-topbar">
      <div class="spp-topbar-left">
        <button class="spp-back" @click="goBack" aria-label="Kembali">
          <ChevronLeft :size="18" :stroke-width="2.5" />
        </button>
        <img src="/logo.png" alt="Logo Sekolah" class="spp-logo" />
        <span class="spp-brand">SPP</span>
      </div>
      <button class="spp-refresh" @click="loadData" aria-label="Muat ulang">
        <RefreshCw :size="17" :stroke-width="2" />
      </button>
    </header>

    <main class="spp-main">
      <div class="spp-card">
        <div class="spp-card-head">
          <span class="spp-label">
            <Wallet :size="14" :stroke-width="2.5" />
            Tagihan SPP
          </span>
          <span class="spp-owner">{{ namaSiswa }}</span>
        </div>
        <div class="spp-num" v-if="!loading">
          {{ fmt(ringkasan.sisa) }}
        </div>
        <div class="spp-num spp-num-skeleton" v-else></div>
        <div class="spp-sub" v-if="!loading">
          Sisa tagihan belum dibayar
        </div>
        <div class="spp-meta" v-if="!loading">
          <span>Total <strong>{{ fmt(ringkasan.total) }}</strong></span>
          <span>Terbayar <strong class="ok">{{ fmt(ringkasan.terbayar) }}</strong></span>
        </div>
      </div>

      <div class="spp-list-head">
        <h2>Riwayat Tagihan</h2>
        <span class="spp-count">{{ bills.length }} bulan</span>
      </div>

      <p v-if="error" class="spp-error">{{ error }}</p>

      <div v-if="!loading && bills.length === 0 && !error" class="spp-empty">
        <span class="spp-empty-ico"><Receipt :size="34" :stroke-width="1.6" /></span>
        <p>Belum ada tagihan</p>
        <span>Tagihan SPP bulanan akan tampil di sini.</span>
      </div>

      <div v-else class="spp-list">
        <div v-for="b in bills" :key="b.id" class="spp-item">
          <div class="spp-item-head">
            <span class="spp-period">{{ periodeLabel(b.periode) }}</span>
            <span class="spp-status" :class="b.status">
              <CheckCircle2 v-if="b.status === 'lunas'" :size="13" :stroke-width="2.5" />
              <Clock v-else :size="13" :stroke-width="2.5" />
              {{ b.status === 'lunas' ? 'Lunas' : 'Belum' }}
            </span>
          </div>

          <div class="spp-item-amount">
            <span>Nominal</span>
            <strong>{{ fmt(b.nominal) }}</strong>
          </div>

          <template v-if="b.status !== 'lunas' && b.terbayar > 0">
            <div class="spp-partial">
              <span>Terbayar {{ fmt(b.terbayar) }}</span>
              <span class="spp-sisa">Sisa {{ fmt(b.sisa) }}</span>
            </div>
          </template>

          <div v-if="b.payments && b.payments.length" class="spp-pays">
            <div v-for="p in b.payments" :key="p.id" class="spp-pay">
              <span class="spp-pay-meta">{{ p.metode === 'tunai' ? 'Tunai' : 'Transfer' }}</span>
              <span class="spp-pay-date">{{ new Date(p.paid_at).toLocaleString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}</span>
              <span class="spp-pay-amount">{{ fmt(p.amount) }}</span>
            </div>
          </div>
        </div>
      </div>
    </main>
  </section>
</template>

<style scoped>
* {
  box-sizing: border-box;
}

.spp-page {
  min-height: 100vh;
  min-height: 100dvh;
  background: #f2f4ef;
  color: #1c2a23;
  font-family: "Quicksand", sans-serif;
}

/* ─── Topbar ─── */
.spp-topbar {
  position: sticky;
  top: 0;
  z-index: 100;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 14px max(5%, 32px);
  background: rgba(242, 244, 239, 0.92);
  backdrop-filter: blur(8px);
  border-bottom: 1px solid #e3e8e3;
}

.spp-topbar-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.spp-back {
  width: 40px;
  height: 40px;
  border: 1px solid #e3e8e3;
  border-radius: 50%;
  background: #fff;
  color: #1c2a23;
  display: grid;
  place-items: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.spp-back:hover {
  background: #eef3ee;
}

.spp-logo {
  height: 34px;
  width: auto;
  border-radius: 9px;
  background: #fff;
  border: 1px solid #e3e8e3;
  padding: 3px;
}

.spp-brand {
  font-size: 16px;
  font-weight: 800;
  letter-spacing: -0.02em;
  color: #1c2a23;
}

.spp-refresh {
  width: 40px;
  height: 40px;
  border: 1px solid #e3e8e3;
  border-radius: 50%;
  background: #fff;
  color: #3a6450;
  display: grid;
  place-items: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.spp-refresh:hover {
  background: #eef3ee;
}

/* ─── Main ─── */
.spp-main {
  max-width: 560px;
  margin: 0 auto;
  padding: 28px max(5%, 24px) 64px;
}

/* ─── Card ─── */
.spp-card {
  position: relative;
  border-radius: 22px;
  padding: 26px 26px 20px;
  background: linear-gradient(135deg, #2d5a3d 0%, #3a6450 45%, #4a8a62 100%);
  color: #fff;
  box-shadow: 0 20px 44px rgba(45, 90, 61, 0.32);
  overflow: hidden;
}

.spp-card::before {
  content: "";
  position: absolute;
  width: 220px;
  height: 220px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.08);
  top: -90px;
  right: -60px;
}

.spp-card-head {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
}

.spp-label {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: #d6e8db;
}

.spp-owner {
  font-size: 12px;
  font-weight: 700;
  color: #e0eee5;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.spp-num {
  position: relative;
  margin-top: 20px;
  font-size: clamp(30px, 7vw, 40px);
  font-weight: 800;
  letter-spacing: -0.03em;
  line-height: 1.1;
}

.spp-num-skeleton {
  height: 44px;
  width: 60%;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.18);
  animation: pulse 1.4s ease-in-out infinite;
}

.spp-sub {
  position: relative;
  margin-top: 6px;
  font-size: 13px;
  color: #cfe3d5;
}

.spp-meta {
  position: relative;
  display: flex;
  gap: 18px;
  margin-top: 18px;
  padding-top: 14px;
  border-top: 1px solid rgba(255, 255, 255, 0.16);
  font-size: 12px;
  color: #d6e8db;
}

.spp-meta strong {
  font-size: 13px;
}

.spp-meta .ok {
  color: #d9f2c9;
}

/* ─── List ─── */
.spp-list-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin: 30px 0 14px;
}

.spp-list-head h2 {
  margin: 0;
  font-size: 17px;
  font-weight: 800;
  color: #1c2a23;
}

.spp-count {
  font-size: 12px;
  font-weight: 700;
  color: #8a9890;
}

.spp-error {
  padding: 14px;
  border-radius: 12px;
  background: #fdecec;
  color: #c0444f;
  font-size: 13px;
  font-weight: 600;
}

.spp-empty {
  text-align: center;
  padding: 44px 0;
  background: #fff;
  border: 1.5px dashed #d9e1da;
  border-radius: 18px;
}

.spp-empty-ico {
  color: #9fb2a5;
  display: inline-block;
}

.spp-empty p {
  margin: 10px 0 2px;
  font-weight: 800;
  color: #33443a;
}

.spp-empty span {
  font-size: 13px;
  color: #8a9890;
}

.spp-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.spp-item {
  background: #fff;
  border: 1px solid #ecefec;
  border-radius: 18px;
  padding: 16px 18px;
}

.spp-item-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.spp-period {
  font-size: 15px;
  font-weight: 800;
  color: #1c2a23;
}

.spp-status {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 800;
}

.spp-status.lunas {
  background: #e3f2e4;
  color: #2f7d43;
}

.spp-status.belum {
  background: #fff1df;
  color: #b06a1f;
}

.spp-item-amount {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 12px;
}

.spp-item-amount span {
  font-size: 13px;
  color: #8a9890;
}

.spp-item-amount strong {
  font-size: 15px;
  font-weight: 800;
  color: #1c2a23;
}

.spp-partial {
  display: flex;
  justify-content: space-between;
  margin-top: 8px;
  font-size: 12px;
  font-weight: 600;
  color: #6b7a6e;
}

.spp-sisa {
  color: #c0444f;
}

.spp-pays {
  margin-top: 12px;
  padding-top: 10px;
  border-top: 1px dashed #e3e8e3;
  display: flex;
  flex-direction: column;
  gap: 7px;
}

.spp-pay {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
}

.spp-pay-meta {
  padding: 2px 8px;
  border-radius: 6px;
  background: #eef3ee;
  color: #3a6450;
  font-weight: 700;
}

.spp-pay-date {
  flex: 1;
  color: #9aa79e;
}

.spp-pay-amount {
  font-weight: 800;
  color: #1c2a23;
}

@keyframes pulse {
  0%, 100% { opacity: 0.8; }
  50% { opacity: 0.4; }
}
</style>
