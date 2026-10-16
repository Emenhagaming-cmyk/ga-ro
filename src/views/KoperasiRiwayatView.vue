<template>
  <section class="riwayat-page">
    <ToastStack />

    <header class="riwayat-head">
      <div>
        <h1>Riwayat Koperasi</h1>
        <p>Semua pesanan koperasi kamu dan statusnya.</p>
      </div>
      <button class="riwayat-refresh" :disabled="loading" @click="load">
        <RefreshCw :size="16" :stroke-width="2.2" :class="{ spin: loading }" />
        Muat ulang
      </button>
    </header>

    <div v-if="loading && orders.length === 0" class="riwayat-loader">
      <Loader2 :size="28" :stroke-width="2.2" class="spin" />
      Memuat pesanan...
    </div>

    <div v-else-if="orders.length === 0" class="riwayat-empty">
      <ShoppingBag :size="44" :stroke-width="1.5" />
      <h3>Belum ada pesanan</h3>
      <p>Yuk belanja kebutuhan sekolah di koperasi.</p>
      <router-link to="/koperasi" class="riwayat-cta">Buka Koperasi</router-link>
    </div>

    <div v-else class="riwayat-list">
      <article v-for="o in orders" :key="o.kode" class="riwayat-card" :class="`is-${o.status}`">
        <div class="rc-top">
          <div>
            <span class="rc-kode">{{ o.kode }}</span>
            <span class="rc-date">{{ fmtDate(o.created_at) }}</span>
          </div>
          <span class="rc-badge" :class="`rc-badge--${o.status}`">{{ statusLabel(o.status) }}</span>
        </div>

        <ul class="rc-items">
          <li v-for="(it, i) in o.items" :key="i">
            <span>{{ it.name }} <em v-if="it.qty > 1">&times;{{ it.qty }}</em></span>
            <span>{{ fmt((it.price || 0) * (it.qty || 0)) }}</span>
          </li>
        </ul>

        <div class="rc-total">
          <span>Total</span>
          <strong>{{ fmt(o.total) }}</strong>
        </div>

        <div v-if="o.status === 'dibayar' || o.status === 'diambil'" class="rc-pickup">
          <Ticket :size="16" :stroke-width="2.2" />
          <span>Kode ambil: <strong>{{ o.pickup_code }}</strong></span>
          <span class="rc-pickup-note">{{ o.status === 'diambil' ? 'Sudah diambil' : 'Ambil di ruang TU' }}</span>
        </div>

        <div v-if="o.status === 'pending'" class="rc-pending-note">
          <Info :size="15" :stroke-width="2.2" />
          Pesanan belum dibayar. Selesaikan pembayaran agar bisa diambil.
        </div>
      </article>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { RefreshCw, Loader2, ShoppingBag, Ticket, Info } from "lucide-vue-next";
import { useAuthSession } from "@/composable/useAuthSession";
import { fetchJson } from "@/services/fetchJson";
import { useToast } from "@/composable/useToast";
import ToastStack from "@/components/ToastStack.vue";

const { BACKEND } = useAuthSession();
const { showToast } = useToast();

const orders = ref([]);
const loading = ref(false);

const fmt = (n) => "Rp" + Math.round(n || 0).toLocaleString("id-ID");
const fmtDate = (iso) => {
  if (!iso) return "-";
  const d = new Date(iso);
  return d.toLocaleDateString("id-ID", { day: "2-digit", month: "short", year: "numeric" }) +
    " " + d.toLocaleTimeString("id-ID", { hour: "2-digit", minute: "2-digit" });
};
const LABELS = {
  pending: "Menunggu Bayar",
  dibayar: "Siap Diambil",
  diambil: "Sudah Diambil",
  batal: "Dibatalkan",
  expired: "Kedaluwarsa",
};
const statusLabel = (s) => LABELS[s] || s;

