<template>
  <div class="tabungan-root">
    <div class="toast" :class="{ show: toastShow }">{{ toastMsg }}</div>

    <div class="wrap">
      <div class="layout">

        <!-- ═══ AREA SALDO ═══ -->
        <div class="area-saldo">
          <div class="saldo-card">
            <span class="saldo-label"><i class="fas fa-wallet"></i> Saldo Tabungan</span>
            <div class="saldo-num">{{ fmt(displaySaldo) }}</div>
            <div class="saldo-owner">{{ namaSiswa }} · Saldo Tabunganmu</div>
            <div class="barcode">
              <span v-for="i in 36" :key="i" :style="{ width: i % 5 === 0 ? '3px' : i % 3 === 0 ? '2px' : '1px' }"></span>
            </div>
          </div>
        </div>

        <!-- ═══ AREA FLOW ═══ -->
        <section class="flow-card area-flow">

          <!-- VIEW 1 · NOMINAL -->
          <div v-show="view === 'form'">
            <div class="flow-head">
              <h2>Isi Nominal Top Up</h2>
              <span class="secure"><i class="fas fa-shield-halved"></i> MIDTRANS</span>
            </div>
            <div class="steps">
              <span class="step-dot on"></span><span class="step-dot"></span><span class="step-dot"></span>
              <span class="step-label">1 · Nominal</span>
            </div>

            <div class="balance-pill">
              <span>Saldo saat ini</span>
              <strong>{{ fmt(displaySaldo) }}</strong>
            </div>

            <div class="field">
              <label>Nominal Top Up (minimal Rp10.000)</label>
              <div class="amount-wrap" :class="{ invalid: amountInvalid }">
                <span class="amount-prefix">Rp</span>
                <input
                  v-model="amountStr"
                  type="text" inputmode="numeric" placeholder="0"
                  @input="onAmountInput"
                />
              </div>
              <span class="field-error" :class="{ show: amountInvalid }">Minimal top up Rp10.000</span>
            </div>

            <div class="presets">
              <button
                v-for="p in PRESETS" :key="p"
                class="preset" :class="{ active: amount === p }"
                @click="setPreset(p)"
              >{{ fmt(p) }}</button>
            </div>

            <div class="summary-box">
              <div><span>Nominal</span><b>{{ fmt(amount) }}</b></div>
              <div><span>Biaya layanan</span><b class="green">Gratis</b></div>
              <div><span>Total Bayar</span><b>{{ fmt(amount) }}</b></div>
            </div>

            <button class="btn-primary" :disabled="!canSubmit" @click="submitPay">
              {{ submitBusy ? "Memproses…" : "Bayar Sekarang" }}
            </button>
            <p class="btn-hint">{{ canSubmit ? "Lanjut ke halaman pembayaran →" : "Pilih nominal untuk melanjutkan" }}</p>
          </div>

          <!-- VIEW 2 · PEMBAYARAN -->
          <div v-show="view === 'pay'">
            <div class="flow-head"><h2>Selesaikan Pembayaran</h2></div>
            <div class="steps">
              <span class="step-dot on"></span><span class="step-dot on"></span><span class="step-dot"></span>
              <span class="step-label">2 · Bayar</span>
            </div>

            <div class="pay-timer" :class="{ urgent: timerUrgent, expired: timerExpired }">
              <i class="fas fa-stopwatch"></i>
              {{ timerExpired ? "Waktu pembayaran habis" : "Selesaikan dalam" }}
              <span v-if="!timerExpired">{{ timerText }}</span>
            </div>

            <div class="order-meta">
              <span>Order ID <b>{{ orderId }}</b></span>
              <span>Total <b>{{ fmt(amount) }}</b></span>
            </div>

            <div class="pay-panel">
              <div class="ewallet-ico snap"><i class="fas fa-shield-halved"></i></div>
              <h3>Selesaikan pembayaran</h3>
              <p class="pay-hint">Halaman Midtrans terbuka di tab baru: QRIS, Virtual Account, e-wallet, atau kartu. Tutup tab itu setelah bayar — saldo di sini bertambah otomatis.</p>
              <button class="btn-copy" type="button" @click="payAtMidtrans(redirectUrl)">
                <i class="fas fa-arrow-up-right-from-square"></i> Buka Halaman Pembayaran
              </button>
            </div>

            <p class="expired-note" :class="{ show: timerExpired }">⌛ Perpanjang waktu untuk melanjutkan pembayaran.</p>
            <button class="btn-primary" :disabled="timerExpired || verifyBusy" @click="goVerify">
              {{ verifyBusy ? "Memproses…" : "Saya Sudah Bayar" }}
            </button>
            <button v-if="timerExpired" class="btn-ghost warn" @click="restartTimer">Perpanjang Waktu 15 Menit</button>
            <button class="btn-ghost" @click="cancelPay">Batalkan Transaksi</button>

            <div class="secure-note">
              <i class="fas fa-shield-halved"></i>
              Saldo bertambah otomatis setelah pembayaran terverifikasi
            </div>
          </div>

          <!-- VIEW 3 · VERIFIKASI -->
          <div v-show="view === 'verify'">
            <div class="steps">
              <span class="step-dot on"></span><span class="step-dot on"></span><span class="step-dot on"></span>
              <span class="step-label">3 · Selesai</span>
            </div>
            <div class="verify-wrap">
              <div class="spinner"></div>
              <h3>Memverifikasi Pembayaran…</h3>
              <p>Mohon tunggu, jangan tutup halaman ini</p>
            </div>
          </div>

          <!-- VIEW 4 · SUKSES -->
          <div v-show="view === 'success'">
            <div class="steps">
              <span class="step-dot on"></span><span class="step-dot on"></span><span class="step-dot on"></span>
              <span class="step-label">3 · Selesai</span>
            </div>
            <div class="success-wrap">
              <div class="success-ico"><i class="fas fa-check"></i></div>
              <h3>Top Up Berhasil! 🎉</h3>
              <p>Saldo tabungan kamu sudah bertambah</p>
              <div class="success-detail">
                <div><span>Nominal</span><b>+{{ fmt(amount) }}</b></div>
                <div><span>Metode</span><b>{{ paidMethod || "-" }}</b></div>
                <div><span>Order ID</span><b class="small">{{ orderId }}</b></div>
                <div><span>Saldo Baru</span><b class="green">{{ fmt(displaySaldo) }}</b></div>
              </div>
              <div class="btn-row">
                <button class="btn-ghost bordered" @click="resetFlow(true)">Top Up Lagi</button>
                <button class="btn-primary" @click="resetFlow(false)">Selesai</button>
              </div>
            </div>
          </div>

        </section>

        <!-- ═══ AREA RIWAYAT ═══ -->
        <section class="area-riwayat" ref="riwayatArea">
          <div class="riwayat-head">
            <h2>Transaksi Terakhir</h2>
            <span>{{ transaksi.length }} transaksi</span>
          </div>
          <div class="tx-list">
            <div v-for="t in transaksi" :key="t.id" class="tx-item">
              <span class="tx-icon"><i class="fas fa-arrow-down-to-line"></i></span>
              <div class="tx-info">
                <span class="tx-title-row">
                  <span class="tx-title">{{ t.desc }}</span>
                  <span class="tx-amount">+{{ fmt(t.amount) }}</span>
                </span>
                <span class="tx-date">{{ t.method }} · {{ t.time }} · #TRX-{{ String(t.id).padStart(5, "0") }}</span>
                <div class="tx-status">
                  <span class="badge" :class="t.status">{{ t.status === "success" ? "✓ Berhasil" : "✕ Gagal" }}</span>
                </div>
              </div>
            </div>
            <div v-if="transaksi.length === 0" class="tx-empty">
              <span>🌱</span>
              <p>Belum ada transaksi</p>
              <small>Mulai top up pertamamu di samping!</small>
            </div>
          </div>
        </section>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { useAuthSession } from "@/composable/useAuthSession";
