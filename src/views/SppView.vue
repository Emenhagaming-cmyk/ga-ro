<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import {
  Wallet, CheckCircle2, Clock, Receipt, ChevronRight, ArrowLeft,
  ShieldCheck, Check, RefreshCw,
} from "lucide-vue-next";
import { useAuthSession } from "@/composable/useAuthSession";
import { fetchJson } from "@/services/fetchJson";
import { getCsrfToken } from "@/services/csrf";
import { useToast } from "@/composable/useToast";

const { session, BACKEND } = useAuthSession();
const { showToast } = useToast();

/* Data contoh — dipakai kalau backend belum siap / gagal */
const DUMMY_BILLS = [
  { id: 1, periode: "2025-01", nominal: 350000, terbayar: 350000, status: "lunas",
    payments: [{ id: 101, metode: "tunai", paid_at: "2025-01-05T09:00:00", amount: 350000 }] },
  { id: 2, periode: "2025-02", nominal: 350000, terbayar: 200000, status: "belum",
    payments: [{ id: 102, metode: "tabungan", paid_at: "2025-02-08T10:30:00", amount: 200000 }] },
  { id: 3, periode: "2025-03", nominal: 350000, terbayar: 0, status: "belum", payments: [] },
  { id: 4, periode: "2025-04", nominal: 350000, terbayar: 0, status: "belum", payments: [] },
];

const withSisa = (list) =>
  list.map((b) => ({ ...b, sisa: Math.max(b.nominal - (b.terbayar || 0), 0) }));

/* ═══════════ STATE ═══════════ */
const bills = ref([]);
const loading = ref(true);
const error = ref("");
const isDummy = ref(false);
const filter = ref("semua"); // semua | belum | lunas

const displaySisa = ref(0);
let sisaRaf = null;

const saldoTabungan = ref(0);
const saldoBusy = ref(false);

/* Flow pembayaran — hanya via saldo tabungan (tanpa gateway) */
const view = ref("idle"); // idle | amount | success
const activeBill = ref(null);
const amountStr = ref("");
const amount = ref(0);

const flowCard = ref(null);

/* ═══════════ COMPUTED ═══════════ */
const fmt = (n) => "Rp" + Math.round(n).toLocaleString("id-ID");
const namaSiswa = computed(() => session.value.name || "Siswa");

const ringkasan = computed(() => {
  let total = 0, terbayar = 0;
  for (const b of bills.value) {
    total += b.nominal;
    terbayar += b.terbayar || 0;
  }
  return { total, terbayar, sisa: Math.max(total - terbayar, 0) };
});

const progressPct = computed(() =>
  ringkasan.value.total ? Math.min(100, (ringkasan.value.terbayar / ringkasan.value.total) * 100) : 0
);

const filteredBills = computed(() => {
  const list = [...bills.value].sort((a, b) => a.periode.localeCompare(b.periode));
  if (filter.value === "belum") return list.filter((b) => b.status !== "lunas");
  if (filter.value === "lunas") return list.filter((b) => b.status === "lunas");
  return list;
});

/* Tagihan belum lunas paling awal → jadi "bayar cepat" di flow idle */
const nextBill = computed(() =>
  [...bills.value]
    .filter((b) => b.status !== "lunas" && b.sisa > 0)
    .sort((a, b) => a.periode.localeCompare(b.periode))[0] || null
);

const payPresets = computed(() => {
  if (!activeBill.value) return [];
  const s = activeBill.value.sisa;
  const half = Math.floor(s / 2 / 1000) * 1000;
  const quarter = Math.floor(s / 4 / 1000) * 1000;
  const opts = [s, half, quarter].filter((v) => v === s || v >= 1000);
  return [...new Set(opts)];
});

/* Bayar dari saldo tabungan — satu-satunya jalur bayar, tanpa gateway */
const canPaySaldo = computed(() =>
  !saldoBusy.value && !!activeBill.value && amount.value >= 1 && amount.value <= saldoTabungan.value
);
const saldoHint = computed(() => {
  if (!activeBill.value) return "";
  if (saldoTabungan.value <= 0) return "Saldo tabungan kosong — top up dulu di menu Tabungan.";
  if (amount.value > saldoTabungan.value) return `Saldo kurang ${fmt(amount.value - saldoTabungan.value)} — top up dulu di menu Tabungan.`;
  return `Saldo tabungan mencukupi · sisa saldo ${fmt(saldoTabungan.value - amount.value)}`;
});