async function load() {
  loading.value = true;
  try {
    const res = await fetchJson(`${BACKEND}/koperasi/orders`);
    if (!res.ok) throw new Error("Gagal memuat riwayat.");
    const d = await res.json();
    orders.value = d.orders || [];
  } catch (e) {
    showToast(e.message || "Gagal memuat riwayat.", "error");
  } finally {
    loading.value = false;
  }
}

onMounted(load);
</script>

<style scoped>
.riwayat-page { padding: 32px max(5%, 32px) 80px; max-width: 760px; margin: 0 auto; }
.riwayat-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 28px; }
.riwayat-head h1 { margin: 0 0 6px; font-size: 26px; font-weight: 800; color: #111827; letter-spacing: -0.02em; }
.riwayat-head p { margin: 0; font-size: 14px; color: #6b7280; }
.riwayat-refresh {
  display: inline-flex; align-items: center; gap: 7px; flex-shrink: 0;
  padding: 9px 16px; border: 1px solid #e7e7e9; border-radius: 999px;
  background: #fff; color: #111827; font-size: 13px; font-weight: 600; cursor: pointer;
}
.riwayat-refresh:hover { background: #f7f7f8; }
.riwayat-refresh:disabled { opacity: 0.6; cursor: not-allowed; }
.spin { animation: spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.riwayat-loader { text-align: center; padding: 60px 0; color: #9ca3af; display: flex; flex-direction: column; align-items: center; gap: 12px; }
.riwayat-empty { text-align: center; padding: 60px 20px; color: #9ca3af; }
.riwayat-empty h3 { margin: 16px 0 4px; color: #374151; }
.riwayat-empty p { margin: 0 0 20px; font-size: 14px; }
.riwayat-cta { display: inline-block; padding: 12px 24px; border-radius: 12px; background: #1c2a23; color: #fff; text-decoration: none; font-weight: 700; font-size: 14px; }

.riwayat-list { display: flex; flex-direction: column; gap: 16px; }
.riwayat-card { border: 1px solid #eef0ef; border-radius: 16px; background: #fff; padding: 18px 20px; box-shadow: 0 4px 16px rgba(17,24,39,0.04); }
.rc-top { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
.rc-kode { display: block; font-size: 14px; font-weight: 800; color: #111827; font-family: monospace; }
.rc-date { font-size: 12px; color: #9ca3af; }
.rc-badge { padding: 5px 12px; border-radius: 999px; font-size: 11px; font-weight: 700; white-space: nowrap; }
.rc-badge--pending { background: #fef3c7; color: #b45309; }
.rc-badge--dibayar { background: #e0edfb; color: #1d4ed8; }
.rc-badge--diambil { background: #e6f6ee; color: #166534; }
.rc-badge--batal, .rc-badge--expired { background: #fee2e2; color: #b91c1c; }

.rc-items { list-style: none; margin: 14px 0; padding: 14px 0; border-top: 1px solid #f2f4f3; border-bottom: 1px solid #f2f4f3; display: flex; flex-direction: column; gap: 8px; }
.rc-items li { display: flex; justify-content: space-between; gap: 12px; font-size: 13px; color: #4b5563; }
.rc-items em { color: #9ca3af; font-style: normal; }
.rc-total { display: flex; justify-content: space-between; align-items: center; }
.rc-total span { font-size: 13px; color: #6b7280; }
.rc-total strong { font-size: 17px; font-weight: 800; color: #111827; }

.rc-pickup {
  display: flex; align-items: center; gap: 8px; margin-top: 14px;
  padding: 12px 14px; border-radius: 12px; background: #1c2a23; color: #fff; font-size: 13px;
}
.rc-pickup strong { font-family: monospace; letter-spacing: 0.12em; font-size: 15px; }
.rc-pickup-note { margin-left: auto; font-size: 11px; color: rgba(255,255,255,0.75); }
.rc-pending-note { display: flex; align-items: center; gap: 8px; margin-top: 14px; padding: 12px 14px; border-radius: 12px; background: #fff8e6; color: #8a6d1f; font-size: 12.5px; }

@media (max-width: 640px) {
  .riwayat-head { flex-direction: column; }
}
</style>