import { fetchJson } from "@/services/fetchJson";
import { getCsrfToken } from "@/services/csrf";

const { session, BACKEND } = useAuthSession();

/* ═══════════ KONSTANTA ═══════════ */
const PRESETS = [10000, 25000, 50000, 100000, 250000, 500000];
const PENDING_KEY = "tabungan_pending_order";

/* ═══════════ STATE ═══════════ */
const namaSiswa = computed(() => session.value.name || "Siswa");

const saldo = ref(0);
const displaySaldo = ref(0);           // untuk animasi count-up
const transaksi = ref([]);

const view = ref("form");              // form | pay | verify | success
const amountStr = ref("");
const amount = ref(0);
const orderId = ref("-");
const redirectUrl = ref("");
const paidMethod = ref("");

const submitBusy = ref(false);
const verifyBusy = ref(false);

const toastMsg = ref("");
const toastShow = ref(false);
let toastTimer = null;

const timerLeft = ref(15 * 60);
let timerInt = null;
let saldoRaf = null;

const riwayatArea = ref(null);

/* ═══════════ COMPUTED ═══════════ */
const fmt = (n) => "Rp" + Math.round(n).toLocaleString("id-ID");

const amountInvalid = computed(() => amount.value > 0 && amount.value < 10000);
const canSubmit = computed(() => amount.value >= 10000 && !submitBusy.value);