/* ═══════════ ANIMASI SISA (count-down) ═══════════ */
function animateSisa(to) {
  const from = displaySisa.value;
  const t0 = performance.now(), dur = 700;
  if (sisaRaf) cancelAnimationFrame(sisaRaf);
  (function tick(t) {
    const p = Math.min(1, (t - t0) / dur), e = 1 - Math.pow(1 - p, 3);
    displaySisa.value = Math.round(from + (to - from) * e);
    if (p < 1) sisaRaf = requestAnimationFrame(tick);
  })(t0);
}

/* ═══════════ LOAD DATA ═══════════ */
async function loadData(silent = false) {
  if (!silent) loading.value = true;
  error.value = "";
  try {
    const res = await fetchJson(`${BACKEND}/spp`);
    if (!res.ok) throw new Error("Gagal memuat tagihan SPP.");
    const data = await res.json();
    bills.value = withSisa(data.bills || []);
    if (typeof data.saldo === "number") saldoTabungan.value = data.saldo;
    isDummy.value = false;
  } catch {
    /* Backend belum siap → tampilkan data contoh agar desain tetap bisa dites */
    if (bills.value.length === 0) {
      bills.value = withSisa(DUMMY_BILLS);
      isDummy.value = true;
    }
  } finally {
    loading.value = false;
    animateSisa(ringkasan.value.sisa);
  }
}

const periodeLabel = (p) => {
  const [y, m] = String(p).split("-");
  const bulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
  return (bulan[Number(m) - 1] || m) + " " + y;
};

/* ═══════════ FLOW: MULAI BAYAR ═══════════ */
function startPay(b) {
  if (!b || b.sisa <= 0) return;
  activeBill.value = b;
  amount.value = b.sisa;
  amountStr.value = b.sisa.toLocaleString("id-ID");
  view.value = "amount";
  flowCard.value?.scrollIntoView({ behavior: "smooth", block: "start" });
}

function gotoIdle() {
  view.value = "idle";
  activeBill.value = null;
  amount.value = 0;
  amountStr.value = "";
}

function onAmountInput() {
  if (!activeBill.value) return;
  const digits = amountStr.value.replace(/[^\d]/g, "").slice(0, 9);
  let v = parseInt(digits, 10) || 0;
  if (v > activeBill.value.sisa) v = activeBill.value.sisa; // clamp maksimal sisa
  amount.value = v;
  amountStr.value = digits ? v.toLocaleString("id-ID") : "";
}

function setPreset(v) {
  amount.value = v;
  amountStr.value = v.toLocaleString("id-ID");
}

/* ═══════════ BAYAR DARI SALDO TABUNGAN ═══════════ */
async function paySaldo() {
  if (!canPaySaldo.value || !activeBill.value) return;
  saldoBusy.value = true;
  try {
    const token = await getCsrfToken();
    const res = await fetchJson(`${BACKEND}/spp/pay-tabungan`, {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": token || "" },
      body: JSON.stringify({ bill_id: activeBill.value.id, amount: amount.value }),
    });
    const d = await res.json().catch(() => ({}));
    if (!res.ok) throw new Error(d.message || "Pembayaran dari tabungan gagal.");

    saldoTabungan.value = d.saldo ?? Math.max(saldoTabungan.value - amount.value, 0);
    const b = activeBill.value;
    b.terbayar = (b.terbayar || 0) + amount.value;
    b.sisa = Math.max(b.nominal - b.terbayar, 0);
    if (d.bill?.status === "lunas" || b.sisa === 0) b.status = "lunas";
    b.payments = b.payments || [];
    b.payments.unshift({
      id: Date.now(),
      metode: "tabungan",
      paid_at: new Date().toISOString(),
      amount: amount.value,
    });
    animateSisa(ringkasan.value.sisa);
    view.value = "success";
    showToast(b.sisa === 0 ? "Tagihan dilunasi dari tabungan" : "Pembayaran dari tabungan berhasil", "success", 4200);
    loadData(true);
  } catch (e) {
    showToast(e.message || "Pembayaran dari tabungan gagal.", "error");
  } finally {
    saldoBusy.value = false;
  }
}

function payNext() {
  resetToIdle();
  if (nextBill.value) startPay(nextBill.value);
}

function resetToIdle() {
  activeBill.value = null;
  amount.value = 0;
  amountStr.value = "";
  view.value = "idle";
}

