<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { ChevronLeft, ArrowDownToLine, ArrowUpFromLine, X, RefreshCw, Wallet, Sparkles } from "lucide-vue-next";
import { useAuthSession } from "@/composable/useAuthSession";
import { getCsrfToken } from "@/services/csrf";
import { fetchJson } from "@/services/fetchJson";

const { session, BACKEND } = useAuthSession();

const saldo = ref(0);
const transaksi = ref([]);
const loading = ref(true);
const error = ref("");
const toast = ref({ show: false, message: "" });

const modalOpen = ref(false);
const modalMode = ref("setor");
const modalAmount = ref("");
const modalDesc = ref("");
const modalBusy = ref(false);

let toastTimer = null;

const fmt = (n) => "Rp" + Math.round(n).toLocaleString("id-ID");

const namaSiswa = computed(() => session.value.name || "Siswa");

async function loadData() {
  loading.value = true;
  error.value = "";
  try {
    const res = await fetchJson(`${BACKEND}/tabungan`);
    if (!res.ok) throw new Error("Gagal memuat data tabungan.");
    const data = await res.json();
    saldo.value = data.saldo || 0;
    transaksi.value = data.transaksi || [];
  } catch (e) {
    error.value = e.message;
  } finally {
    loading.value = false;
  }
}

const showToast = (message) => {
  toast.value = { show: true, message };
  if (toastTimer) clearTimeout(toastTimer);
  toastTimer = setTimeout(() => (toast.value.show = false), 2600);
};

function openModal(mode) {
  modalMode.value = mode;
  modalAmount.value = "";
  modalDesc.value = "";
  modalOpen.value = true;
}

function closeModal() {
  if (modalBusy.value) return;
  modalOpen.value = false;
}