const timerText = computed(() => {
  const m = String(Math.floor(timerLeft.value / 60)).padStart(2, "0");
  const s = String(timerLeft.value % 60).padStart(2, "0");
  return `${m}:${s}`;
});
const timerUrgent = computed(() => timerLeft.value > 0 && timerLeft.value <= 60);
const timerExpired = computed(() => timerLeft.value <= 0);

/* ═══════════ TOAST ═══════════ */
function showToast(msg) {
  toastMsg.value = msg;
  toastShow.value = true;
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => (toastShow.value = false), 2600);
}

/* ═══════════ SALDO (count-up) ═══════════ */
function setSaldo(v) {
  const from = saldo.value;
  saldo.value = v;
  const t0 = performance.now(), dur = 700;
  if (saldoRaf) cancelAnimationFrame(saldoRaf);
  (function tick(t) {
    const p = Math.min(1, (t - t0) / dur), e = 1 - Math.pow(1 - p, 3);
    displaySaldo.value = Math.round(from + (v - from) * e);
    if (p < 1) saldoRaf = requestAnimationFrame(tick);
  })(t0);
}

/* ═══════════ VIEW 1: NOMINAL ═══════════ */
function onAmountInput() {
  const digits = amountStr.value.replace(/[^\d]/g, "").slice(0, 9);
  amount.value = parseInt(digits, 10) || 0;
  amountStr.value = digits ? Number(digits).toLocaleString("id-ID") : "";
}

function setPreset(p) {
  amountStr.value = p.toLocaleString("id-ID");
  amount.value = p;
}

/* ═══════════ SUBMIT → TRANSAKSI MIDTRANS ═══════════ */
async function submitPay() {
  if (!canSubmit.value) return;
  submitBusy.value = true;
  try {
    const token = await getCsrfToken();
    const res = await fetchJson(`${BACKEND}/tabungan/topup`, {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": token || "" },
      body: JSON.stringify({ amount: amount.value }),
    });
    if (!res.ok) throw new Error("Gagal membuat transaksi top up.");
    const d = await res.json();
    orderId.value = d.order_id;
    amount.value = d.amount;
    redirectUrl.value = d.redirect_url;
    // Disimpan dulu: kalau siswa menutup halaman Midtrans atau menekan "back",
    // order tetap bisa diverifikasi saat buka /tabungan lagi.
    sessionStorage.setItem(PENDING_KEY, JSON.stringify({ orderId: d.order_id, amount: d.amount, at: Date.now() }));
    payAtMidtrans(d.redirect_url);
  } catch (e) {
    showToast(e.message || "Gagal membuat transaksi top up.");
  } finally {
    submitBusy.value = false;
  }
}