/* ═══════════ LIFECYCLE ═══════════ */
onMounted(() => loadData());
onBeforeUnmount(() => {
  if (sisaRaf) cancelAnimationFrame(sisaRaf);
});
</script>

<template>
  <section class="spp-page">
    <div class="wrap">
      <div class="layout">

        <!-- ═══ AREA RINGKASAN ═══ -->
        <div class="area-summary">
          <div class="spp-card">
            <div class="spp-card-head">
              <span class="spp-label">
                <Wallet :size="14" :stroke-width="2.5" />
                Tagihan SPP
              </span>
              <span class="spp-owner">{{ namaSiswa }}</span>
            </div>

            <div class="spp-num" v-if="!loading">{{ fmt(displaySisa) }}</div>
            <div class="spp-num spp-num-skeleton" v-else></div>

            <div class="spp-sub" v-if="!loading">Sisa tagihan belum dibayar</div>

            <div class="spp-progress" v-if="!loading">
              <div class="spp-progress-fill" :style="{ width: progressPct + '%' }"></div>
            </div>
            <div class="spp-progress-note" v-if="!loading">
              {{ Math.round(progressPct) }}% terbayar
            </div>

            <div class="spp-meta" v-if="!loading">
              <span>Total <strong>{{ fmt(ringkasan.total) }}</strong></span>
              <span>Terbayar <strong class="ok">{{ fmt(ringkasan.terbayar) }}</strong></span>
            </div>
          </div>
        </div>

        <!-- ═══ AREA FLOW PEMBAYARAN ═══ -->
        <section class="flow-card area-flow" ref="flowCard">

          <!-- VIEW · IDLE -->
          <div v-if="view === 'idle'">
            <div class="flow-head">
              <h2>Pembayaran SPP</h2>
              <span class="secure"><ShieldCheck :size="12" :stroke-width="2.5" /> SALDO TABUNGAN</span>
            </div>
            <div class="steps">
              <span class="step-dot on"></span><span class="step-dot"></span>
              <span class="step-label">Siap Bayar</span>
            </div>

            <!-- Ada tagihan belum lunas -->
            <template v-if="nextBill">
              <div class="next-label">Tagihan tertua yang belum lunas</div>
              <div class="next-box">
                <div class="next-top">
                  <span class="next-period">{{ periodeLabel(nextBill.periode) }}</span>
                  <span class="badge belum"><Clock :size="12" :stroke-width="2.5" /> Belum Lunas</span>
                </div>
                <div class="next-rows">
                  <div><span>Nominal</span><b>{{ fmt(nextBill.nominal) }}</b></div>
                  <div v-if="nextBill.terbayar > 0"><span>Sudah terbayar</span><b class="ok">{{ fmt(nextBill.terbayar) }}</b></div>
                  <div class="divider"><span>Sisa tagihan</span><b class="warn">{{ fmt(nextBill.sisa) }}</b></div>
                </div>
                <button class="btn-primary" @click="startPay(nextBill)">
                  Bayar Sekarang <ChevronRight :size="16" :stroke-width="2.5" />
                </button>
              </div>
            </template>

            <!-- Semua lunas -->
            <div v-else class="all-done">
              <span class="all-done-ico"><CheckCircle2 :size="34" :stroke-width="1.8" /></span>
              <h3>Semua Tagihan Lunas! 🎉</h3>
              <p>Kerja bagus, tidak ada tagihan SPP yang tertunggak.</p>
            </div>

            <div class="secure-note">
              <ShieldCheck :size="13" :stroke-width="2.5" />
              Bayar langsung dari saldo tabungan · penuh atau parsial
            </div>
          </div>

          <!-- VIEW · NOMINAL -->
          <div v-else-if="view === 'amount'">
            <button class="btn-back" @click="gotoIdle">
              <ArrowLeft :size="14" :stroke-width="2.5" /> Ganti Tagihan
            </button>
            <div class="flow-head">
              <h2>Bayar SPP — {{ activeBill ? periodeLabel(activeBill.periode) : "" }}</h2>
            </div>
            <div class="steps">
              <span class="step-dot on"></span><span class="step-dot"></span>
              <span class="step-label">1 · Nominal</span>
            </div>

            <div class="balance-pill" v-if="activeBill">
              <span>Sisa tagihan {{ periodeLabel(activeBill.periode) }}</span>
              <strong>{{ fmt(activeBill.sisa) }}</strong>
            </div>

            <div class="field">
              <label>Nominal Bayar</label>
              <div class="amount-wrap">
                <span class="amount-prefix">Rp</span>
                <input
                  v-model="amountStr"
                  type="text" inputmode="numeric" placeholder="0"
                  @input="onAmountInput"
                />
              </div>
            </div>

            <div class="presets">
              <button
                v-for="p in payPresets" :key="p"
                class="preset" :class="{ active: amount === p }"
                @click="setPreset(p)"
              >{{ p === activeBill?.sisa ? "Lunas · " + fmt(p) : fmt(p) }}</button>
            </div>

            <div class="summary-box" v-if="activeBill">
              <div><span>Nominal tagihan</span><b>{{ fmt(activeBill.nominal) }}</b></div>
              <div v-if="activeBill.terbayar > 0"><span>Sudah terbayar</span><b class="ok">{{ fmt(activeBill.terbayar) }}</b></div>
              <div><span>Pembayaran ini</span><b>{{ fmt(amount) }}</b></div>
              <div><span>Sisa setelah bayar</span><b :class="activeBill.sisa - amount <= 0 ? 'ok' : 'warn'">
                {{ activeBill.sisa - amount <= 0 ? "Lunas 🎉" : fmt(activeBill.sisa - amount) }}
              </b></div>
            </div>

            <!-- Pembayaran hanya dari saldo tabungan (tanpa gateway) -->
            <div class="saldo-box">
              <div class="saldo-top">
                <span>Saldo Tabungan</span>
                <strong>{{ fmt(saldoTabungan) }}</strong>
              </div>
              <button
                class="btn-saldo"
                :disabled="!canPaySaldo || saldoBusy"
                @click="paySaldo"
              >
                <Wallet :size="15" :stroke-width="2.5" />
                {{ saldoBusy ? "Memproses…" : "Bayar dari Saldo Tabungan" }}
              </button>
              <p class="saldo-hint" :class="{ warn: amount > saldoTabungan || saldoTabungan <= 0 }">{{ saldoHint }}</p>
            </div>

            <div class="secure-note">
              <ShieldCheck :size="13" :stroke-width="2.5" />
              Saldo langsung terpotong · tagihan diperbarui seketika
            </div>
          </div>

          <!-- VIEW · SUKSES -->
          <div v-else-if="view === 'success' && activeBill">
            <div class="steps">
              <span class="step-dot on"></span><span class="step-dot on"></span>
              <span class="step-label">2 · Selesai</span>
            </div>
            <div class="success-wrap">
              <div class="success-ico"><Check :size="30" :stroke-width="3" /></div>
              <h3>{{ activeBill.sisa === 0 ? "Tagihan Dilunasi! 🎉" : "Pembayaran Diterima!" }}</h3>
              <p>SPP {{ periodeLabel(activeBill.periode) }} telah diperbarui</p>
              <div class="success-detail">
                <div><span>Periode</span><b>{{ periodeLabel(activeBill.periode) }}</b></div>
                <div><span>Pembayaran ini</span><b>{{ fmt(amount) }}</b></div>
                <div><span>Metode</span><b>Saldo Tabungan</b></div>
                <div><span>Sisa saldo tabungan</span><b class="ok">{{ fmt(saldoTabungan) }}</b></div>
                <div><span>Sisa tagihan</span>
                  <b :class="activeBill.sisa === 0 ? 'ok' : 'warn'">
                    {{ activeBill.sisa === 0 ? "Lunas 🎉" : fmt(activeBill.sisa) }}
                  </b>
                </div>
              </div>
              <div class="btn-row">
                <button class="btn-ghost bordered" @click="payNext">Bayar Tagihan Lain</button>
                <button class="btn-primary" @click="resetToIdle">Selesai</button>
              </div>
            </div>
          </div>

        </section>

        <!-- ═══ AREA RIWAYAT TAGIHAN ═══ -->
        <section class="area-bills">
          <div class="bills-head">
            <h2>Daftar Tagihan</h2>
            <div class="bills-head-right">
              <span class="bills-count">{{ filteredBills.length }} bulan</span>
              <button class="refresh-btn" @click="loadData()" aria-label="Muat ulang">
                <RefreshCw :size="14" :stroke-width="2.5" />
              </button>
            </div>
          </div>

          <div class="filter-chips">
            <button class="mchip" :class="{ active: filter === 'semua' }" @click="filter = 'semua'">Semua</button>
            <button class="mchip" :class="{ active: filter === 'belum' }" @click="filter = 'belum'">Belum Lunas</button>
            <button class="mchip" :class="{ active: filter === 'lunas' }" @click="filter = 'lunas'">Lunas</button>
          </div>

          <p v-if="isDummy" class="dummy-note">⚠ Menampilkan data contoh — backend belum terhubung</p>
          <p v-if="error" class="spp-error">{{ error }}</p>

          <div v-if="!loading && filteredBills.length === 0 && !error" class="spp-empty">
            <span class="spp-empty-ico"><Receipt :size="34" :stroke-width="1.6" /></span>
            <p>Tidak ada tagihan</p>
            <span>Tagihan SPP bulanan akan tampil di sini.</span>
          </div>

          <div v-else class="bills-list">
            <div v-for="b in filteredBills" :key="b.id" class="bill-item">
              <div class="bill-head">
                <span class="bill-period">{{ periodeLabel(b.periode) }}</span>
                <span class="spp-status" :class="b.status">
                  <CheckCircle2 v-if="b.status === 'lunas'" :size="13" :stroke-width="2.5" />
                  <Clock v-else :size="13" :stroke-width="2.5" />
                  {{ b.status === 'lunas' ? 'Lunas' : 'Belum' }}
                </span>
              </div>

              <div class="bill-amount-row">
                <div class="bill-amount">
                  <span>Nominal</span>
                  <strong>{{ fmt(b.nominal) }}</strong>
                </div>
                <button v-if="b.status !== 'lunas' && b.sisa > 0" class="btn-pay" @click="startPay(b)">
                  Bayar <ChevronRight :size="13" :stroke-width="2.5" />
                </button>
              </div>

              <div v-if="b.status !== 'lunas' && b.terbayar > 0" class="bill-partial">
                <span>Terbayar {{ fmt(b.terbayar) }}</span>
                <span class="bill-sisa">Sisa {{ fmt(b.sisa) }}</span>
              </div>

              <div v-if="b.payments && b.payments.length" class="bill-pays">
                <div v-for="p in b.payments" :key="p.id" class="bill-pay">
                  <span class="bill-pay-meta">{{ p.metode === 'tunai' ? 'Tunai' : p.metode }}</span>
                  <span class="bill-pay-date">{{ new Date(p.paid_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}</span>
                  <span class="bill-pay-amount">{{ fmt(p.amount) }}</span>
                </div>
              </div>
            </div>
          </div>
        </section>

      </div>
    </div>
  </section>
</template>

<style scoped>
* { box-sizing: border-box; }

.spp-page {
  --green-900: #2d5a3d; --green-700: #3a6450; --green-500: #4a8a62; --green-100: #e8f0eb;
  --ink: #1c2a23; --muted: #8a9890; --line: #e3e8e3; --bg: #f2f4ef;
  --red: #c0444f; --red-bg: #fdecec; --amber: #b7791f; --amber-bg: #fff3df;

  font-family: "Quicksand", sans-serif;
  color: var(--ink);
  -webkit-font-smoothing: antialiased;
  width: 100%;              /* ← full-fill area konten: tanpa gap kiri-kanan */
  min-width: 0;
  background: transparent;  /* ← ikut background aplikasi → serasi dengan sidebar */
}

.spp-page button { font-family: "Quicksand", sans-serif; cursor: pointer; }
.spp-page button:disabled { cursor: not-allowed; }

/* ─── Wrap: full width, tanpa max-width & margin auto ─── */
.wrap { width: 100%; padding: 16px clamp(14px, 2.5vw, 28px) 40px; }

/* ─── Layout grid ─── */
.layout {
  display: grid; gap: 14px;
  grid-template-columns: 1fr;
  grid-template-areas: "summary" "flow" "bills";
  align-items: stretch;
}
.area-summary { grid-area: summary; }
.area-flow    { grid-area: flow; }
.area-bills   { grid-area: bills; }

@media (min-width: 900px) {
  .layout {
    grid-template-columns: minmax(320px, 400px) minmax(0, 1fr);
    grid-template-areas:
      "summary flow"
      "bills   flow";
    gap: 18px;
    align-items: stretch;
  }
}

/* Opsional: layar sangat lebar (≥1600px) — gap simetris agar tetap terasa di tengah.
   Hapus kalau ingin benar-benar full-fill.
@media (min-width: 1600px) {
  .wrap { max-width: 1440px; margin-inline: auto; }
}
*/

/* ─── Kartu Ringkasan ─── */
.spp-card {
  position: relative; border-radius: 22px; padding: 26px 26px 20px;
  background: linear-gradient(135deg, var(--green-900) 0%, var(--green-700) 45%, var(--green-500) 100%);
  color: #fff; box-shadow: 0 20px 44px rgba(45, 90, 61, 0.32); overflow: hidden;
}
.spp-card::before {
  content: ""; position: absolute; width: 220px; height: 220px; border-radius: 50%;
  background: rgba(255, 255, 255, 0.08); top: -90px; right: -60px;
}
.spp-card-head {
  position: relative; display: flex; align-items: center; justify-content: space-between; gap: 10px;
}
.spp-label {
  display: inline-flex; align-items: center; gap: 7px;
  font-size: 12px; font-weight: 700; letter-spacing: 0.05em;
  text-transform: uppercase; color: #d6e8db;
}
.spp-owner {
  font-size: 12px; font-weight: 700; color: #e0eee5;
  text-transform: uppercase; letter-spacing: 0.04em;
}
.spp-num {
  position: relative; margin-top: 20px;
  font-size: clamp(30px, 6vw, 40px); font-weight: 800;
  letter-spacing: -0.03em; line-height: 1.1;
}
.spp-num-skeleton {
  height: 44px; width: 60%; border-radius: 10px;
  background: rgba(255, 255, 255, 0.18); animation: pulse 1.4s ease-in-out infinite;
}
.spp-sub { position: relative; margin-top: 6px; font-size: 13px; color: #cfe3d5; }

.spp-progress {
  position: relative; margin-top: 16px;
  height: 8px; border-radius: 99px; background: rgba(255, 255, 255, 0.18); overflow: hidden;
}
.spp-progress-fill {
  height: 100%; border-radius: 99px;
  background: linear-gradient(90deg, #d9f2c9, #ffffff);
  transition: width .6s cubic-bezier(.2, .9, .4, 1);
}
.spp-progress-note {
  position: relative; margin-top: 7px;
  font-size: 11px; font-weight: 700; color: #d6e8db; letter-spacing: .03em;
}

.spp-meta {
  position: relative; display: flex; gap: 18px; margin-top: 14px;
  padding-top: 14px; border-top: 1px solid rgba(255, 255, 255, 0.16);
  font-size: 12px; color: #d6e8db;
}
.spp-meta strong { font-size: 13px; }
.spp-meta .ok { color: #d9f2c9; }

/* ─── Flow Card ─── */
.flow-card {
  background: #fff; border: 1.5px solid var(--line); border-radius: 22px;
  padding: 18px; box-shadow: 0 8px 30px rgba(28, 42, 35, .05);
  min-height: 100%; display: flex; flex-direction: column;
  scroll-margin-top: 16px;
}
.flow-head { display: flex; align-items: center; justify-content: space-between; }
.flow-head h2 { font-size: 18px; font-weight: 800; margin: 0; }
.flow-head .secure {
  display: inline-flex; align-items: center; gap: 5px;
  font-size: 10.5px; font-weight: 800; color: var(--green-700);
  background: var(--green-100); padding: 5px 11px; border-radius: 999px;
}
.steps { display: flex; align-items: center; gap: 8px; margin: 14px 0 18px; }
.step-dot { height: 4px; flex: 1; border-radius: 99px; background: var(--line); transition: background .3s; }
.step-dot.on { background: var(--green-700); }
.step-label { font-size: 11px; font-weight: 700; color: var(--muted); white-space: nowrap; }

/* Idle: tagihan berikutnya */
.next-label { font-size: 12px; font-weight: 700; color: var(--muted); margin-bottom: 8px; }
.next-box {
  border: 1.5px dashed #cfe0d5; border-radius: 16px; padding: 16px;
}
.next-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
.next-period { font-size: 16px; font-weight: 800; }
.next-rows div {
  display: flex; justify-content: space-between;
  font-size: 13px; font-weight: 600; color: #6b7a6e; margin-bottom: 6px;
}
.next-rows b { color: var(--ink); }
.next-rows b.ok { color: var(--green-700); }
.next-rows b.warn { color: var(--red); }
.next-rows .divider {
  border-top: 1.5px dashed var(--line); padding-top: 8px; margin-top: 4px;
}
.next-box .btn-primary { margin-top: 12px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; }

.all-done { text-align: center; padding: 34px 0 26px; }
.all-done-ico { color: var(--green-700); display: inline-block; margin-bottom: 10px; }
.all-done h3 { font-size: 17px; font-weight: 800; margin: 0 0 4px; }
.all-done p { font-size: 13px; color: var(--muted); font-weight: 600; margin: 0; }

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
.amount-prefix { padding: 0 2px 0 15px; font-weight: 800; color: #6b7a6e; font-size: 15px; }
.amount-wrap input {
  flex: 1; min-width: 0; border: none; outline: none; background: transparent;
  padding: 14px 15px 14px 4px;
  font-family: "Quicksand", sans-serif; font-size: 17px; font-weight: 800; color: var(--ink);
}

.presets { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 18px; }
.preset {
  padding: 11px 4px; border-radius: 11px;
  border: 1.5px solid #dfe5df; background: #fff;
  font-weight: 800; font-size: 12px; color: #33443a; transition: all .15s;
}
.preset.active { border-color: var(--green-700); background: var(--green-100); color: var(--green-700); }

.summary-box { border: 1.5px dashed #cfe0d5; border-radius: 14px; padding: 13px 15px; margin-bottom: 16px; }
.summary-box div {
  display: flex; justify-content: space-between;
  font-size: 13px; font-weight: 600; color: #6b7a6e; margin-bottom: 6px;
}
.summary-box div:last-child { margin-bottom: 0; padding-top: 6px; border-top: 1.5px dashed var(--line); }
.summary-box b { color: var(--ink); }
.summary-box b.ok { color: var(--green-700); }
.summary-box b.warn { color: var(--red); }

/* Panel bayar dari saldo tabungan */
.saldo-box {
  margin-top: 14px; padding: 14px 15px;
  border: 1.5px solid #cfe0d5; border-radius: 14px; background: #f6faf6;
}
.saldo-top {
  display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;
}
.saldo-top span { font-size: 12.5px; font-weight: 700; color: #6b7a6e; }
.saldo-top strong { font-size: 15px; font-weight: 800; color: var(--green-700); }
.btn-saldo {
  width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 7px;
  padding: 13px; border: 1.5px solid var(--green-700); border-radius: 12px;
  background: #fff; color: var(--green-700);
  font-size: 14px; font-weight: 800; transition: all .15s;
}
.btn-saldo:hover:not(:disabled) { background: var(--green-100); }
.btn-saldo:disabled { opacity: .45; }
.saldo-hint { margin: 8px 0 0; font-size: 11.5px; font-weight: 600; color: var(--muted); text-align: center; }
.saldo-hint.warn { color: var(--amber); }

.btn-primary {
  width: 100%; padding: 15px; border: none; border-radius: 13px;
  background: var(--green-700); color: #fff;
  font-size: 15px; font-weight: 800; transition: opacity .2s, transform .1s;
}
.btn-primary:hover:not(:disabled) { opacity: .9; }
.btn-primary:active:not(:disabled) { transform: scale(.985); }
.btn-primary:disabled { opacity: .45; }
.btn-ghost {
  width: 100%; padding: 12px; margin-top: 8px;
  border: none; border-radius: 13px; background: transparent;
  color: var(--muted); font-size: 13px; font-weight: 700;
}
.btn-ghost:hover { background: #f2f4f1; }
.btn-ghost.warn { color: var(--amber); font-weight: 800; }
.btn-ghost.bordered { border: 1.5px solid var(--line); }
.btn-back {
  display: inline-flex; align-items: center; gap: 5px;
  border: none; background: none; color: var(--green-700);
  font-size: 13px; font-weight: 800; padding: 0; margin-bottom: 10px;
}

.secure-note {
  display: flex; align-items: center; justify-content: center; gap: 6px;
  margin-top: 12px; font-size: 11.5px; font-weight: 600; color: #9aa79e;
  text-align: center;
}

/* Chip filter */
.filter-chips {
  display: flex; gap: 8px; overflow-x: auto;
  padding-bottom: 6px; margin-bottom: 14px; scrollbar-width: thin;
}
.mchip {
  flex-shrink: 0; padding: 9px 15px; border-radius: 999px;
  border: 1.5px solid #dfe5df; background: #fff;
  font-weight: 800; font-size: 12.5px; color: #33443a; transition: all .15s;
}
.mchip.active { border-color: var(--green-700); background: var(--green-100); color: var(--green-700); }

/* Verifikasi & sukses */
.success-wrap { text-align: center; padding: 10px 0 4px; }
.success-ico {
  width: 72px; height: 72px; margin: 0 auto 16px; border-radius: 50%;
  background: var(--green-100); color: var(--green-700);
  display: grid; place-items: center;
  animation: pop .45s cubic-bezier(.2, 1.4, .4, 1);
}
@keyframes pop { 0% { transform: scale(.4); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
.success-wrap h3 { font-size: 18px; font-weight: 800; margin: 0 0 6px; }
.success-wrap p { font-size: 13px; color: var(--muted); font-weight: 600; margin: 0 0 16px; }
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
.success-detail b.ok { color: var(--green-700); }
.success-detail b.warn { color: var(--red); }
.success-detail b.small { font-size: 12px; }
.btn-row { display: flex; gap: 10px; }
.btn-row .btn-ghost { margin-top: 0; flex: 1; }
.btn-row .btn-primary { flex: 1.4; }

/* ─── Daftar Tagihan ─── */
.bills-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
.bills-head h2 { margin: 0; font-size: 15px; font-weight: 800; }
.bills-head-right { display: flex; align-items: center; gap: 10px; }
.bills-count { font-size: 11.5px; font-weight: 700; color: var(--muted); }
.refresh-btn {
  width: 30px; height: 30px; border: 1px solid var(--line); border-radius: 50%;
  background: #fff; color: var(--green-700); display: grid; place-items: center;
}
.refresh-btn:hover { background: #eef3ee; }

.dummy-note {
  font-size: 11.5px; font-weight: 700; color: var(--amber);
  background: var(--amber-bg); border-radius: 10px;
  padding: 8px 12px; margin: 0 0 10px;
}
.spp-error {
  padding: 12px 14px; border-radius: 12px;
  background: var(--red-bg); color: var(--red);
  font-size: 13px; font-weight: 600; margin: 0 0 10px;
}
.spp-empty {
  text-align: center; padding: 40px 0;
  background: #fff; border: 1.5px dashed #d9e1da; border-radius: 18px;
}
.spp-empty-ico { color: #9fb2a5; display: inline-block; }
.spp-empty p { margin: 10px 0 2px; font-weight: 800; color: #33443a; }
.spp-empty span:last-child { font-size: 13px; color: var(--muted); }

.bills-list { display: flex; flex-direction: column; gap: 10px; }
.bill-item {
  background: #fff; border: 1px solid #ecefec;
  border-radius: 18px; padding: 14px 16px;
}
.bill-head { display: flex; align-items: center; justify-content: space-between; }
.bill-period { font-size: 15px; font-weight: 800; }
.spp-status {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 4px 10px; border-radius: 999px;
  font-size: 11px; font-weight: 800;
}
.spp-status.lunas { background: #e3f2e4; color: #2f7d43; }
.spp-status.belum { background: var(--amber-bg); color: var(--amber); }

.bill-amount-row {
  display: flex; align-items: center; justify-content: space-between;
  gap: 10px; margin-top: 10px;
}
.bill-amount { display: flex; flex-direction: column; gap: 1px; }
.bill-amount span { font-size: 11.5px; color: var(--muted); font-weight: 600; }
.bill-amount strong { font-size: 15px; font-weight: 800; }
.btn-pay {
  flex-shrink: 0; display: inline-flex; align-items: center; gap: 4px;
  padding: 9px 15px; border: none; border-radius: 10px;
  background: var(--green-700); color: #fff;
  font-size: 12.5px; font-weight: 800;
}
.btn-pay:hover { opacity: .9; }

.bill-partial {
  display: flex; justify-content: space-between;
  margin-top: 8px; font-size: 12px; font-weight: 600; color: #6b7a6e;
}
.bill-sisa { color: var(--red); }

.bill-pays {
  margin-top: 11px; padding-top: 10px; border-top: 1px dashed #e3e8e3;
  display: flex; flex-direction: column; gap: 7px;
}
.bill-pay { display: flex; align-items: center; gap: 8px; font-size: 12px; }
.bill-pay-meta {
  padding: 2px 8px; border-radius: 6px;
  background: #eef3ee; color: var(--green-700); font-weight: 700;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 55%;
}
.bill-pay-date { flex: 1; color: #9aa79e; }
.bill-pay-amount { font-weight: 800; }

@keyframes pulse {
  0%, 100% { opacity: 0.8; }
  50% { opacity: 0.4; }
}
</style>