async function submitModal() {
  const amount = parseInt(modalAmount.value.replace(/[^\d]/g, ""), 10);
  if (!amount || amount < 1) {
    showToast("Masukkan nominal yang valid.");
    return;
  }

  modalBusy.value = true;
  try {
    const res = await fetchJson(`${BACKEND}/tabungan`, {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": await getCsrfToken() },
      body: JSON.stringify({
        type: modalMode.value,
        amount,
        description: modalDesc.value.trim() || null,
      }),
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || "Transaksi gagal.");
    showToast(data.message);
    modalBusy.value = false;
    modalOpen.value = false;
    await loadData();
  } catch (e) {
    showToast(e.message);
  } finally {
    modalBusy.value = false;
  }
}

const modalTitle = computed(() => (modalMode.value === "setor" ? "Setor Tabungan" : "Tarik Tabungan"));
const modalBtn = computed(() => (modalMode.value === "setor" ? "Simpan Setoran" : "Tarik Sekarang"));
const maxTarik = computed(() => saldo.value);

function goBack() {
  window.history.back();
}

onMounted(loadData);
onUnmounted(() => {
  if (toastTimer) clearTimeout(toastTimer);
  document.body.style.overflow = "";
});
</script>

<template>
  <section class="tabungan-page">
    <Transition name="fade-up">
      <div v-if="toast.show" class="tab-toast">{{ toast.message }}</div>
    </Transition>

    <header class="tab-topbar">
      <div class="tab-topbar-left">
        <button class="tab-back" @click="goBack" aria-label="Kembali">
          <ChevronLeft :size="18" :stroke-width="2.5" />
        </button>
        <img src="/logo.png" alt="Logo Sekolah" class="tab-logo" />
        <span class="tab-brand">Tabungan Siswa</span>
      </div>
      <button class="tab-refresh" @click="loadData" aria-label="Muat ulang">
        <RefreshCw :size="17" :stroke-width="2" />
      </button>
    </header>

    <main class="tab-main">
      <!-- ═══ Kartu Saldo ═══ -->
      <div class="saldo-card">
        <div class="saldo-card-head">
          <span class="saldo-label">
            <Wallet :size="14" :stroke-width="2.5" />
            Saldo Tabungan
          </span>
          <span class="saldo-chip">
            <Sparkles :size="13" :stroke-width="2.5" />
            Membangun Masa Depan
          </span>
        </div>
        <div class="saldo-num" v-if="!loading">
          {{ fmt(saldo) }}
        </div>
        <div class="saldo-num saldo-num-skeleton" v-else></div>
        <div class="saldo-owner">{{ namaSiswa }}</div>
        <div class="saldo-barcode">
          <span v-for="i in 36" :key="i" :style="{ width: i % 5 === 0 ? '3px' : '1px' }"></span>
        </div>
      </div>

      <!-- ═══ Aksi ═══ -->
      <div class="tab-actions">
        <button class="tab-action act-setor" @click="openModal('setor')">
          <span class="action-icon"><ArrowDownToLine :size="20" :stroke-width="2.5" /></span>
          <span class="action-label">Setor Uang</span>
          <span class="action-sub">Tambah tabungan</span>
        </button>
        <button class="tab-action act-tarik" @click="openModal('tarik')">
          <span class="action-icon"><ArrowUpFromLine :size="20" :stroke-width="2.5" /></span>
          <span class="action-label">Tarik Uang</span>
          <span class="action-sub">Saldo tersedia {{ fmt(maxTarik) }}</span>
        </button>
      </div>

      <!-- ═══ Riwayat ═══ -->
      <div class="riwayat">
        <div class="riwayat-head">
          <h2>Riwayat Transaksi</h2>
          <span class="riwayat-count">{{ transaksi.length }} transaksi</span>
        </div>

        <p v-if="error" class="riwayat-error">{{ error }}</p>

        <div v-if="!loading && transaksi.length === 0 && !error" class="riwayat-empty">
          <span class="empty-emoji">🌱</span>
          <p>Belum ada transaksi</p>
          <span>Mulai setor tabungan pertamamu sekarang!</span>
        </div>

        <div v-else class="riwayat-list">
          <div v-for="t in transaksi" :key="t.id" class="riwayat-item">
            <span class="rw-icon" :class="t.type">
              <ArrowDownToLine v-if="t.type === 'setor'" :size="16" :stroke-width="2.5" />
              <ArrowUpFromLine v-else :size="16" :stroke-width="2.5" />
            </span>
            <div class="rw-info">
              <span class="rw-title">{{ t.description || (t.type === 'setor' ? 'Setor tunai' : 'Penarikan') }}</span>
              <span class="rw-date">{{ new Date(t.created_at).toLocaleString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }}</span>
            </div>
            <span class="rw-amount" :class="t.type">
              {{ t.type === 'setor' ? '+' : '−' }}{{ fmt(t.amount) }}
            </span>
          </div>
        </div>
      </div>
    </main>

    <!-- ═══ Modal setor/tarik ═══ -->
    <Transition name="modal">
      <div v-if="modalOpen" class="modal-overlay" @click.self="closeModal">
        <div class="modal-box">
          <div class="modal-head">
            <h2>{{ modalTitle }}</h2>
            <button class="modal-close" @click="closeModal" aria-label="Tutup">
              <X :size="18" :stroke-width="2.5" />
            </button>
          </div>

          <div class="modal-balance">
            <span>Saldo saat ini</span>
            <strong>{{ fmt(saldo) }}</strong>
          </div>

          <label class="modal-field">
            <span>Nominal (Rp)</span>
            <input
              v-model="modalAmount"
              type="text"
              inputmode="numeric"
              placeholder="Masukkan nominal"
              @input="modalAmount = $event.target.value.replace(/[^\d]/g, '').slice(0, 12)"
            />
          </label>

          <label class="modal-field">
            <span>Keterangan (opsional)</span>
            <input v-model="modalDesc" type="text" maxlength="100" placeholder="cth: Tabungan mingguan" />
          </label>

          <button
            class="modal-submit"
            :class="modalMode"
            :disabled="modalBusy"
            @click="submitModal"
          >
            {{ modalBusy ? "Memproses..." : modalBtn }}
          </button>
        </div>
      </div>
    </Transition>
  </section>
</template>

<style scoped>
* {
  box-sizing: border-box;
}

.tabungan-page {
  min-height: 100vh;
  min-height: 100dvh;
  background: #f2f4ef;
  color: #1c2a23;
  font-family: "Quicksand", sans-serif;
}

/* ─── Topbar ─── */
.tab-topbar {
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

.tab-topbar-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.tab-back {
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

.tab-back:hover {
  background: #eef3ee;
}

.tab-logo {
  height: 34px;
  width: auto;
  border-radius: 9px;
  background: #fff;
  border: 1px solid #e3e8e3;
  padding: 3px;
}

.tab-brand {
  font-size: 16px;
  font-weight: 800;
  letter-spacing: -0.02em;
  color: #1c2a23;
}

.tab-refresh {
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

.tab-refresh:hover {
  background: #eef3ee;
}

/* ─── Main ─── */
.tab-main {
  max-width: 560px;
  margin: 0 auto;
  padding: 28px max(5%, 24px) 64px;
}

/* ─── Kartu Saldo ─── */
.saldo-card {
  position: relative;
  border-radius: 22px;
  padding: 28px 26px 22px;
  background: linear-gradient(135deg, #2d5a3d 0%, #3a6450 45%, #4a8a62 100%);
  color: #fff;
  box-shadow: 0 20px 44px rgba(45, 90, 61, 0.32);
  overflow: hidden;
}

.saldo-card::before {
  content: "";
  position: absolute;
  width: 220px;
  height: 220px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.08);
  top: -90px;
  right: -60px;
}

.saldo-card-head {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
}

.saldo-label {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: #d6e8db;
}

.saldo-chip {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 12px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.16);
  font-size: 11px;
  font-weight: 700;
  color: #eaf4ed;
}

.saldo-num {
  position: relative;
  margin-top: 20px;
  font-size: clamp(32px, 7vw, 42px);
  font-weight: 800;
  letter-spacing: -0.03em;
  line-height: 1.1;
}

.saldo-num-skeleton {
  height: 46px;
  width: 60%;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.18);
  animation: pulse 1.4s ease-in-out infinite;
}

.saldo-owner {
  position: relative;
  margin-top: 8px;
  font-size: 14px;
  font-weight: 600;
  color: #e0eee5;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.saldo-barcode {
  position: relative;
  display: flex;
  justify-content: center;
  gap: 2px;
  margin-top: 22px;
  height: 30px;
  align-items: stretch;
}

.saldo-barcode span {
  background: #fff;
  opacity: 0.7;
}

/* ─── Aksi ─── */
.tab-actions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
  margin-top: 20px;
}

.tab-action {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 4px;
  padding: 18px;
  border-radius: 18px;
  border: 1.5px solid #e3e8e3;
  background: #fff;
  text-align: left;
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  font-family: "Quicksand", sans-serif;
}

.tab-action:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 28px rgba(28, 42, 35, 0.1);
}