function payAtMidtrans(url) {
  if (!url) {
    showToast("URL pembayaran tidak tersedia.");
    return;
  }
  const win = window.open(url, "_blank", "noopener");
  if (!win) {
    showToast("Popup diblokir browser. Izinkan popup untuk membayar.");
    return;
  }
  // Halaman tetap di sini; server terus menanyakan status ke Midtrans sampai lunas.
  goVerify({ blocking: false });
}

/* ═══════════ VIEW 2: PEMBAYARAN ═══════════ */

/* ── Timer 15 menit ── */
function startTimer() {
  stopTimer();
  timerInt = setInterval(() => {
    timerLeft.value--;
    if (timerLeft.value <= 0) {
      stopTimer();
      showToast("Waktu pembayaran habis.");
    }
  }, 1000);
}
function stopTimer() { if (timerInt) { clearInterval(timerInt); timerInt = null; } }
function restartTimer() {
  timerLeft.value = 15 * 60;
  startTimer();
  showToast("Waktu diperpanjang 15 menit.");
}

/* ═══════════ VIEW 3-4: VERIFIKASI & SUKSES ═══════════ */
const POLL_TRIES = 30;
const POLL_TRIES_BACKGROUND = 90;
const POLL_GAP = 2000;

async function goVerify(opts = {}) {
  const blocking = opts.blocking !== false;
  const tries = blocking ? POLL_TRIES : POLL_TRIES_BACKGROUND;
  if (verifyBusy.value) return;
  stopTimer();
  verifyBusy.value = true;
  if (blocking) view.value = "verify";

  // Server yang menanyakan status ke Midtrans (webhook bisa belum sampai, terutama di
  // dev lokal) dan yangcredited saldo. Frontend cuma menunggu jawaban.
  for (let i = 0; i < tries; i++) {
    await new Promise((r) => setTimeout(r, POLL_GAP));
    try {
      const token = await getCsrfToken();
      const res = await fetchJson(`${BACKEND}/tabungan/topup/${orderId.value}/verify`, {
        method: "POST",
        headers: { "X-CSRF-TOKEN": token || "" },
      });
      if (!res.ok) continue;
      const d = await res.json();
      if (typeof d.amount === "number") amount.value = d.amount;
      if (d.status === "paid") return completeFlow(d);
      if (d.status === "failed") {
        verifyBusy.value = false;
        sessionStorage.removeItem(PENDING_KEY);
        view.value = "form";
        showToast("Pembayaran gagal atau kedaluwarsa.");
        loadData();
        return;
      }
    } catch {}
  }

  verifyBusy.value = false;
  // Order yang tak pernah dibayar dibuang dari storage, supaya buka /tabungan lagi
  // tidak langsung terjebak di mode verifikasi.
  sessionStorage.removeItem(PENDING_KEY);
  view.value = blocking ? "pay" : "form";
  if (blocking) startTimer();
  showToast(blocking ? "Status belum terkonfirmasi. Coba cek lagi." : "Transaksi sebelumnya belum terbayar.");
}

function completeFlow(d) {
  verifyBusy.value = false;
  paidMethod.value = d.method || "Midtrans";
  sessionStorage.removeItem(PENDING_KEY);
  view.value = "success";
  showToast("Saldo berhasil ditambahkan 🎉");
  loadData();
}

/* ═══════════ BATALKAN / RESET ═══════════ */
function cancelPay() {
  stopTimer();
  sessionStorage.removeItem(PENDING_KEY);
  view.value = "form";
  showToast("Transaksi dibatalkan.");
}

function resetFlow(scrollRiwayat) {
  stopTimer();
  amount.value = 0;
  amountStr.value = "";
  redirectUrl.value = "";
  paidMethod.value = "";
  sessionStorage.removeItem(PENDING_KEY);
  view.value = "form";
  if (!scrollRiwayat) {
    riwayatArea.value?.scrollIntoView({ behavior: "smooth" });
  } else {
    window.scrollTo({ top: 0, behavior: "smooth" });
  }
}

