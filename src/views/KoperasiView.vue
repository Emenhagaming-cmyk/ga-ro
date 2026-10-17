<template>
  <section class="koperasi-page">
    <!-- ═══ SHOP ═══ -->
    <div v-if="view === 'shop'">
      <header class="kop-topbar">
        <router-link to="/koperasi-riwayat" class="kop-ghost-btn">
          <History :size="16" :stroke-width="2.2" />
          Riwayat
        </router-link>
        <div class="kop-topbar-right">
          <span class="kop-saldo-chip">
            <Wallet :size="14" :stroke-width="2.4" />
            {{ fmt(saldo) }}
          </span>
          <button class="kop-cart-btn" @click="cartOpen = true" aria-label="Buka keranjang">
            <ShoppingBag :size="18" :stroke-width="2" />
            <span>Keranjang</span>
            <span v-if="cartCount > 0" class="cart-count">{{ cartCount }}</span>
          </button>
        </div>
      </header>

      <div class="kop-header">
        <h1>Koperasi Siswa</h1>
        <p>Belanja kebutuhan sekolah, bayar dari saldo tabungan atau Midtrans, lalu ambil di ruang TU.</p>
      </div>

      <div v-if="!open" class="kop-closed">
        <Clock :size="18" :stroke-width="2.2" />
        <div>
          <strong>Koperasi sedang tutup</strong>
          <span>Jam operasional {{ hours }}. Pemesanan dibuka kembali saat jam buka.</span>
        </div>
      </div>

      <div class="filter-row">
        <div class="filter-pills">
          <button
            v-for="cat in categories"
            :key="cat"
            :class="['pill', { active: activeCategory === cat }]"
            @click="activeCategory = cat"
          >{{ cat }}</button>
        </div>
        <span class="product-count">{{ filteredProducts.length }} Produk</span>
      </div>

      <div v-if="loading" class="kop-skeleton-grid">
        <div v-for="n in 8" :key="n" class="kop-skeleton" />
      </div>

      <div v-else class="produk-grid">
        <article v-for="item in filteredProducts" :key="item.id" class="produk-card" @click="openDetail(item)">
          <div class="produk-image">
            <img v-if="item.image" :src="item.image" :alt="item.name" loading="lazy" class="produk-img" />
            <span v-else class="produk-emoji">{{ item.emoji || '📦' }}</span>
            <span v-if="item.stock === 0" class="produk-habis">Habis</span>
          </div>
          <div class="produk-info">
            <div class="produk-info-left">
              <h3>{{ item.name }}</h3>
              <p>{{ item.description }}</p>
              <span class="produk-price">{{ fmt(item.price) }}</span>
            </div>
            <button
              class="produk-add"
              :disabled="item.stock === 0"
              @click.stop="addToCart(item)"
              :aria-label="'Tambah ' + item.name + ' ke keranjang'"
            >
              <Plus :size="16" :stroke-width="2.5" />
            </button>
          </div>
        </article>
      </div>

      <div v-if="!loading && filteredProducts.length === 0" class="empty-state">
        Tidak ada produk di kategori ini.
      </div>
    </div>

    <!-- ═══ CART DRAWER ═══ -->
    <Transition name="drawer">
      <div v-if="cartOpen" class="drawer-overlay" @click.self="cartOpen = false">
        <aside class="drawer" role="dialog" aria-label="Keranjang belanja">
          <div class="drawer-head">
            <h2>Keranjang <span v-if="cartCount > 0">({{ cartCount }})</span></h2>
            <button class="drawer-close" @click="cartOpen = false" aria-label="Tutup">
              <X :size="20" :stroke-width="2" />
            </button>
          </div>

          <div v-if="cart.length === 0" class="drawer-empty">
            <ShoppingBag :size="36" :stroke-width="1.5" />
            <p>Keranjang masih kosong</p>
            <span>Tambahkan produk untuk mulai belanja</span>
          </div>

          <div v-else class="drawer-body">
            <div v-for="item in cart" :key="item.product_id" class="si">
              <div class="si-visual">
                <img v-if="item.image" :src="item.image" :alt="item.name" class="si-img" />
                <span v-else>{{ item.emoji || '📦' }}</span>
              </div>
              <div class="si-content">
                <h4>{{ item.name }}</h4>
                <span class="si-price">{{ fmt(item.price) }}</span>
              </div>
              <div class="si-actions">
                <div class="qty-control">
                  <button class="qty-btn" @click="updateQty(item.product_id, -1)" aria-label="Kurangi">
                    <Minus :size="14" :stroke-width="2.5" />
                  </button>
                  <span class="qty-num">{{ item.qty }}</span>
                  <button class="qty-btn" @click="updateQty(item.product_id, 1)" :disabled="item.qty >= item.stock" aria-label="Tambah">
                    <Plus :size="14" :stroke-width="2.5" />
                  </button>
                </div>
                <button class="si-del" @click="removeFromCart(item.product_id)" aria-label="Hapus">
                  <Trash2 :size="15" :stroke-width="2" />
                </button>
              </div>
            </div>
          </div>

          <div v-if="cart.length > 0" class="drawer-foot">
            <div class="delivery-row">
              <MapPin :size="14" :stroke-width="2" />
              <span>Ambil di ruang TU / koperasi</span>
              <strong class="delivery-free">Gratis</strong>
            </div>
            <div class="total-row">
              <span>Total</span>
              <strong>{{ fmt(cartTotal) }}</strong>
            </div>
            <button class="kop-btn primary full" :disabled="!open" @click="goCheckout">Lanjut Bayar</button>
          </div>
        </aside>
      </div>
    </Transition>

    <!-- ═══ CHECKOUT ═══ -->
    <div v-if="view === 'checkout'" class="flow-wrap">
      <div class="flow-box">
        <div class="flow-topbar">
          <button class="flow-back" @click="view = 'shop'" aria-label="Kembali">
            <ChevronLeft :size="18" :stroke-width="2" />
          </button>
          <h2>Checkout</h2>
          <div class="flow-spacer"></div>
        </div>

        <div class="checkout-section">
          <div class="section-label">Pesanan Anda</div>
          <div class="order-items">
            <div v-for="item in cart" :key="item.product_id" class="oi">
              <div class="oi-visual">
                <img v-if="item.image" :src="item.image" :alt="item.name" class="si-img" />
                <span v-else>{{ item.emoji || '📦' }}</span>
              </div>
              <div class="oi-info">
                <h4>{{ item.name }}</h4>
                <span class="oi-meta">{{ item.qty }} &times; {{ fmt(item.price) }}</span>
              </div>
              <strong class="oi-total">{{ fmt(item.price * item.qty) }}</strong>
            </div>
          </div>
        </div>

        <div class="checkout-section">
          <div class="section-label">Metode Pembayaran</div>
          <div class="pay-options">
            <button :class="['pay-opt', { selected: payMethod === 'tabungan' }]" @click="payMethod = 'tabungan'">
              <div class="po-icon tabungan-icon"><Wallet :size="22" :stroke-width="1.6" /></div>
              <div class="po-text">
                <h4>Saldo Tabungan</h4>
                <span>Saldo kamu {{ fmt(saldo) }} &middot; bayar instan</span>
              </div>
              <div v-if="payMethod === 'tabungan'" class="po-check"><Check :size="16" :stroke-width="3" /></div>
            </button>
            <button :class="['pay-opt', { selected: payMethod === 'midtrans' }]" @click="payMethod = 'midtrans'">
              <div class="po-icon midtrans-icon"><CreditCard :size="22" :stroke-width="1.6" /></div>
              <div class="po-text">
                <h4>Midtrans</h4>
                <span>QRIS, e-wallet, atau VA bank</span>
              </div>
              <div v-if="payMethod === 'midtrans'" class="po-check"><Check :size="16" :stroke-width="3" /></div>
            </button>
          </div>
          <p v-if="payMethod === 'tabungan' && !canPaySaldo" class="pay-warn">
            Saldo tabungan kurang {{ fmt(cartTotal - saldo) }}. Pilih Midtrans atau top up dulu di menu Tabungan.
          </p>
        </div>

        <div class="checkout-section">
          <div class="summary-row total">
            <span>Total</span>
            <strong>{{ fmt(cartTotal) }}</strong>
          </div>
        </div>

        <div class="checkout-footer">
          <span class="checkout-total-label">Total {{ fmt(cartTotal) }}</span>
          <button
            class="kop-btn primary checkout-btn"
            :disabled="!payMethod || submitBusy || (payMethod === 'tabungan' && !canPaySaldo)"
            @click="submitOrder"
          >
            <span v-if="submitBusy" class="btn-spin"></span>
            {{ submitBusy ? 'Memproses...' : 'Bayar Sekarang' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ═══ MENUNGGU PEMBAYARAN ═══ -->
    <div v-if="view === 'pay'" class="flow-wrap">
      <div class="flow-box flow-center">
        <div class="spin-circle"><Loader2 :size="34" :stroke-width="2.5" class="spin" /></div>
        <h2 class="suc-title">Menunggu Pembayaran</h2>
        <p class="suc-sub">Selesaikan pembayaran lalu tunggu konfirmasi otomatis.</p>
        <p class="pay-kode">Kode pesanan <strong>{{ order?.kode }}</strong></p>
      </div>
    </div>

    <!-- ═══ SUKSES ═══ -->
    <div v-if="view === 'success'" class="flow-wrap">
      <div class="flow-box flow-center">
        <div class="suc-circle"><Check :size="44" :stroke-width="2.5" /></div>
        <h2 class="suc-title">Pesanan Berhasil!</h2>
        <p class="suc-sub">Tunjukkan kode ambil di ruang TU untuk mengambil pesanan.</p>

        <div class="pickup-card">
          <Ticket :size="22" :stroke-width="2" class="pickup-icon" />
          <div>
            <span class="pickup-label">Kode Ambil</span>
            <strong class="pickup-code">{{ order?.pickup_code }}</strong>
          </div>
        </div>

        <div class="suc-card">
          <div class="suc-row">
            <span>No. Pesanan</span>
            <strong>{{ order?.kode }}</strong>
          </div>
          <div class="suc-row">
            <span>Metode</span>
            <strong>{{ metodeLabel(order?.metode) }}</strong>
          </div>
          <div class="suc-row">
            <span>Total Dibayar</span>
            <strong class="suc-amount">{{ fmt(order?.total || 0) }}</strong>
          </div>
        </div>

        <div class="suc-notif">
          <div class="sn-icon"><Store :size="20" :stroke-width="2" /></div>
          <div class="sn-text">
            <strong>Ambil pesanan di Ruang TU / Koperasi</strong>
            <p>Sebutkan kode ambil <strong>{{ order?.pickup_code }}</strong> kepada petugas. Pesanan diserahkan saat jam operasional ({{ hours }}).</p>
          </div>
        </div>

        <div class="suc-actions">
          <router-link to="/koperasi-riwayat" class="kop-btn primary full">Lihat Riwayat</router-link>
          <button class="kop-btn ghost full" @click="backToShop">Kembali Belanja</button>
        </div>
      </div>
    </div>

    <!-- ═══ DETAIL PRODUK ═══ -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="selected" class="modal-overlay" @click.self="closeDetail">
          <div class="modal-content">
            <button class="modal-close" @click="closeDetail">&times;</button>
            <div class="modal-image">
              <img v-if="selected.image" :src="selected.image" :alt="selected.name" class="modal-img" />
              <span v-else class="modal-emoji">{{ selected.emoji || '📦' }}</span>
            </div>
            <div class="modal-body">
              <span class="modal-category">{{ selected.category }}</span>
              <h2>{{ selected.name }}</h2>
              <p class="modal-price">{{ fmt(selected.price) }}</p>
              <p class="modal-desc">{{ selected.description }}</p>
              <div class="modal-stock"><strong>Stok:</strong> {{ selected.stock }} tersedia</div>
              <button class="kop-btn primary full modal-add" :disabled="selected.stock === 0 || !open" @click="addToCart(selected)">
                <Plus :size="16" :stroke-width="2.5" />
                Tambah ke Keranjang
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </section>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import {
  Plus, Minus, X, Trash2, ShoppingBag, ChevronLeft, Check, CreditCard, Wallet,
  History, Clock, MapPin, Ticket, Store, Loader2,
} from "lucide-vue-next";
import { useAuthSession } from "@/composable/useAuthSession";
import { fetchJson } from "@/services/fetchJson";
import { getCsrfToken } from "@/services/csrf";
import { useToast } from "@/composable/useToast";

const { BACKEND } = useAuthSession();
const { showToast } = useToast();

const CART_KEY = "koperasi_cart";
const SNAP_SCRIPT_ID = "midtrans-snap-js";

const view = ref("shop");
const loading = ref(true);
const open = ref(true);
const hours = ref("");
const saldo = ref(0);

const products = ref([]);
const categories = ref(["Semua"]);
const activeCategory = ref("Semua");
const selected = ref(null);

const cart = ref([]);
const cartOpen = ref(false);

const payMethod = ref(null);
const order = ref(null);
const submitBusy = ref(false);
const verifyBusy = ref(false);

const snapToken = ref("");
const snapClientKey = ref("");
const snapScriptUrl = ref("");
const redirectUrl = ref("");
let snapDone = false;

/* ─── Persist cart ─── */
function loadCart() {
  try {
    const raw = localStorage.getItem(CART_KEY);
    cart.value = raw ? JSON.parse(raw) : [];
  } catch {
    cart.value = [];
  }
}
watch(cart, (v) => {
  try { localStorage.setItem(CART_KEY, JSON.stringify(v)); } catch {}
}, { deep: true });

/* ─── Computed ─── */
const fmt = (n) => "Rp" + Math.round(n || 0).toLocaleString("id-ID");
const filteredProducts = computed(() =>
  activeCategory.value === "Semua" ? products.value : products.value.filter((p) => p.category === activeCategory.value)
);
const cartCount = computed(() => cart.value.reduce((s, i) => s + i.qty, 0));
const cartTotal = computed(() => cart.value.reduce((s, i) => s + i.price * i.qty, 0));
const canPaySaldo = computed(() => saldo.value >= cartTotal.value);

function metodeLabel(m) {
  return { tabungan: "Saldo Tabungan", midtrans: "Midtrans" }[m] || "-";
}

/* ─── Cart actions ─── */
function addToCart(product) {
  if (product.stock === 0) return;
  const existing = cart.value.find((i) => i.product_id === product.id);
  if (existing) {
    if (existing.qty < product.stock) {
      existing.qty++;
      showToast("Ditambahkan ke keranjang", "success");
    } else {
      showToast("Stok tidak mencukupi", "error");
    }
  } else {
    cart.value.push({
      product_id: product.id,
      name: product.name,
      price: product.price,
      stock: product.stock,
      image: product.image,
      emoji: product.emoji,
      qty: 1,
    });
    showToast("Ditambahkan ke keranjang", "success");
  }
}
function removeFromCart(id) {
  cart.value = cart.value.filter((i) => i.product_id !== id);
  if (cart.value.length === 0) cartOpen.value = false;
}
function updateQty(id, delta) {
  const item = cart.value.find((i) => i.product_id === id);
  if (!item) return;
  const next = item.qty + delta;
  if (next <= 0) { removeFromCart(id); return; }
  if (next > item.stock) { showToast("Stok tidak mencukupi", "error"); return; }
  item.qty = next;
}

function goCheckout() {
  cartOpen.value = false;
  payMethod.value = null;
  view.value = "checkout";
}
function backToShop() {
  order.value = null;
  payMethod.value = null;
  view.value = "shop";
}

/* ─── Detail modal ─── */
function openDetail(item) {
  selected.value = item;
  document.body.style.overflow = "hidden";
}
function closeDetail() {
  selected.value = null;
  document.body.style.overflow = "";
}

/* ─── Checkout ─── */
async function submitOrder() {
  if (!payMethod.value || submitBusy.value) return;
  if (payMethod.value === "tabungan" && !canPaySaldo.value) return;

  submitBusy.value = true;
  try {
    const token = await getCsrfToken();
    const res = await fetchJson(`${BACKEND}/koperasi/checkout`, {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": token || "" },
      body: JSON.stringify({
        items: cart.value.map((i) => ({ product_id: i.product_id, qty: i.qty })),
      }),
    });
    const d = await res.json().catch(() => ({}));
    if (!res.ok) throw new Error(d.message || "Gagal membuat pesanan.");

    order.value = d.order;

    if (payMethod.value === "tabungan") {
      await payWithTabungan();
    } else {
      await payWithMidtrans();
    }
  } catch (e) {
    showToast(e.message || "Gagal memproses pesanan.", "error");
  } finally {
    submitBusy.value = false;
  }
}

async function payWithTabungan() {
  const token = await getCsrfToken();
  const res = await fetchJson(`${BACKEND}/koperasi/orders/${order.value.kode}/pay-tabungan`, {
    method: "POST",
    headers: { "X-CSRF-TOKEN": token || "" },
  });
  const d = await res.json().catch(() => ({}));
  if (!res.ok) throw new Error(d.message || "Pembayaran saldo gagal.");

  if (typeof d.saldo === "number") saldo.value = d.saldo;
  completeOrder(d.order);
}

async function payWithMidtrans() {
  const token = await getCsrfToken();
  const res = await fetchJson(`${BACKEND}/koperasi/orders/${order.value.kode}/midtrans`, {
    method: "POST",
    headers: { "X-CSRF-TOKEN": token || "" },
  });
  const d = await res.json().catch(() => ({}));
  if (!res.ok) throw new Error(d.message || "Gagal memulai pembayaran Midtrans.");

  snapToken.value = d.snap_token || "";
  snapClientKey.value = d.client_key || "";
  snapScriptUrl.value = d.snap_script_url || "";
  redirectUrl.value = d.redirect_url || "";

  if (!(await openSnap())) {
    // Snap tak bisa dimuat → buka halaman pembayaran di tab baru, lalu polling status.
    if (redirectUrl.value) {
      window.open(redirectUrl.value, "_blank");
      goVerify();
    } else {
      showToast("Pembayaran tidak tersedia.", "error");
    }
  }
}

/* ─── Snap popup (pola sama dengan TabunganView) ─── */
function loadSnapScript() {
  return new Promise((resolve, reject) => {
    if (window.snap?.pay) return resolve();
    if (!snapScriptUrl.value || !snapClientKey.value) return reject(new Error("Snap tidak siap"));

    let s = document.getElementById(SNAP_SCRIPT_ID);
    if (!s) {
      s = document.createElement("script");
      s.id = SNAP_SCRIPT_ID;
      s.src = snapScriptUrl.value;
      s.setAttribute("data-client-key", snapClientKey.value);
      s.onerror = () => reject(new Error("Gagal memuat Midtrans Snap"));
      document.head.appendChild(s);
    }
    s.addEventListener("load", () => (window.snap?.pay ? resolve() : reject(new Error("Snap tidak siap"))), { once: true });
  });
}

async function openSnap() {
  if (!snapToken.value) return false;
  try {
    await loadSnapScript();
  } catch {
    return false;
  }
  snapDone = false;
  window.snap.pay(snapToken.value, {
    onSuccess: () => { snapDone = true; goVerify(); },
    onPending: () => { snapDone = true; goVerify(); },
    onError: () => { snapDone = true; showToast("Pembayaran gagal. Coba lagi.", "error"); view.value = "checkout"; },
    onClose: () => { if (!snapDone) showToast("Pembayaran belum diselesaikan.", "info"); },
  });
  return true;
}

/* ─── Verifikasi status (server tanya Midtrans) ─── */
async function goVerify(tries = 20) {
  if (verifyBusy.value) return;
  verifyBusy.value = true;
  view.value = "pay";

  for (let i = 0; i < tries; i++) {
    await new Promise((r) => setTimeout(r, 1500));
    try {
      const token = await getCsrfToken();
      const res = await fetchJson(`${BACKEND}/koperasi/orders/${order.value.kode}/verify`, {
        method: "POST",
        headers: { "X-CSRF-TOKEN": token || "" },
      });
      if (!res.ok) continue;
      const d = await res.json();
      if (d.order?.status === "dibayar") { completeOrder(d.order); return; }
      if (d.order?.status === "expired" || d.order?.status === "batal") {
        verifyBusy.value = false;
        view.value = "checkout";
        showToast("Pembayaran kedaluwarsa.", "error");
        return;
      }
    } catch {}
  }

  verifyBusy.value = false;
  view.value = "checkout";
  showToast("Status belum terkonfirmasi. Cek lagi di Riwayat.", "info");
}

function completeOrder(o) {
  verifyBusy.value = false;
  order.value = o;
  cart.value = [];
  view.value = "success";
  showToast("Pembayaran berhasil!", "success", 4200);
  loadCatalog();
}

/* ─── Load katalog ─── */
async function loadCatalog() {
  try {
    const res = await fetchJson(`${BACKEND}/koperasi`);
    if (!res.ok) return;
    const d = await res.json();
    products.value = d.products || [];
    const cats = d.categories || [];
    categories.value = ["Semua", ...cats];
    open.value = d.open !== false;
    hours.value = d.hours || "";
    if (typeof d.saldo === "number") saldo.value = d.saldo;
  } catch {
    showToast("Gagal memuat katalog koperasi.", "error");
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  loadCart();
  loadCatalog();
});
</script>

<style scoped>
.koperasi-page {
  min-height: 100vh;
  background: #ffffff;
  color: #111827;
  overflow-x: hidden;
}

/* ─── Topbar ─── */
.kop-topbar {
  position: sticky; top: 0; z-index: 100;
  display: flex; align-items: center; justify-content: space-between; gap: 16px;
  padding: 14px max(5%, 32px);
  background: rgba(255, 255, 255, 0.92);
  backdrop-filter: blur(8px);
  border-bottom: 1px solid #f0f1f3;
}
.kop-topbar-right { display: flex; align-items: center; gap: 10px; }
.kop-ghost-btn {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 9px 16px; border: 1px solid #ececed; border-radius: 999px;
  background: #fff; color: #111827; font-size: 13px; font-weight: 600;
  text-decoration: none; transition: all 0.2s ease;
}
.kop-ghost-btn:hover { background: #f7f7f8; border-color: #d9d9dc; }
.kop-saldo-chip {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 8px 14px; border-radius: 999px;
  background: #eef2ef; color: #1c2a23;
  font-size: 13px; font-weight: 700;
}
.kop-cart-btn {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 10px 18px; border: 1px solid #ececed; border-radius: 999px;
  background: #fff; color: #111827; font-size: 14px; font-weight: 600;
  cursor: pointer; transition: all 0.2s ease;
}
.kop-cart-btn:hover { background: #f7f7f8; border-color: #d9d9dc; }
.cart-count {
  min-width: 20px; height: 20px; padding: 0 6px; border-radius: 999px;
  background: #1c2a23; color: #fff; font-size: 11px; font-weight: 700;
  display: grid; place-items: center;
}

/* ─── Header ─── */
.kop-header { padding: 48px max(5%, 32px) 12px; }
.kop-header h1 {
  margin: 0 0 12px; font-size: clamp(32px, 5vw, 52px); font-weight: 800;
  letter-spacing: -0.03em; line-height: 1.05; color: #111827;
}
.kop-header p { margin: 0; max-width: 560px; font-size: 15px; line-height: 1.7; color: #6b7280; }

/* ─── Closed banner ─── */
.kop-closed {
  display: flex; align-items: center; gap: 12px;
  margin: 20px max(5%, 32px) 0;
  padding: 14px 18px; border-radius: 14px;
  background: #fff8e6; border: 1px solid #f3e2b3; color: #8a6d1f;
}
.kop-closed div { display: flex; flex-direction: column; }
.kop-closed strong { font-size: 14px; }
.kop-closed span { font-size: 13px; }

/* ─── Filters ─── */
.filter-row {
  display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;
  padding: 28px max(5%, 32px) 8px;
}
.filter-pills { display: flex; flex-wrap: wrap; gap: 8px; }
.pill {
  padding: 9px 18px; border: 1px solid #e7e7e9; border-radius: 999px;
  background: #fff; color: #4b5563; font-size: 13px; font-weight: 600;
  cursor: pointer; transition: all 0.2s ease;
}
.pill:hover { border-color: #1c2a23; color: #1c2a23; }
.pill.active { background: #1c2a23; border-color: #1c2a23; color: #fff; }
.product-count { font-size: 13px; color: #9ca3af; font-weight: 500; }

/* ─── Product grid ─── */
.produk-grid {
  display: grid; grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 28px 20px; padding: 20px max(4%, 24px) 80px;
}
.produk-card { cursor: pointer; border-radius: 12px; background: #fff; min-width: 0; }
.produk-image {
  position: relative; aspect-ratio: 4 / 3; background: #f5f6f7; border-radius: 12px;
  display: grid; place-items: center; overflow: hidden; transition: transform 0.3s ease;
}
.produk-card:hover .produk-image { transform: translateY(-3px); }
.produk-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease; }
.produk-card:hover .produk-img { transform: scale(1.05); }
.produk-emoji { font-size: 52px; }
.produk-habis {
  position: absolute; top: 10px; right: 10px; padding: 4px 10px; border-radius: 999px;
  background: rgba(17,24,39,0.8); color: #fff; font-size: 11px; font-weight: 700;
}
.produk-info { padding: 14px 2px 0; display: flex; align-items: center; justify-content: space-between; gap: 10px; }
.produk-info-left { min-width: 0; }
.produk-info h3 {
  margin: 0 0 4px; font-size: 15px; font-weight: 700; color: #111827;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.produk-info p {
  margin: 0 0 8px; font-size: 13px; color: #9ca3af;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.produk-price { font-size: 14px; font-weight: 700; color: #111827; }
.produk-add {
  width: 34px; height: 34px; border: none; border-radius: 50%;
  background: #1c2a23; color: #fff; display: grid; place-items: center;
  cursor: pointer; transition: all 0.2s ease; flex-shrink: 0;
}
.produk-add:hover { background: #2f3d35; transform: scale(1.08); }
.produk-add:disabled { background: #d1d5db; cursor: not-allowed; transform: none; }

/* ─── Skeleton ─── */
.kop-skeleton-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 28px 20px; padding: 20px max(4%, 24px) 80px; }
.kop-skeleton { aspect-ratio: 3 / 4; border-radius: 12px; background: linear-gradient(90deg, #f2f3f4 25%, #e9eaec 37%, #f2f3f4 63%); background-size: 400% 100%; animation: shimmer 1.3s infinite; }
@keyframes shimmer { 0% { background-position: 100% 0; } 100% { background-position: 0 0; } }

.empty-state { text-align: center; padding: 80px 0; color: #9ca3af; }

/* ─── Cart drawer ─── */
.drawer-overlay { position: fixed; inset: 0; z-index: 9000; background: rgba(17,24,39,0.45); display: flex; justify-content: flex-end; }
.drawer {
  width: min(420px, 100%); height: 100%; box-sizing: border-box; background: #fff;
  display: flex; flex-direction: column; padding: 20px 22px;
  box-shadow: -20px 0 60px rgba(17,24,39,0.15);
}
.drawer-head { display: flex; align-items: center; justify-content: space-between; padding-bottom: 16px; border-bottom: 1px solid #f0f1f3; }
.drawer-head h2 { margin: 0; font-size: 17px; font-weight: 800; color: #111827; }
.drawer-close { width: 34px; height: 34px; border: none; border-radius: 50%; background: #f5f6f7; color: #111827; display: grid; place-items: center; cursor: pointer; }
.drawer-close:hover { background: #e9eaec; }
.drawer-body { flex: 1; overflow-y: auto; padding: 16px 0; display: flex; flex-direction: column; gap: 12px; }
.drawer-empty { text-align: center; padding: 60px 0; color: #9ca3af; }
.drawer-empty p { font-weight: 700; color: #6b7280; margin: 12px 0 2px; }
.drawer-empty span { font-size: 13px; }
.drawer-foot { flex-shrink: 0; border-top: 1px solid #f0f1f3; padding: 16px 0 env(safe-area-inset-bottom, 8px); display: flex; flex-direction: column; gap: 12px; }
.si { display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 14px; border: 1px solid #f0f1f3; background: #fafbfc; }
.si-visual { width: 52px; height: 52px; border-radius: 10px; background: #f5f6f7; display: grid; place-items: center; font-size: 24px; overflow: hidden; flex-shrink: 0; }
.si-img { width: 100%; height: 100%; object-fit: cover; }
.si-content { flex: 1; min-width: 0; }
.si-content h4 { margin: 0 0 2px; font-size: 14px; font-weight: 700; color: #111827; }
.si-price { font-size: 12px; color: #6b7280; font-weight: 600; }
.si-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
.qty-control { display: flex; align-items: center; gap: 2px; background: #fff; border: 1px solid #e7e7e9; border-radius: 10px; padding: 3px; }
.qty-btn { width: 26px; height: 26px; border: none; border-radius: 8px; background: transparent; color: #111827; display: grid; place-items: center; cursor: pointer; }
.qty-btn:disabled { opacity: 0.35; cursor: not-allowed; }
.qty-btn:hover:not(:disabled) { background: #f5f6f7; }
.qty-num { min-width: 22px; text-align: center; font-size: 13px; font-weight: 700; color: #111827; }
.si-del { width: 32px; height: 32px; border: none; border-radius: 10px; background: #feebee; color: #c62839; display: grid; place-items: center; cursor: pointer; }
.si-del:hover { background: #fdd7dc; }
.delivery-row { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #6b7280; padding: 10px 12px; background: #fafbfc; border-radius: 12px; }
.delivery-free { margin-left: auto; color: #1c2a23; font-weight: 700; }
.total-row { display: flex; justify-content: space-between; align-items: center; font-size: 15px; }
.total-row strong { font-size: 18px; font-weight: 800; color: #111827; }

.drawer-enter-active, .drawer-leave-active { transition: opacity 0.3s ease; }
.drawer-enter-from, .drawer-leave-to { opacity: 0; }
.drawer-enter-active .drawer, .drawer-leave-active .drawer { transition: transform 0.3s ease; }
.drawer-enter-from .drawer, .drawer-leave-to .drawer { transform: translateX(100%); }

/* ─── Buttons ─── */
.kop-btn {
  display: inline-flex; align-items: center; justify-content: center; gap: 8px;
  padding: 13px 24px; border: none; border-radius: 12px;
  font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.2s ease;
  text-decoration: none;
}
.kop-btn.primary { background: #1c2a23; color: #fff; }
.kop-btn.primary:hover { background: #2f3d35; }
.kop-btn.primary:disabled { background: #d1d5db; cursor: not-allowed; }
.kop-btn.ghost { background: #fff; color: #111827; border: 1px solid #e7e7e9; }
.kop-btn.ghost:hover { background: #f7f7f8; }
.kop-btn.full { width: 100%; min-height: 48px; }

/* ─── Flows ─── */
.flow-wrap { min-height: calc(100vh - 90px); background: #fafbfc; display: flex; justify-content: center; padding: 32px max(5%, 32px); }
.flow-box {
  width: 100%; max-width: 540px; background: #fff; border: 1px solid #f0f1f3;
  border-radius: 16px; box-shadow: 0 8px 30px rgba(17,24,39,0.06);
  padding: 24px 28px; align-self: flex-start;
}
.flow-topbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
.flow-topbar h2 { font-size: 18px; font-weight: 800; color: #111827; margin: 0; }
.flow-back { width: 36px; height: 36px; border: 1px solid #ececed; border-radius: 10px; background: #fff; color: #111827; display: grid; place-items: center; cursor: pointer; }
.flow-back:hover { background: #f7f7f8; }
.flow-spacer { width: 36px; }
.flow-center { text-align: center; }

.checkout-section { margin-bottom: 24px; }
.section-label { font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: #9ca3af; margin-bottom: 12px; }
.order-items { display: flex; flex-direction: column; gap: 10px; }
.oi { display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 14px; background: #fafbfc; border: 1px solid #f0f1f3; }
.oi-visual { width: 48px; height: 48px; border-radius: 10px; background: #f5f6f7; display: grid; place-items: center; font-size: 22px; overflow: hidden; flex-shrink: 0; }
.oi-info { flex: 1; min-width: 0; }
.oi-info h4 { margin: 0 0 2px; font-size: 14px; font-weight: 700; color: #111827; }
.oi-meta { font-size: 12px; color: #9ca3af; }
.oi-total { font-size: 13px; font-weight: 800; color: #111827; }

.pay-options { display: flex; flex-direction: column; gap: 10px; }
.pay-opt { display: flex; align-items: center; gap: 12px; padding: 14px 16px; border: 1.5px solid #ececed; border-radius: 14px; background: #fff; text-align: left; cursor: pointer; transition: all 0.2s ease; }
.pay-opt:hover { border-color: #d1d5db; }
.pay-opt.selected { border-color: #1c2a23; background: #f8faf9; }
.po-icon { width: 42px; height: 42px; border-radius: 12px; display: grid; place-items: center; flex-shrink: 0; }
.tabungan-icon { background: #eef2ef; color: #1c2a23; }
.midtrans-icon { background: #eef1f7; color: #1d4ed8; }
.po-text { flex: 1; }
.po-text h4 { margin: 0 0 2px; font-size: 14px; font-weight: 700; color: #111827; }
.po-text span { font-size: 12px; color: #9ca3af; }
.po-check { width: 22px; height: 22px; border-radius: 50%; background: #1c2a23; color: #fff; display: grid; place-items: center; }
.pay-warn { margin: 12px 0 0; font-size: 12px; color: #b45309; }

.summary-row { display: flex; justify-content: space-between; align-items: center; }
.summary-row.total { border-top: 1px solid #f0f1f3; padding-top: 12px; font-size: 14px; color: #6b7280; }
.summary-row.total strong { font-size: 18px; color: #111827; }

.checkout-footer { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
.checkout-total-label { font-size: 14px; font-weight: 700; color: #111827; }
.checkout-btn { flex: 1; max-width: 220px; }

.btn-spin { width: 16px; height: 16px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.35); border-top-color: #fff; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* ─── Waiting ─── */
.spin-circle { width: 76px; height: 76px; border-radius: 50%; background: #eef2ef; color: #1c2a23; display: grid; place-items: center; margin: 16px auto 20px; }
.spin { animation: spin 1s linear infinite; }
.pay-kode { margin-top: 12px; font-size: 15px; color: #6b7280; }

/* ─── Success ─── */
.suc-circle { width: 76px; height: 76px; border-radius: 50%; background: #e6f6ee; color: #166534; display: grid; place-items: center; margin: 16px auto 20px; }
.suc-title { margin: 0 0 6px; font-size: 22px; font-weight: 800; color: #111827; }
.suc-sub { margin: 0 0 20px; font-size: 14px; color: #6b7280; }
.pickup-card {
  display: flex; align-items: center; justify-content: center; gap: 16px;
  padding: 18px; margin-bottom: 16px; border-radius: 16px;
  background: #1c2a23; color: #fff;
}
.pickup-icon { flex-shrink: 0; }
.pickup-card > div { text-align: left; }
.pickup-label { display: block; font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(255,255,255,0.7); }
.pickup-code { font-size: 30px; font-weight: 800; letter-spacing: 0.14em; font-family: monospace; }
.suc-card { background: #fafbfc; border: 1px solid #f0f1f3; border-radius: 16px; padding: 8px 18px; margin-bottom: 16px; text-align: left; }
.suc-row { display: flex; justify-content: space-between; align-items: center; font-size: 13px; padding: 10px 0; border-bottom: 1px solid #f0f1f3; }
.suc-row:last-child { border-bottom: none; }
.suc-row span { color: #6b7280; }
.suc-amount { color: #166534; font-size: 15px; }
.suc-notif { display: flex; gap: 12px; text-align: left; background: #eef2ef; border-radius: 14px; padding: 14px 16px; margin-bottom: 18px; }
.sn-icon { width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0; background: #fff; color: #1c2a23; display: grid; place-items: center; }
.sn-text strong { font-size: 13px; color: #111827; }
.sn-text p { margin: 4px 0 0; font-size: 12px; color: #4b5563; line-height: 1.6; }
.suc-actions { display: flex; flex-direction: column; gap: 10px; }

/* ─── Detail modal ─── */
.modal-overlay { position: fixed; inset: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 24px; background: rgba(17,24,39,0.5); backdrop-filter: blur(4px); }
.modal-content { position: relative; width: 100%; max-width: 640px; max-height: 85vh; overflow-y: auto; background: #fff; border-radius: 16px; box-shadow: 0 24px 60px rgba(17,24,39,0.2); }
.modal-close { position: absolute; top: 16px; right: 16px; z-index: 10; width: 36px; height: 36px; border-radius: 50%; border: none; background: rgba(255,255,255,0.9); color: #111827; font-size: 22px; cursor: pointer; display: grid; place-items: center; box-shadow: 0 2px 12px rgba(17,24,39,0.2); }
.modal-close:hover { background: #fff; }
.modal-image { width: 100%; aspect-ratio: 16 / 9; background: #f5f6f7; display: grid; place-items: center; border-radius: 16px 16px 0 0; }
.modal-img { width: 100%; height: 100%; object-fit: cover; object-position: center; }
.modal-emoji { font-size: 72px; }
.modal-body { padding: 28px 32px 36px; }
.modal-category { display: inline-flex; padding: 4px 10px; border-radius: 999px; background: #f0f1f3; color: #4b5563; font-size: 11px; font-weight: 700; text-transform: uppercase; }
.modal-body h2 { margin: 14px 0 8px; font-size: 22px; font-weight: 800; color: #111827; }
.modal-price { margin: 0 0 16px; font-size: 18px; font-weight: 800; color: #111827; }
.modal-desc { font-size: 15px; color: #4b5563; line-height: 1.8; }
.modal-stock { margin-top: 16px; padding-top: 16px; border-top: 1px solid #f0f1f3; font-size: 13px; color: #6b7280; }
.modal-add { margin-top: 18px; }
.modal-enter-active, .modal-leave-active { transition: opacity 0.25s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }

@media (max-width: 1024px) {
  .produk-grid, .kop-skeleton-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
@media (max-width: 768px) {
  .kop-header { padding-top: 32px; }
  .produk-grid, .kop-skeleton-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 24px 14px; padding-bottom: 56px; }
  .flow-wrap { padding: 16px; }
  .flow-box { padding: 20px; }
  .checkout-footer { flex-direction: column; }
  .checkout-btn { max-width: 100%; }
}
@media (max-width: 480px) {
  .produk-grid, .kop-skeleton-grid { grid-template-columns: 1fr; }
  .kop-cart-btn span:not(.cart-count) { display: none; }
}
</style>