.action-icon {
  width: 42px;
  height: 42px;
  border-radius: 13px;
  display: grid;
  place-items: center;
  margin-bottom: 6px;
}

.act-setor .action-icon {
  background: #e8f0eb;
  color: #3a6450;
}

.act-tarik .action-icon {
  background: #fdecec;
  color: #c0444f;
}

.action-label {
  font-size: 14px;
  font-weight: 800;
  color: #1c2a23;
}

.action-sub {
  font-size: 12px;
  font-weight: 500;
  color: #8a9890;
}

/* ─── Riwayat ─── */
.riwayat {
  margin-top: 32px;
}

.riwayat-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 14px;
}

.riwayat-head h2 {
  margin: 0;
  font-size: 17px;
  font-weight: 800;
  color: #1c2a23;
}

.riwayat-count {
  font-size: 12px;
  font-weight: 700;
  color: #8a9890;
}

.riwayat-error {
  padding: 14px;
  border-radius: 12px;
  background: #fdecec;
  color: #c0444f;
  font-size: 13px;
  font-weight: 600;
}

.riwayat-empty {
  text-align: center;
  padding: 44px 0;
  background: #fff;
  border: 1.5px dashed #d9e1da;
  border-radius: 18px;
}

.riwayat-empty .empty-emoji {
  font-size: 40px;
}

.riwayat-empty p {
  margin: 10px 0 2px;
  font-weight: 800;
  color: #33443a;
}

.riwayat-empty span {
  font-size: 13px;
  color: #8a9890;
}

.riwayat-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.riwayat-item {
  display: flex;
  align-items: center;
  gap: 13px;
  padding: 14px 16px;
  border-radius: 16px;
  background: #fff;
  border: 1px solid #ecefec;
}