/* ═══════════ LOAD DATA BACKEND ═══════════ */
async function loadData() {
  try {
    const res = await fetchJson(`${BACKEND}/tabungan`, { credentials: "include" });
    if (!res.ok) return;
    const d = await res.json();
    if (typeof d.saldo === "number") setSaldo(d.saldo);
    transaksi.value = (d.transaksi || []).map((t) => ({
      id: t.id,
      amount: t.amount,
      desc: t.description || (t.type === "setor" ? "Top Up Online" : "Penarikan"),
      method: t.method || "-",
      status: t.status || "success",
      time: t.created_at
        ? new Date(t.created_at).toLocaleString("id-ID", { day: "numeric", month: "short", year: "numeric", hour: "2-digit", minute: "2-digit" })
        : "-",
    }));
  } catch {
    /* biarkan dummy / data terakhir */
  }
}

/* ═══════════ KEMBALI DARI MIDTRANS ═══════════ */
function handleMidtransReturn() {
  const q = new URLSearchParams(window.location.search);
  const mode = q.get("midtrans");
  const order = q.get("order");
  if (q.has("auth") || mode) {
    window.history.replaceState(null, "", window.location.pathname);
  }

  if (mode === "finish" && order) {
    orderId.value = order;
    goVerify({ blocking: true });
    return;
  }

  if (mode === "cancel") {
    sessionStorage.removeItem(PENDING_KEY);
    showToast("Pembayaran dibatalkan.");
    return;
  }

  let saved = null;
  try {
    saved = JSON.parse(sessionStorage.getItem(PENDING_KEY) || "null");
  } catch {}

  // Hanya order yang masih muda (30 menit) yang dilanjutkan; sisanya dibuang.
  if (!saved?.orderId || Date.now() - (saved.at || 0) > 30 * 60 * 1000) {
    sessionStorage.removeItem(PENDING_KEY);
    return;
  }

  orderId.value = saved.orderId;
  amount.value = saved.amount || amount.value;
  goVerify({ blocking: false });
}

/* ═══════════ LIFECYCLE ═══════════ */
onMounted(() => {
  loadData();
  handleMidtransReturn();
});
onBeforeUnmount(() => {
  stopTimer();
  clearTimeout(toastTimer);
  if (saldoRaf) cancelAnimationFrame(saldoRaf);
});
</script>

<style>
@import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css");
@import url("https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700;800&display=swap");

/* ═══════════ ROOT — NYATU DENGAN SIDEBAR ═══════════ */
.tabungan-root {
  font-family: "Quicksand", sans-serif;
  color: var(--ink, #1c2a23);
  -webkit-font-smoothing: antialiased;
  width: 100%;              /* ikuti lebar penuh area konten (tanpa gap kiri-kanan) */
  min-width: 0;
  background: transparent;  /* ikut background aplikasi → serasi dengan sidebar */
}

:root {
  --green-900: #2d5a3d; --green-700: #3a6450; --green-500: #4a8a62; --green-100: #e8f0eb;
  --ink: #1c2a23; --muted: #8a9890; --line: #e3e8e3; --bg: #f2f4ef;
  --red: #c0444f; --red-bg: #fdecec; --amber: #b7791f; --amber-bg: #fff3df;
}

.tabungan-root button { font-family: "Quicksand", sans-serif; cursor: pointer; }
.tabungan-root button:disabled { cursor: not-allowed; }

/* ═══════════ WRAP — FULL FILL, TANPA GAP ═══════════ */
.wrap {
  width: 100%;
  padding: 16px clamp(14px, 2.5vw, 28px) 40px;
}

/* ═══════════ LAYOUT GRID ═══════════ */
.layout {
  display: grid; gap: 14px;
  grid-template-columns: 1fr;
  grid-template-areas: "saldo" "flow" "riwayat";
  align-items: stretch;
}
.area-saldo   { grid-area: saldo; }
.area-flow    { grid-area: flow; }
.area-riwayat { grid-area: riwayat; }

@media (min-width: 900px) {
  .layout {
    grid-template-columns: minmax(320px, 400px) minmax(0, 1fr);
    grid-template-areas:
      "saldo    flow"
      "riwayat  flow";
    gap: 18px;
    align-items: stretch;
  }
}

/* Opsional: layar sangat lebar (≥1600px) → batasi & tengahkan.
   Gap yang muncul SIMETRIS jadi tetap terasa di tengah.
   Hapus blok ini kalau ingin benar-benar full-fill tanpa gap apa pun.
@media (min-width: 1600px) {
  .wrap { max-width: 1440px; margin-inline: auto; }
}
*/

/* ═══════════ KARTU SALDO ═══════════ */
.saldo-card {
  position: relative; border-radius: 22px; padding: 20px 22px 14px;
  background: linear-gradient(135deg, var(--green-900) 0%, var(--green-700) 45%, var(--green-500) 100%);
  color: #fff; box-shadow: 0 20px 44px rgba(45,90,61,.32); overflow: hidden;
}
.saldo-card::before {
  content: ""; position: absolute; width: 220px; height: 220px; border-radius: 50%;
  background: rgba(255,255,255,.08); top: -90px; right: -60px;
}
.saldo-label {
  position: relative; display: inline-flex; align-items: center; gap: 7px;
  font-size: 11px; font-weight: 700; letter-spacing: .05em;
  text-transform: uppercase; color: #d6e8db;
}
.saldo-num {
  position: relative; margin-top: 14px;
  font-size: clamp(30px, 6vw, 36px); font-weight: 800; letter-spacing: -.03em; line-height: 1.1;
}
.saldo-owner {
  position: relative; margin-top: 5px;
  font-size: 12px; font-weight: 600; color: #e0eee5;
  text-transform: uppercase; letter-spacing: .04em;
}
.barcode {
  position: relative; display: flex; justify-content: center; gap: 2px;
  margin-top: 18px; height: 24px; align-items: stretch;
}
.barcode span { background: #fff; opacity: .7; }

/* ═══════════ FLOW CARD ═══════════ */
.flow-card {
  background: #fff; border: 1.5px solid var(--line); border-radius: 22px;
  padding: 18px; box-shadow: 0 8px 30px rgba(28,42,35,.05);
  min-height: 100%; display: flex; flex-direction: column;
}
.flow-head { display: flex; align-items: center; justify-content: space-between; }
.flow-head h2 { font-size: 18px; font-weight: 800; }
.flow-head .secure {
  display: inline-flex; align-items: center; gap: 5px;
  font-size: 10.5px; font-weight: 800; color: var(--green-700);
  background: var(--green-100); padding: 5px 11px; border-radius: 999px;
}
.steps { display: flex; align-items: center; gap: 8px; margin: 14px 0 18px; }
.step-dot { height: 4px; flex: 1; border-radius: 99px; background: var(--line); transition: background .3s; }
.step-dot.on { background: var(--green-700); }
.step-label { font-size: 11px; font-weight: 700; color: var(--muted); white-space: nowrap; }

.balance-pill {
  display: flex; justify-content: space-between; align-items: center;
  padding: 12px 14px; border-radius: 12px; background: #f2f6f2; margin-bottom: 16px;
}
.balance-pill span { font-size: 13px; color: #6b7a6e; font-weight: 600; }
.balance-pill strong { font-size: 15px; font-weight: 800; }

.field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 12px; }
.field > label { font-size: 12px; font-weight: 700; color: #6b7a6e; }
.amount-wrap {
  display: flex; align-items: center;
  border: 1.5px solid #dfe5df; border-radius: 12px; transition: border-color .2s;
}
.amount-wrap:focus-within { border-color: var(--green-700); }
.amount-wrap.invalid { border-color: var(--red); }
.amount-prefix { padding: 0 2px 0 15px; font-weight: 800; color: #6b7a6e; font-size: 15px; }
.amount-wrap input {
  flex: 1; min-width: 0; border: none; outline: none; background: transparent;
  padding: 14px 15px 14px 4px;
  font-family: "Quicksand", sans-serif; font-size: 17px; font-weight: 800; color: var(--ink);
}
.field-error { font-size: 11.5px; font-weight: 700; color: var(--red); display: none; }
.field-error.show { display: block; }

.presets { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 18px; }
.preset {
  padding: 11px 4px; border-radius: 11px;
  border: 1.5px solid #dfe5df; background: #fff;
  font-weight: 800; font-size: 12.5px; color: #33443a; transition: all .15s;
}
.preset.active { border-color: var(--green-700); background: var(--green-100); color: var(--green-700); }

.summary-box { border: 1.5px dashed #cfe0d5; border-radius: 14px; padding: 13px 15px; margin-bottom: 16px; }
.summary-box div {
  display: flex; justify-content: space-between;
  font-size: 13px; font-weight: 600; color: #6b7a6e; margin-bottom: 6px;
}
.summary-box div:last-child { margin-bottom: 0; padding-top: 6px; border-top: 1.5px dashed var(--line); }
.summary-box b { color: var(--ink); }
.summary-box b.green { color: var(--green-700); }

.btn-primary {
  width: 100%; padding: 15px; border: none; border-radius: 13px;
  background: var(--green-700); color: #fff;
  font-size: 15px; font-weight: 800; transition: opacity .2s, transform .1s;
}
.btn-primary:hover:not(:disabled) { opacity: .9; }
.btn-primary:active:not(:disabled) { transform: scale(.985); }
.btn-primary:disabled { opacity: .45; }
.btn-hint { text-align: center; font-size: 11.5px; font-weight: 600; color: var(--muted); margin-top: 9px; }
.btn-ghost {
  width: 100%; padding: 12px; margin-top: 8px;
  border: none; border-radius: 13px; background: transparent;
  color: var(--muted); font-size: 13px; font-weight: 700;
}
.btn-ghost:hover { background: #f2f4f1; }
.btn-ghost.warn { color: var(--amber); font-weight: 800; }
.btn-ghost.bordered { border: 1.5px solid var(--line); }

.secure-note {
  display: flex; align-items: center; justify-content: center; gap: 6px;
  margin-top: 12px; font-size: 11.5px; font-weight: 600; color: #9aa79e;
}

/* ─── Panel pembayaran (Snap popup) ─── */

/* ─── Halaman bayar ─── */
.pay-timer {
  display: flex; align-items: center; justify-content: center; gap: 7px;
  padding: 11px; border-radius: 12px;
  background: var(--amber-bg); color: var(--amber);
  font-size: 13px; font-weight: 800; margin-bottom: 14px;
}
.pay-timer.urgent { animation: blink 1s infinite; }
.pay-timer.expired { background: var(--red-bg); color: var(--red); animation: none; }
@keyframes blink { 50% { opacity: .55; } }
.order-meta {
  display: flex; justify-content: space-between;
  font-size: 12px; font-weight: 600; color: var(--muted);
  background: #f7f9f7; border-radius: 10px; padding: 10px 12px; margin-bottom: 14px;
}
.order-meta b { color: var(--ink); }
.pay-panel {
  border: 1.5px solid var(--line); border-radius: 16px;
  padding: 18px; text-align: center; margin-bottom: 14px;
}
.pay-panel h3 { font-size: 15px; font-weight: 800; margin-bottom: 4px; }
.pay-hint { font-size: 12px; color: var(--muted); font-weight: 500; }
.btn-copy {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 8px 14px; border: none; border-radius: 999px;
  background: var(--green-700); color: #fff; font-size: 12px; font-weight: 800;
}
.ewallet-ico {
  width: 60px; height: 60px; margin: 14px auto 12px; border-radius: 17px;
  display: grid; place-items: center; font-size: 10px; font-weight: 800; color: #fff;
}
.ewallet-ico.snap { background: var(--green-700); }
.expired-note { display: none; text-align: center; font-size: 12.5px; font-weight: 700; color: var(--red); margin-bottom: 10px; }
.expired-note.show { display: block; }

/* ─── Verifikasi & sukses ─── */
.verify-wrap { text-align: center; padding: 30px 0; }
.spinner {
  width: 48px; height: 48px; margin: 0 auto 16px;
  border-radius: 50%; border: 4px solid var(--green-100); border-top-color: var(--green-700);
  animation: spin .8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.verify-wrap h3 { font-size: 16px; font-weight: 800; }
.verify-wrap p { font-size: 12.5px; color: var(--muted); font-weight: 600; margin-top: 6px; }

.success-wrap { text-align: center; padding: 10px 0 4px; }
.success-ico {
  width: 72px; height: 72px; margin: 0 auto 16px; border-radius: 50%;
  background: var(--green-100); color: var(--green-700); font-size: 28px;
  display: grid; place-items: center;
  animation: pop .45s cubic-bezier(.2,1.4,.4,1);
}
@keyframes pop { 0% { transform: scale(.4); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
.success-wrap h3 { font-size: 18px; font-weight: 800; margin-bottom: 6px; }
.success-wrap p { font-size: 13px; color: var(--muted); font-weight: 600; margin-bottom: 16px; }
.success-detail {
  border: 1.5px dashed #cfe0d5; border-radius: 14px;
  padding: 15px; margin-bottom: 16px; text-align: left;
}
.success-detail div {
  display: flex; justify-content: space-between;
  font-size: 13px; font-weight: 600; color: #6b7a6e; margin-bottom: 8px;
}
.success-detail div:last-child { margin-bottom: 0; }
.success-detail b { color: var(--ink); }
.success-detail b.green { color: var(--green-700); }
.success-detail b.small { font-size: 12px; }
.btn-row { display: flex; gap: 10px; }
.btn-row .btn-ghost { margin-top: 0; flex: 1; }
.btn-row .btn-primary { flex: 1.4; }

/* ═══════════ RIWAYAT ═══════════ */
.riwayat-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
.riwayat-head h2 { font-size: 15px; font-weight: 800; }
.riwayat-head span { font-size: 11.5px; font-weight: 700; color: var(--muted); }
.tx-list { display: flex; flex-direction: column; gap: 10px; flex: 1 1 auto; }
.tx-item {
  display: flex; align-items: flex-start; gap: 11px;
  padding: 13px 14px; border-radius: 16px;
  background: #fff; border: 1px solid #ecefec;
}
.tx-icon {
  flex-shrink: 0; width: 36px; height: 36px; border-radius: 12px;
  display: grid; place-items: center; font-size: 14px;
  background: var(--green-100); color: var(--green-700); margin-top: 2px;
}
.tx-info { flex: 1; min-width: 0; }
.tx-title-row { display: flex; align-items: center; gap: 8px; }
.tx-title { flex: 1; min-width: 0; font-size: 13px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.tx-amount { flex-shrink: 0; font-size: 13px; font-weight: 800; color: var(--green-700); }
.tx-date { display: block; margin-top: 2px; font-size: 11px; color: #9aa79e; }
.tx-status { margin-top: 8px; }
.tx-actions { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-top: 8px; }
.badge { flex-shrink: 0; padding: 3px 9px; border-radius: 999px; font-size: 10px; font-weight: 800; white-space: nowrap; }
.badge.pending { background: var(--amber-bg); color: var(--amber); }
.badge.success { background: var(--green-100); color: var(--green-700); }
.badge.failed { background: var(--red-bg); color: var(--red); }
.btn-resume {
  flex-shrink: 0; padding: 7px 14px; border: none; border-radius: 10px;
  background: var(--green-700); color: #fff; font-size: 11.5px; font-weight: 800;
}
.btn-resume:hover { opacity: .88; }
.tx-empty {
  text-align: center; padding: 32px 12px;
  background: #fff; border: 1.5px dashed #d9e1da; border-radius: 16px;
}
.tx-empty span { font-size: 30px; }
.tx-empty p { margin: 8px 0 2px; font-weight: 800; font-size: 13.5px; }
.tx-empty small { font-size: 12px; color: var(--muted); }

/* ═══════════ TOAST ═══════════ */
.toast {
  position: fixed; top: 20px; left: 50%;
  transform: translateX(-50%) translateY(-8px);
  z-index: 3000; opacity: 0; pointer-events: none;
  padding: 12px 22px; border-radius: 13px;
  background: var(--ink); color: #fff;
  font-size: 14px; font-weight: 700;
  box-shadow: 0 14px 34px rgba(28,42,35,.3);
  transition: opacity .25s, transform .25s;
  max-width: 90vw; text-align: center;
}
.toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
</style>