.rw-icon {
  flex-shrink: 0;
  width: 38px;
  height: 38px;
  border-radius: 12px;
  display: grid;
  place-items: center;
}

.rw-icon.setor {
  background: #e8f0eb;
  color: #3a6450;
}

.rw-icon.tarik {
  background: #fdecec;
  color: #c0444f;
}

.rw-info {
  flex: 1;
  min-width: 0;
}

.rw-title {
  display: block;
  font-size: 14px;
  font-weight: 700;
  color: #1c2a23;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.rw-date {
  display: block;
  margin-top: 2px;
  font-size: 12px;
  color: #9aa79e;
}

.rw-amount {
  flex-shrink: 0;
  font-size: 14px;
  font-weight: 800;
}

.rw-amount.setor {
  color: #3a6450;
}

.rw-amount.tarik {
  color: #c0444f;
}

/* ─── Toast ─── */
.tab-toast {
  position: fixed;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 12000;
  padding: 12px 22px;
  border-radius: 13px;
  background: #1c2a23;
  color: #fff;
  font-size: 14px;
  font-weight: 700;
  box-shadow: 0 14px 34px rgba(28, 42, 35, 0.3);
}

.fade-up-enter-active {
  transition: opacity 0.25s ease, transform 0.25s ease;
}

.fade-up-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.fade-up-enter-from,
.fade-up-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

/* ─── Modal ─── */
.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 11000;
  background: rgba(15, 29, 20, 0.5);
  display: grid;
  place-items: center;
  padding: 20px;
}

.modal-box {
  width: 100%;
  max-width: 420px;
  background: #fff;
  border-radius: 20px;
  padding: 24px;
  box-shadow: 0 24px 60px rgba(15, 29, 20, 0.3);
}

.modal-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
}

.modal-head h2 {
  margin: 0;
  font-size: 18px;
  font-weight: 800;
  color: #1c2a23;
}

.modal-close {
  width: 34px;
  height: 34px;
  border: none;
  border-radius: 50%;
  background: #f2f4f1;
  color: #1c2a23;
  display: grid;
  place-items: center;
  cursor: pointer;
}

.modal-balance {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 14px;
  border-radius: 12px;
  background: #f2f6f2;
  margin-bottom: 16px;
}

.modal-balance span {
  font-size: 13px;
  color: #6b7a6e;
  font-weight: 600;
}

.modal-balance strong {
  font-size: 15px;
  font-weight: 800;
  color: #1c2a23;
}

.modal-field {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 14px;
}

.modal-field span {
  font-size: 12px;
  font-weight: 700;
  color: #6b7a6e;
}

.modal-field input {
  padding: 13px 15px;
  border: 1.5px solid #dfe5df;
  border-radius: 12px;
  font-family: "Quicksand", sans-serif;
  font-size: 15px;
  font-weight: 600;
  color: #1c2a23;
  outline: none;
  transition: border-color 0.2s ease;
}

.modal-field input:focus {
  border-color: #3a6450;
}

.modal-submit {
  width: 100%;
  margin-top: 6px;
  padding: 14px;
  border: none;
  border-radius: 13px;
  color: #fff;
  font-family: "Quicksand", sans-serif;
  font-size: 15px;
  font-weight: 800;
  cursor: pointer;
  transition: opacity 0.2s ease;
}

.modal-submit.setor {
  background: #3a6450;
}

.modal-submit.tarik {
  background: #c0444f;
}

.modal-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.modal-enter-active {
  transition: opacity 0.25s ease;
}

.modal-leave-active {
  transition: opacity 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .modal-box {
  transition: transform 0.25s ease;
  transform: translateY(0);
}

.modal-enter-from .modal-box {
  transform: translateY(18px);
}

@keyframes pulse {
  0%, 100% { opacity: 0.8; }
  50% { opacity: 0.4; }
}

@media (max-width: 520px) {
  .tab-actions {
    grid-template-columns: 1fr;
  }

  .tab-action {
    flex-direction: row;
    align-items: center;
    gap: 14px;
  }

  .action-icon {
    margin-bottom: 0;
  }
}
</style>