<template>
  <section class="kop">
    <!-- ═══ TOAST ═══ -->
    <Transition name="toast">
      <div v-if="toast.show" :class="['kop-toast', toast.type]">
        <span class="toast-dot"></span>
        <span>{{ toast.message }}</span>
      </div>
    </Transition>

    <!-- ═══ SHOP VIEW ═══ -->
    <div v-if="view === 'shop'" class="kop-shop">
      <div class="kop-topbar">
        <button class="kop-back" @click="goBack" aria-label="Kembali">
          <ChevronLeft :size="20" :stroke-width="2" />
        </button>
        <button class="kop-cart-topbar" @click="drawerOpen = true" aria-label="Keranjang">
          <ShoppingBag :size="20" :stroke-width="2" />
          <span v-if="cartCount > 0" class="cart-badge">{{ cartCount }}</span>
        </button>
      </div>

      <header class="kop-header">
        <span class="kop-eyebrow">Koperasi Sekolah</span>
        <h1>Belanja di Koperasi</h1>
        <p>Kebutuhan siswa tersedia praktis. Pesan, bayar, ambil.</p>
      </header>

      <div class="kop-tabs">
        <button
          v-for="cat in categories"
          :key="cat"
          :class="['kop-tab', { active: activeCat === cat }]"
          @click="activeCat = cat"
        >{{ cat }}</button>
      </div>

      <div class="kop-grid">
        <div v-for="p in filtered" :key="p.id" class="kop-card">
          <div class="card-visual" :style="{ background: p.bgColor }">
            <span class="card-emoji" role="img" :aria-label="p.name">{{ p.emoji }}</span>
            <span v-if="p.isNew" class="card-new">Baru</span>
            <button
              class="card-add"
              :disabled="p.stock === 0"
              @click="addToCart(p)"
              :aria-label="'Tambah ' + p.name"
            >
              <Plus :size="18" :stroke-width="2.5" />
            </button>
          </div>
          <div class="card-body">
            <span class="card-cat">{{ p.category }}</span>
            <h3 class="card-name">{{ p.name }}</h3>
            <p class="card-desc">{{ p.desc }}</p>
            <div class="card-foot">
              <span class="card-price">{{ fmt(p.numPrice) }}</span>
              <span :class="['card-stock', { low: p.stock < 10 && p.stock > 0, out: p.stock === 0 }]">
                {{ p.stock > 0 ? `Stok ${p.stock}` : 'Habis' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <div v-if="filtered.length === 0" class="kop-empty">
        <Package :size="40" :stroke-width="1.5" />
        <p>Belum ada produk di kategori ini.</p>
      </div>

      <div class="kop-info">
        <Info :size="18" :stroke-width="2" class="info-icon" />
        <p><strong>Cara Pesan:</strong> Pilih produk, checkout, bayar via QRIS atau transfer. Pesanan akan disiapkan oleh penjaga koperasi.</p>
      </div>
    </div>

    <!-- ═══ CART DRAWER ═══ -->
    <Transition name="overlay-fade">
      <div v-if="drawerOpen" class="kop-overlay" @click="drawerOpen = false"></div>
    </Transition>
    <Transition name="slide-right">
      <aside v-if="drawerOpen" class="kop-drawer" role="dialog" aria-label="Keranjang belanja">
        <div class="drawer-head">
          <h2>Keranjang <span v-if="cartCount > 0">({{ cartCount }})</span></h2>
          <button class="drawer-close" @click="drawerOpen = false" aria-label="Tutup">
            <X :size="20" :stroke-width="2" />
          </button>
        </div>

        <div v-if="cart.length === 0" class="drawer-empty">
          <ShoppingBag :size="36" :stroke-width="1.5" />
          <p>Keranjang masih kosong</p>
          <span>Tambahkan produk untuk mulai belanja</span>
        </div>

        <div v-else class="drawer-body">
          <div class="drawer-items">
            <div v-for="item in cart" :key="item.product.id" class="di">
              <div class="di-visual" :style="{ background: item.product.bgColor }">
                <span>{{ item.product.emoji }}</span>
              </div>
              <div class="di-content">
                <h4>{{ item.product.name }}</h4>
                <span class="di-price">{{ fmt(item.product.numPrice) }}</span>
              </div>
              <div class="di-actions">
                <div class="qty-control">
                  <button class="qty-btn" @click="updateQty(item.product.id, -1)" aria-label="Kurangi">
                    <Minus :size="14" :stroke-width="2.5" />
                  </button>
                  <span class="qty-num">{{ item.quantity }}</span>
                  <button
                    class="qty-btn"
                    @click="updateQty(item.product.id, 1)"
                    :disabled="item.quantity >= item.product.stock"
                    aria-label="Tambah"
                  >
                    <Plus :size="14" :stroke-width="2.5" />
                  </button>
                </div>
                <button class="di-del" @click="removeFromCart(item.product.id)" aria-label="Hapus">
                  <Trash2 :size="15" :stroke-width="2" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <div v-if="cart.length > 0" class="drawer-foot">
          <div class="drawer-total-row">
            <span>Total</span>
            <strong>{{ fmt(cartTotal) }}</strong>
          </div>
          <button class="kop-btn primary full" @click="goCheckout">
            Checkout
            <ArrowRight :size="18" :stroke-width="2" />
          </button>
        </div>
      </aside>
    </Transition>

    <!-- ═══ CHECKOUT VIEW ═══ -->
    <div v-if="view === 'checkout'" class="kop-flow">
      <div class="flow-box">
        <button class="flow-back" @click="view = 'shop'">
          <ChevronLeft :size="18" :stroke-width="2" />
          Kembali
        </button>

        <div class="flow-steps">
          <span class="step active">Rincian</span>
          <span class="step-line"></span>
          <span class="step">Bayar</span>
          <span class="step-line"></span>
          <span class="step">Selesai</span>
        </div>

        <h2 class="flow-title">Rincian Pesanan</h2>
        <p class="flow-sub">Periksa pesanan Anda sebelum melanjutkan</p>

        <div class="order-card">
          <div class="oc-id">
            <span>No. Pesanan</span>
            <strong>{{ orderId }}</strong>
          </div>

          <div class="oc-items">
            <div v-for="item in cart" :key="item.product.id" class="oc-item">
              <div class="oc-item-left">
                <div class="oc-thumb" :style="{ background: item.product.bgColor }">
                  <span>{{ item.product.emoji }}</span>
                </div>
                <div class="oc-item-info">
                  <h4>{{ item.product.name }}</h4>
                  <span>{{ item.quantity }} &times; {{ fmt(item.product.numPrice) }}</span>
                </div>
              </div>
              <strong class="oc-item-total">{{ fmt(item.product.numPrice * item.quantity) }}</strong>
            </div>
          </div>

          <div class="oc-summary">
            <div class="oc-row">
              <span>Subtotal ({{ cartCount }} item)</span>
              <span>{{ fmt(cartTotal) }}</span>
            </div>
            <div class="oc-row">
              <span>Biaya layanan</span>
              <span class="oc-free">Gratis</span>
            </div>
            <div class="oc-row oc-total">
              <span>Total Pembayaran</span>
              <strong>{{ fmt(cartTotal) }}</strong>
            </div>
          </div>
        </div>

        <button class="kop-btn primary full" @click="view = 'payment'">
          Pilih Pembayaran
          <ArrowRight :size="18" :stroke-width="2" />
        </button>
      </div>
    </div>

    <!-- ═══ PAYMENT METHOD ═══ -->
    <div v-if="view === 'payment'" class="kop-flow">
      <div class="flow-box">
        <button class="flow-back" @click="view = 'checkout'">
          <ChevronLeft :size="18" :stroke-width="2" />
          Kembali
        </button>

        <div class="flow-steps">
          <span class="step done">Rincian</span>
          <span class="step-line done"></span>
          <span class="step active">Bayar</span>
          <span class="step-line"></span>
          <span class="step">Selesai</span>
        </div>

        <h2 class="flow-title">Metode Pembayaran</h2>
        <p class="flow-sub">Pilih metode pembayaran yang tersedia</p>

        <div class="pay-grid">
          <button
            :class="['pay-opt', { selected: payMethod === 'qris' }]"
            @click="payMethod = 'qris'"
          >
            <div class="po-icon">
              <QrCode :size="28" :stroke-width="1.5" />
            </div>
            <div class="po-text">
              <h4>QRIS</h4>
              <span>Scan QR untuk bayar instan</span>
            </div>
            <div v-if="payMethod === 'qris'" class="po-check">
              <Check :size="16" :stroke-width="3" />
            </div>
          </button>

          <button
            :class="['pay-opt', { selected: payMethod === 'transfer' }]"
            @click="payMethod = 'transfer'"
          >
            <div class="po-icon">
              <CreditCard :size="28" :stroke-width="1.5" />
            </div>
            <div class="po-text">
              <h4>Transfer Bank</h4>
              <span>Transfer ke rekening koperasi</span>
            </div>
            <div v-if="payMethod === 'transfer'" class="po-check">
              <Check :size="16" :stroke-width="3" />
            </div>
          </button>
        </div>

        <div class="pay-amount">
          <span>Total Pembayaran</span>
          <strong>{{ fmt(cartTotal) }}</strong>
        </div>

        <button
          class="kop-btn primary full"
          :disabled="!payMethod"
          @click="processPayment"
        >
          Bayar Sekarang
          <ArrowRight :size="18" :stroke-width="2" />
        </button>
      </div>
    </div>

    <!-- ═══ PENDING PAYMENT ═══ -->
    <div v-if="view === 'pending'" class="kop-flow">
      <div class="flow-box flow-center">
        <div class="flow-steps">
          <span class="step done">Rincian</span>
          <span class="step-line done"></span>
          <span class="step active">Bayar</span>
          <span class="step-line"></span>
          <span class="step">Selesai</span>
        </div>

        <div class="pend-card">
          <div class="pend-head">
            <h3>{{ payMethod === 'qris' ? 'Scan QRIS' : 'Transfer Bank' }}</h3>
            <span class="pend-badge">Menunggu Pembayaran</span>
          </div>

          <!-- QRIS content -->
          <div v-if="payMethod === 'qris'" class="pend-body">
            <div class="qr-wrap">
              <div class="qr-code">
                <div class="qr-inner">
                  <div class="qr-block tl"></div>
                  <div class="qr-block tr"></div>
                  <div class="qr-block bl"></div>
                  <div class="qr-dots">
                    <span v-for="n in 25" :key="n" class="qr-dot" :style="{ opacity: Math.random() > 0.3 ? 1 : 0.2 }"></span>
                  </div>
                </div>
                <span class="qr-brand">QRIS</span>
              </div>
            </div>
            <p class="pend-amount">{{ fmt(cartTotal) }}</p>
            <p class="pend-hint">Buka aplikasi e-wallet atau m-banking, lalu scan kode QR di atas</p>
          </div>

          <!-- Transfer content -->
          <div v-if="payMethod === 'transfer'" class="pend-body">
            <div class="bank-details">
              <div class="bank-row">
                <span class="bank-label">Bank</span>
                <strong>BSI (Bank Syariah Indonesia)</strong>
              </div>
              <div class="bank-row bank-copy" @click="copyText('7182736450')">
                <span class="bank-label">No. Rekening</span>
                <div class="bank-val">
                  <strong>7182736450</strong>
                  <span class="copy-tag">
                    <Copy :size="13" :stroke-width="2" />
                    Salin
                  </span>
                </div>
              </div>
              <div class="bank-row">
                <span class="bank-label">Atas Nama</span>
                <strong>Koperasi SMK Bahrul Ulum</strong>
              </div>
              <div class="bank-row bank-copy" @click="copyText(String(cartTotal))">
                <span class="bank-label">Jumlah Transfer</span>
                <div class="bank-val">
                  <strong class="bank-amount">{{ fmt(cartTotal) }}</strong>
                  <span class="copy-tag">
                    <Copy :size="13" :stroke-width="2" />
                    Salin
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Countdown Timer -->
          <div class="pend-timer">
            <span class="timer-label">Selesaikan pembayaran dalam</span>
            <div class="timer-row">
              <div class="timer-cell">
                <span class="timer-num">{{ pad(timeLeft.hours) }}</span>
                <span class="timer-unit">Jam</span>
              </div>
              <span class="timer-colon">:</span>
              <div class="timer-cell">
                <span class="timer-num">{{ pad(timeLeft.minutes) }}</span>
                <span class="timer-unit">Menit</span>
              </div>
              <span class="timer-colon">:</span>
              <div class="timer-cell">
                <span class="timer-num">{{ pad(timeLeft.seconds) }}</span>
                <span class="timer-unit">Detik</span>
              </div>
            </div>
            <p class="timer-warn">Pembayaran otomatis dibatalkan jika melewati batas waktu</p>
          </div>
        </div>

        <button
          class="kop-btn primary full"
          :disabled="isProcessing"
          @click="confirmPayment"
        >
          <span v-if="isProcessing" class="btn-spin"></span>
          {{ isProcessing ? 'Memverifikasi...' : 'Saya Sudah Bayar' }}
        </button>
      </div>
    </div>

    <!-- ═══ SUCCESS VIEW ═══ -->
    <div v-if="view === 'success'" class="kop-flow">
      <div class="flow-box flow-center">
        <div class="flow-steps">
          <span class="step done">Rincian</span>
          <span class="step-line done"></span>
          <span class="step done">Bayar</span>
          <span class="step-line done"></span>
          <span class="step active">Selesai</span>
        </div>

        <div class="suc-circle">
          <Check :size="44" :stroke-width="2.5" />
        </div>

        <h2 class="suc-title">Pembayaran Berhasil!</h2>
        <p class="suc-sub">Pesanan Anda telah dikonfirmasi</p>

        <div class="suc-card">
          <div class="suc-row">
            <span>No. Pesanan</span>
            <strong>{{ orderId }}</strong>
          </div>
          <div class="suc-row">
            <span>Metode</span>
            <strong>{{ payMethod === 'qris' ? 'QRIS' : 'Transfer Bank' }}</strong>
          </div>
          <div class="suc-row">
            <span>Total Dibayar</span>
            <strong class="suc-amount">{{ fmt(cartTotal) }}</strong>
          </div>
          <div class="suc-row">
            <span>Status</span>
            <span class="suc-status">Lunas</span>
          </div>
        </div>

        <div class="suc-notif">
          <div class="sn-icon">
            <Bell :size="20" :stroke-width="2" />
          </div>
          <div class="sn-text">
            <strong>Penjaga koperasi telah diberitahu</strong>
            <p>Pesanan Anda akan segera disiapkan. Silakan ambil di koperasi sekolah saat jam istirahat.</p>
          </div>
        </div>

        <button class="kop-btn primary full" @click="backToShop">
          Kembali Belanja
        </button>
      </div>
    </div>

    <!-- ═══ FLOATING CART ═══ -->
    <Transition name="float-up">
      <button
        v-if="view === 'shop' && cartCount > 0 && !drawerOpen"
        class="kop-float"
        @click="drawerOpen = true"
      >
        <ShoppingBag :size="18" :stroke-width="2" />
        <span class="float-count">{{ cartCount }} item</span>
        <span class="float-sep"></span>
        <span class="float-total">{{ fmt(cartTotal) }}</span>
      </button>
    </Transition>
  </section>
</template>

<script setup>
import { ref, computed, onUnmounted } from 'vue'
import {
  ChevronLeft, ShoppingBag, Plus, Minus, X, Trash2,
  ArrowRight, QrCode, CreditCard, Check, Copy,
  Info, Package, Bell
} from 'lucide-vue-next'

/* ─── State ─── */
const view = ref('shop')
const activeCat = ref('Semua')
const drawerOpen = ref(false)
const payMethod = ref(null)
const isProcessing = ref(false)
const orderId = ref('')
const deadline = ref(null)
const timeLeft = ref({ hours: 0, minutes: 59, seconds: 59 })
let timerInterval = null

const toast = ref({ show: false, message: '', type: 'success' })
let toastTimer = null

/* ─── Product Data ─── */
const categories = ['Semua', 'Alat Tulis', 'Seragam', 'Snack', 'Minuman', 'Aksesoris']

const products = [
  { id: 1,  name: 'Pensil 2B',             desc: 'Pensil standar ujian nasional',   category: 'Alat Tulis', numPrice: 3000,   stock: 45, emoji: '✏️', bgColor: '#e8efe4', isNew: false },
  { id: 2,  name: 'Buku Tulis 40 Lembar',  desc: 'Buku tulis Sinar Dunia',          category: 'Alat Tulis', numPrice: 5000,   stock: 30, emoji: '📒', bgColor: '#e4ede8', isNew: false },
  { id: 3,  name: 'Penggaris 30cm',        desc: 'Penggaris plastik transparan',    category: 'Alat Tulis', numPrice: 4000,   stock: 20, emoji: '📏', bgColor: '#eaf0e6', isNew: false },
  { id: 4,  name: 'Seragam Putih',         desc: 'Kemeja putih lengan pendek',      category: 'Seragam',    numPrice: 75000,  stock: 15, emoji: '👕', bgColor: '#e6ece8', isNew: true  },
  { id: 5,  name: 'Seragam Biru',          desc: 'Kemeja biru lengan panjang',      category: 'Seragam',    numPrice: 85000,  stock: 12, emoji: '👔', bgColor: '#e4eae6', isNew: true  },
  { id: 6,  name: 'Rok/Celana Biru Tua',   desc: 'Rok atau celana bahan',           category: 'Seragam',    numPrice: 65000,  stock: 18, emoji: '👖', bgColor: '#e8ede4', isNew: false },
  { id: 7,  name: 'Jaket Almamater',       desc: 'Jaket hijau toska sekolah',       category: 'Seragam',    numPrice: 150000, stock: 8,  emoji: '🧥', bgColor: '#e2ece6', isNew: true  },
  { id: 8,  name: 'Topi Sekolah',          desc: 'Topi dengan logo sekolah',        category: 'Aksesoris',  numPrice: 35000,  stock: 25, emoji: '🧢', bgColor: '#e6ece4', isNew: false },
  { id: 9,  name: 'Dasi Sekolah',          desc: 'Dasi regu / pramuka',             category: 'Aksesoris',  numPrice: 25000,  stock: 22, emoji: '🎀', bgColor: '#ece8e4', isNew: false },
  { id: 10, name: 'Sepatu Olahraga',       desc: 'Hitam putih, semua ukuran',       category: 'Aksesoris',  numPrice: 120000, stock: 10, emoji: '👟', bgColor: '#eaece6', isNew: false },
  { id: 11, name: 'Snack Ring',            desc: 'Keripik kentang rasa BBQ',        category: 'Snack',      numPrice: 4000,   stock: 40, emoji: '🍿', bgColor: '#efe8e0', isNew: false },
  { id: 12, name: 'Biskuit Cokelat',       desc: 'Biskuit cokelat krim',            category: 'Snack',      numPrice: 3500,   stock: 35, emoji: '🍪', bgColor: '#eee8e2', isNew: false },
  { id: 13, name: 'Roti Goreng',           desc: 'Roti goreng isi cokelat',         category: 'Snack',      numPrice: 5000,   stock: 15, emoji: '🥖', bgColor: '#efe8e0', isNew: true  },
  { id: 14, name: 'Air Mineral',           desc: 'Air mineral 600ml',               category: 'Minuman',    numPrice: 3000,   stock: 50, emoji: '💧', bgColor: '#e4ede8', isNew: false },
  { id: 15, name: 'Teh Botol',             desc: 'Teh botol Sosro 450ml',           category: 'Minuman',    numPrice: 5000,   stock: 30, emoji: '🍵', bgColor: '#e6efe8', isNew: false },
  { id: 16, name: 'Kopi Susu',             desc: 'Kopi susu kemasan',               category: 'Minuman',    numPrice: 7000,   stock: 18, emoji: '☕', bgColor: '#eae6e0', isNew: true  },
]

/* ─── Cart ─── */
const cart = ref([])

const filtered = computed(() => {
  if (activeCat.value === 'Semua') return products
  return products.filter(p => p.category === activeCat.value)
})

const cartCount = computed(() => cart.value.reduce((s, i) => s + i.quantity, 0))
const cartTotal = computed(() => cart.value.reduce((s, i) => s + i.product.numPrice * i.quantity, 0))

function addToCart(product) {
  if (product.stock === 0) return
  const existing = cart.value.find(i => i.product.id === product.id)
  if (existing) {
    if (existing.quantity < product.stock) {
      existing.quantity++
      showToast(`${product.name} ditambahkan`)
    } else {
      showToast('Stok tidak mencukupi', 'error')
    }
  } else {
    cart.value.push({ product, quantity: 1 })
    showToast(`${product.name} ditambahkan`)
  }
}

function removeFromCart(id) {
  cart.value = cart.value.filter(i => i.product.id !== id)
  if (cart.value.length === 0) drawerOpen.value = false
}

function updateQty(id, delta) {
  const item = cart.value.find(i => i.product.id === id)
  if (!item) return
  const next = item.quantity + delta
  if (next <= 0) { removeFromCart(id); return }
  if (next > item.product.stock) { showToast('Stok tidak mencukupi', 'error'); return }
  item.quantity = next
}

/* ─── Helpers ─── */
function fmt(n) { return 'Rp ' + n.toLocaleString('id-ID') }
function pad(n) { return String(n).padStart(2, '0') }

function genOrderId() {
  const d = new Date()
  return `KOP-${d.getFullYear()}${pad(d.getMonth() + 1)}${pad(d.getDate())}-${Math.floor(Math.random() * 9000) + 1000}`
}

function showToast(msg, type = 'success') {
  if (toastTimer) clearTimeout(toastTimer)
  toast.value = { show: true, message: msg, type }
  toastTimer = setTimeout(() => { toast.value.show = false }, 3000)
}

async function copyText(text) {
  try {
    await navigator.clipboard.writeText(text)
    showToast('Berhasil disalin!')
  } catch { showToast('Gagal menyalin', 'error') }
}

/* ─── Navigation ─── */
function goBack() { window.history.back() }

function goCheckout() {
  drawerOpen.value = false
  orderId.value = genOrderId()
  setTimeout(() => { view.value = 'checkout' }, 200)
}

function processPayment() {
  view.value = 'pending'
  startTimer()
}

function startTimer() {
  if (timerInterval) clearInterval(timerInterval)
  deadline.value = Date.now() + 3600000
  const tick = () => {
    const rem = deadline.value - Date.now()
    if (rem <= 0) {
      clearInterval(timerInterval)
      showToast('Batas waktu habis, pembayaran dibatalkan', 'error')
      resetOrder()
      return
    }
    timeLeft.value = {
      hours:   Math.floor(rem / 3600000),
      minutes: Math.floor((rem % 3600000) / 60000),
      seconds: Math.floor((rem % 60000) / 1000),
    }
  }
  tick()
  timerInterval = setInterval(tick, 1000)
}

function confirmPayment() {
  isProcessing.value = true
  setTimeout(() => {
    isProcessing.value = false
    if (timerInterval) clearInterval(timerInterval)
    view.value = 'success'
    showToast('Pembayaran berhasil dikonfirmasi!')
  }, 2000)
}

function backToShop() { resetOrder() }

function resetOrder() {
  cart.value = []
  payMethod.value = null
  orderId.value = ''
  isProcessing.value = false
  view.value = 'shop'
}

onUnmounted(() => {
  if (timerInterval) clearInterval(timerInterval)
  if (toastTimer) clearTimeout(toastTimer)
})
</script>

<!-- Font import (unscoped) -->
<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap');
</style>

<style scoped>
/* ═══════════════════════════════════════
   DESIGN TOKENS
   Forest green palette. One accent (amber).
   Shape: 12px containers, 8px inputs, pill CTAs.
   ═══════════════════════════════════════ */
.kop {
  --green-900: #0f3d22;
  --green-700: #1a6b3c;
  --green-600: #1f7d46;
  --green-500: #27965a;
  --green-200: #b8dbc6;
  --green-100: #dceee2;
  --green-50:  #f0f7f2;
  --surface:   #f4f6f3;
  --white:     #ffffff;
  --text-1:    #1a2420;
  --text-2:    #4d5f53;
  --text-3:    #7d8f84;
  --amber:     #c77d0a;
  --amber-dk:  #a86808;
  --red:       #c53030;
  --border:    rgba(26, 107, 60, 0.1);
  --shadow-sm: 0 2px 8px rgba(20, 50, 30, 0.05);
  --shadow-md: 0 8px 24px rgba(20, 50, 30, 0.07);
  --shadow-lg: 0 16px 40px rgba(20, 50, 30, 0.1);
  --radius-sm: 8px;
  --radius-md: 12px;
  --radius-lg: 16px;
  --radius-pill: 999px;

  font-family: 'Outfit', system-ui, -apple-system, sans-serif;
  min-height: 100vh;
  min-height: 100dvh;
  background: var(--surface);
  color: var(--text-1);
  -webkit-font-smoothing: antialiased;
}

/* ═══ TOAST ═══ */
.kop-toast {
  position: fixed;
  top: 24px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 9999;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 20px;
  border-radius: var(--radius-pill);
  background: var(--green-900);
  color: #fff;
  font-size: 14px;
  font-weight: 500;
  box-shadow: var(--shadow-lg);
  pointer-events: none;
}
.kop-toast.error {
  background: var(--red);
}
.toast-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #4ade80;
  flex-shrink: 0;
}
.kop-toast.error .toast-dot {
  background: #fca5a5;
}

.toast-enter-active { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
.toast-leave-active { transition: all 0.25s ease; }
.toast-enter-from { opacity: 0; transform: translateX(-50%) translateY(-12px); }
.toast-leave-to   { opacity: 0; transform: translateX(-50%) translateY(-8px); }

/* ═══ SHOP VIEW ═══ */
.kop-shop {
  padding: 24px 7% 120px;
  max-width: 1400px;
  margin: 0 auto;
}

.kop-topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 32px;
  padding-top: 16px;
}

.kop-back {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  border: 1px solid var(--border);
  background: var(--white);
  border-radius: var(--radius-md);
  color: var(--green-700);
  cursor: pointer;
  transition: all 0.2s ease;
}
.kop-back:hover {
  background: var(--green-50);
  border-color: var(--green-200);
}
.kop-back:active { transform: scale(0.96); }

.kop-cart-topbar {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  border: 1px solid var(--border);
  background: var(--white);
  border-radius: var(--radius-md);
  color: var(--green-700);
  cursor: pointer;
  transition: all 0.2s ease;
}
.kop-cart-topbar:hover {
  background: var(--green-50);
  border-color: var(--green-200);
}
.cart-badge {
  position: absolute;
  top: -6px;
  right: -6px;
  min-width: 20px;
  height: 20px;
  padding: 0 6px;
  border-radius: var(--radius-pill);
  background: var(--green-700);
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
}

.kop-header {
  margin-bottom: 32px;
}
.kop-eyebrow {
  display: inline-block;
  padding: 6px 14px;
  border-radius: var(--radius-pill);
  background: var(--green-100);
  color: var(--green-700);
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  margin-bottom: 14px;
}
.kop-header h1 {
  font-size: clamp(28px, 4vw, 44px);
  font-weight: 800;
  letter-spacing: -0.02em;
  line-height: 1.1;
  color: var(--green-900);
  margin: 0 0 10px;
}
.kop-header p {
  color: var(--text-2);
  font-size: 16px;
  line-height: 1.6;
  max-width: 480px;
  margin: 0;
}

/* ═══ CATEGORY TABS ═══ */
.kop-tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 28px;
}
.kop-tab {
  padding: 9px 18px;
  border: 1px solid var(--border);
  border-radius: var(--radius-pill);
  background: var(--white);
  color: var(--text-2);
  font-family: inherit;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}
.kop-tab:hover {
  border-color: var(--green-200);
  color: var(--green-700);
}
.kop-tab.active {
  background: var(--green-700);
  border-color: var(--green-700);
  color: #fff;
}
.kop-tab:active { transform: scale(0.96); }

/* ═══ PRODUCT GRID ═══ */
.kop-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 18px;
}

.kop-card {
  border-radius: var(--radius-md);
  overflow: hidden;
  background: var(--white);
  border: 1px solid var(--border);
  box-shadow: var(--shadow-sm);
  transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s ease;
}
.kop-card:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-md);
}
.kop-card:hover .card-add {
  opacity: 1;
  transform: translateY(0);
}

.card-visual {
  position: relative;
  aspect-ratio: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}
.card-emoji {
  font-size: 48px;
  line-height: 1;
  filter: drop-shadow(0 2px 4px rgba(0,0,0,0.08));
}
.card-new {
  position: absolute;
  top: 10px;
  left: 10px;
  padding: 3px 10px;
  border-radius: var(--radius-pill);
  background: var(--amber);
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
.card-add {
  position: absolute;
  bottom: 10px;
  right: 10px;
  width: 36px;
  height: 36px;
  border: none;
  border-radius: var(--radius-pill);
  background: var(--green-700);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  opacity: 0;
  transform: translateY(6px);
  transition: all 0.2s ease;
  box-shadow: 0 4px 12px rgba(26, 107, 60, 0.3);
}
.card-add:hover { background: var(--green-600); }
.card-add:active { transform: translateY(0) scale(0.92); }
.card-add:disabled {
  background: var(--text-3);
  cursor: not-allowed;
  box-shadow: none;
}

.card-body {
  padding: 14px 16px 16px;
}
.card-cat {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--green-700);
}
.card-name {
  margin: 5px 0;
  font-size: 15px;
  font-weight: 700;
  color: var(--text-1);
  line-height: 1.3;
}
.card-desc {
  margin: 0;
  font-size: 13px;
  color: var(--text-3);
  line-height: 1.4;
}
.card-foot {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 10px;
  padding-top: 10px;
  border-top: 1px solid var(--border);
}
.card-price {
  font-size: 14px;
  font-weight: 800;
  color: var(--green-700);
}
.card-stock {
  font-size: 11px;
  font-weight: 500;
  color: var(--text-3);
}
.card-stock.low { color: var(--amber); font-weight: 600; }
.card-stock.out { color: var(--red); font-weight: 600; }

/* ═══ EMPTY STATE ═══ */
.kop-empty {
  text-align: center;
  padding: 60px 20px;
  color: var(--text-3);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
}
.kop-empty p { margin: 0; font-size: 15px; }

/* ═══ INFO BANNER ═══ */
.kop-info {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin-top: 32px;
  padding: 16px 20px;
  border-radius: var(--radius-md);
  background: var(--green-50);
  border: 1px solid var(--border);
}
.info-icon {
  color: var(--green-700);
  flex-shrink: 0;
  margin-top: 2px;
}
.kop-info p {
  font-size: 13.5px;
  color: var(--text-2);
  line-height: 1.6;
  margin: 0;
}
.kop-info strong { color: var(--text-1); }

/* ═══ CART DRAWER ═══ */
.kop-overlay {
  position: fixed;
  inset: 0;
  z-index: 900;
  background: rgba(15, 30, 20, 0.4);
  backdrop-filter: blur(4px);
}
.overlay-fade-enter-active,
.overlay-fade-leave-active { transition: opacity 0.3s ease; }
.overlay-fade-enter-from,
.overlay-fade-leave-to { opacity: 0; }

.kop-drawer {
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  z-index: 950;
  width: 400px;
  max-width: 92vw;
  background: var(--white);
  display: flex;
  flex-direction: column;
  box-shadow: -8px 0 40px rgba(15, 30, 20, 0.12);
}
.slide-right-enter-active { transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1); }
.slide-right-leave-active { transition: transform 0.25s ease; }
.slide-right-enter-from,
.slide-right-leave-to { transform: translateX(100%); }

.drawer-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
}
.drawer-head h2 {
  font-size: 18px;
  font-weight: 700;
  margin: 0;
}
.drawer-head h2 span {
  color: var(--text-3);
  font-weight: 500;
}
.drawer-close {
  width: 36px;
  height: 36px;
  border: 1px solid var(--border);
  background: transparent;
  border-radius: var(--radius-sm);
  color: var(--text-2);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.15s ease;
}
.drawer-close:hover { background: var(--green-50); color: var(--text-1); }

.drawer-empty {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  color: var(--text-3);
  padding: 40px;
}
.drawer-empty p {
  font-size: 15px;
  font-weight: 600;
  color: var(--text-2);
  margin: 8px 0 0;
}
.drawer-empty span { font-size: 13px; }

.drawer-body {
  flex: 1;
  overflow-y: auto;
  padding: 16px 24px;
}
.drawer-items {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.di {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px;
  border-radius: var(--radius-md);
  background: var(--surface);
  border: 1px solid transparent;
  transition: border-color 0.15s ease;
}
.di:hover { border-color: var(--border); }

.di-visual {
  width: 48px;
  height: 48px;
  border-radius: var(--radius-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  font-size: 24px;
}
.di-content {
  flex: 1;
  min-width: 0;
}
.di-content h4 {
  margin: 0;
  font-size: 14px;
  font-weight: 600;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.di-price {
  font-size: 12px;
  color: var(--text-3);
  font-weight: 500;
}

.di-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-shrink: 0;
}
.qty-control {
  display: flex;
  align-items: center;
  gap: 0;
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  overflow: hidden;
  background: var(--white);
}
.qty-btn {
  width: 30px;
  height: 30px;
  border: none;
  background: transparent;
  color: var(--text-2);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.15s ease;
}
.qty-btn:hover { background: var(--green-50); color: var(--green-700); }
.qty-btn:disabled { opacity: 0.3; cursor: not-allowed; }
.qty-btn:active { transform: scale(0.9); }
.qty-num {
  width: 28px;
  text-align: center;
  font-size: 13px;
  font-weight: 700;
  border-left: 1px solid var(--border);
  border-right: 1px solid var(--border);
  line-height: 30px;
}

.di-del {
  width: 30px;
  height: 30px;
  border: none;
  background: transparent;
  color: var(--text-3);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  border-radius: var(--radius-sm);
  transition: all 0.15s ease;
}
.di-del:hover { background: #fef2f2; color: var(--red); }

.drawer-foot {
  padding: 16px 24px 24px;
  border-top: 1px solid var(--border);
  flex-shrink: 0;
}
.drawer-total-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 14px;
}
.drawer-total-row span {
  font-size: 14px;
  color: var(--text-2);
}
.drawer-total-row strong {
  font-size: 18px;
  font-weight: 800;
  color: var(--green-900);
}

/* ═══ GLOBAL BUTTON ═══ */
.kop-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 14px 28px;
  border: none;
  border-radius: var(--radius-pill);
  font-family: inherit;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
  letter-spacing: 0.01em;
}
.kop-btn:active { transform: scale(0.97); }
.kop-btn.primary {
  background: var(--green-700);
  color: #fff;
  box-shadow: 0 4px 16px rgba(26, 107, 60, 0.25);
}
.kop-btn.primary:hover {
  background: var(--green-600);
  box-shadow: 0 6px 20px rgba(26, 107, 60, 0.3);
}
.kop-btn.primary:disabled {
  background: var(--text-3);
  box-shadow: none;
  cursor: not-allowed;
}
.kop-btn.full { width: 100%; }

/* ═══ FLOW VIEWS (Checkout, Payment, Pending, Success) ═══ */
.kop-flow {
  padding: 24px 7%;
  min-height: 100vh;
  min-height: 100dvh;
  display: flex;
  align-items: flex-start;
  justify-content: center;
}
.flow-box {
  width: 100%;
  max-width: 560px;
  padding-top: 24px;
}
.flow-box.flow-center {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.flow-back {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 0;
  border: none;
  background: transparent;
  color: var(--text-2);
  font-family: inherit;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: color 0.15s ease;
  margin-bottom: 24px;
}
.flow-back:hover { color: var(--green-700); }

/* ─── Step indicator ─── */
.flow-steps {
  display: flex;
  align-items: center;
  gap: 0;
  margin-bottom: 32px;
  width: 100%;
  max-width: 360px;
}
.flow-center .flow-steps { align-self: center; }

.step {
  font-size: 12px;
  font-weight: 700;
  color: var(--text-3);
  white-space: nowrap;
  letter-spacing: 0.04em;
}
.step.active { color: var(--green-700); }
.step.done { color: var(--green-500); }
.step-line {
  flex: 1;
  height: 2px;
  background: var(--green-100);
  margin: 0 12px;
  border-radius: 1px;
}
.step-line.done { background: var(--green-500); }

.flow-title {
  font-size: clamp(22px, 3vw, 28px);
  font-weight: 800;
  letter-spacing: -0.02em;
  margin: 0 0 6px;
  color: var(--green-900);
}
.flow-sub {
  font-size: 14px;
  color: var(--text-2);
  margin: 0 0 28px;
}
.flow-center .flow-title,
.flow-center .flow-sub { text-align: center; }

/* ═══ ORDER CARD (Checkout) ═══ */
.order-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  overflow: hidden;
  margin-bottom: 20px;
  box-shadow: var(--shadow-sm);
}
.oc-id {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 20px;
  background: var(--green-50);
  border-bottom: 1px solid var(--border);
}
.oc-id span { font-size: 13px; color: var(--text-3); }
.oc-id strong { font-size: 13px; color: var(--green-700); font-weight: 700; }

.oc-items { padding: 8px 20px; }
.oc-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid var(--border);
}
.oc-item:last-child { border-bottom: none; }
.oc-item-left {
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 0;
}
.oc-thumb {
  width: 42px;
  height: 42px;
  border-radius: var(--radius-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  flex-shrink: 0;
}
.oc-item-info h4 {
  margin: 0;
  font-size: 14px;
  font-weight: 600;
}
.oc-item-info span {
  font-size: 12px;
  color: var(--text-3);
}
.oc-item-total {
  font-size: 14px;
  font-weight: 700;
  color: var(--text-1);
  flex-shrink: 0;
  margin-left: 12px;
}

.oc-summary {
  padding: 16px 20px;
  border-top: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.oc-row {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
  color: var(--text-2);
}
.oc-free {
  color: var(--green-500);
  font-weight: 600;
}
.oc-total {
  padding-top: 10px;
  border-top: 1px dashed var(--border);
  font-size: 15px;
}
.oc-total span { font-weight: 600; color: var(--text-1); }
.oc-total strong {
  font-size: 17px;
  font-weight: 800;
  color: var(--green-700);
}

/* ═══ PAYMENT OPTIONS ═══ */
.pay-grid {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 24px;
}
.pay-opt {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 18px 20px;
  border: 2px solid var(--border);
  border-radius: var(--radius-md);
  background: var(--white);
  cursor: pointer;
  transition: all 0.2s ease;
  text-align: left;
  font-family: inherit;
}
.pay-opt:hover { border-color: var(--green-200); }
.pay-opt.selected {
  border-color: var(--green-700);
  background: var(--green-50);
}
.pay-opt:active { transform: scale(0.99); }

.po-icon {
  width: 52px;
  height: 52px;
  border-radius: var(--radius-md);
  background: var(--green-50);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--green-700);
  flex-shrink: 0;
}
.pay-opt.selected .po-icon { background: var(--green-100); }

.po-text {
  flex: 1;
}
.po-text h4 {
  margin: 0;
  font-size: 15px;
  font-weight: 700;
}
.po-text span {
  font-size: 13px;
  color: var(--text-3);
}
.po-check {
  width: 28px;
  height: 28px;
  border-radius: var(--radius-pill);
  background: var(--green-700);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.pay-amount {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 20px;
  border-radius: var(--radius-md);
  background: var(--green-50);
  margin-bottom: 16px;
}
.pay-amount span { font-size: 14px; color: var(--text-2); }
.pay-amount strong { font-size: 18px; font-weight: 800; color: var(--green-900); }

/* ═══ PENDING PAYMENT ═══ */
.pend-card {
  width: 100%;
  max-width: 560px;
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  overflow: hidden;
  margin-bottom: 20px;
  box-shadow: var(--shadow-sm);
}
.pend-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 18px 24px;
  border-bottom: 1px solid var(--border);
}
.pend-head h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 700;
}
.pend-badge {
  padding: 4px 12px;
  border-radius: var(--radius-pill);
  background: #fef3c7;
  color: #92400e;
  font-size: 12px;
  font-weight: 700;
}

.pend-body { padding: 28px 24px; }

/* QR code visual */
.qr-wrap {
  display: flex;
  justify-content: center;
  margin-bottom: 20px;
}
.qr-code {
  width: 180px;
  height: 180px;
  border: 2px solid var(--text-1);
  border-radius: var(--radius-sm);
  padding: 12px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  position: relative;
}
.qr-inner {
  width: 100%;
  height: 100%;
  position: relative;
  display: grid;
  grid-template-columns: 1fr 1fr;
  grid-template-rows: 1fr 1fr;
  gap: 8px;
}
.qr-block {
  border: 3px solid var(--text-1);
  border-radius: 3px;
  position: relative;
}
.qr-block::after {
  content: '';
  position: absolute;
  inset: 4px;
  background: var(--text-1);
  border-radius: 1px;
}
.qr-block.tl { grid-area: 1 / 1; }
.qr-block.tr { grid-area: 1 / 2; }
.qr-block.bl { grid-area: 2 / 1; }
.qr-dots {
  grid-area: 2 / 2;
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 2px;
  padding: 2px;
}
.qr-dot {
  aspect-ratio: 1;
  background: var(--text-1);
  border-radius: 1px;
}
.qr-brand {
  position: absolute;
  bottom: -10px;
  background: var(--white);
  padding: 0 8px;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.08em;
  color: var(--text-1);
}

.pend-amount {
  text-align: center;
  font-size: 22px;
  font-weight: 800;
  color: var(--green-900);
  margin: 0 0 8px;
}
.pend-hint {
  text-align: center;
  font-size: 13px;
  color: var(--text-3);
  margin: 0;
  line-height: 1.5;
}

/* Bank transfer details */
.bank-details {
  display: flex;
  flex-direction: column;
  gap: 0;
}
.bank-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 0;
  border-bottom: 1px solid var(--border);
}
.bank-row:last-child { border-bottom: none; }
.bank-label {
  font-size: 13px;
  color: var(--text-3);
}
.bank-row strong {
  font-size: 14px;
  font-weight: 700;
  text-align: right;
}
.bank-amount {
  color: var(--green-700);
  font-size: 16px;
}
.bank-copy {
  cursor: pointer;
  border-radius: var(--radius-sm);
  padding: 14px 8px;
  margin: 0 -8px;
  transition: background 0.15s ease;
}
.bank-copy:hover { background: var(--green-50); }
.bank-val {
  display: flex;
  align-items: center;
  gap: 10px;
}
.copy-tag {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 3px 8px;
  border-radius: var(--radius-pill);
  background: var(--green-50);
  color: var(--green-700);
  font-size: 11px;
  font-weight: 600;
  cursor: pointer;
}

/* Timer */
.pend-timer {
  padding: 24px;
  border-top: 1px solid var(--border);
  text-align: center;
}
.timer-label {
  font-size: 13px;
  color: var(--text-3);
  margin-bottom: 14px;
  display: block;
}
.timer-row {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  margin-bottom: 14px;
}
.timer-cell {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}
.timer-num {
  width: 56px;
  height: 56px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: var(--radius-md);
  background: var(--green-50);
  font-size: 24px;
  font-weight: 800;
  color: var(--green-900);
  font-variant-numeric: tabular-nums;
}
.timer-colon {
  font-size: 24px;
  font-weight: 800;
  color: var(--text-3);
  margin-bottom: 20px;
}
.timer-unit {
  font-size: 11px;
  color: var(--text-3);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}
.timer-warn {
  font-size: 12px;
  color: var(--amber);
  margin: 0;
  font-weight: 500;
}

/* ═══ SUCCESS VIEW ═══ */
.suc-circle {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: var(--green-100);
  color: var(--green-700);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
  animation: suc-pop 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
}
@keyframes suc-pop {
  0%   { transform: scale(0); opacity: 0; }
  60%  { transform: scale(1.1); }
  100% { transform: scale(1); opacity: 1; }
}
.suc-title {
  font-size: clamp(22px, 3vw, 28px);
  font-weight: 800;
  color: var(--green-900);
  margin: 0 0 6px;
  letter-spacing: -0.02em;
}
.suc-sub {
  font-size: 14px;
  color: var(--text-2);
  margin: 0 0 28px;
}

.suc-card {
  width: 100%;
  max-width: 560px;
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: 4px 20px;
  margin-bottom: 20px;
  box-shadow: var(--shadow-sm);
}
.suc-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 0;
  border-bottom: 1px solid var(--border);
}
.suc-row:last-child { border-bottom: none; }
.suc-row span { font-size: 13px; color: var(--text-3); }
.suc-row strong { font-size: 14px; }
.suc-amount { color: var(--green-700); }
.suc-status {
  display: inline-block;
  padding: 3px 10px;
  border-radius: var(--radius-pill);
  background: var(--green-100);
  color: var(--green-700);
  font-size: 12px;
  font-weight: 700;
}

.suc-notif {
  width: 100%;
  max-width: 560px;
  display: flex;
  align-items: flex-start;
  gap: 14px;
  padding: 18px 20px;
  border-radius: var(--radius-md);
  background: var(--green-50);
  border: 1px solid var(--border);
  margin-bottom: 20px;
}
.sn-icon {
  width: 40px;
  height: 40px;
  border-radius: var(--radius-pill);
  background: var(--green-100);
  color: var(--green-700);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.sn-text strong {
  display: block;
  font-size: 14px;
  font-weight: 700;
  margin-bottom: 4px;
}
.sn-text p {
  margin: 0;
  font-size: 13px;
  color: var(--text-2);
  line-height: 1.5;
}

/* ═══ FLOATING CART ═══ */
.kop-float {
  position: fixed;
  bottom: 24px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 800;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px 24px;
  border: none;
  border-radius: var(--radius-pill);
  background: var(--green-700);
  color: #fff;
  font-family: inherit;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 8px 32px rgba(26, 107, 60, 0.35);
  transition: all 0.2s ease;
}
.kop-float:hover {
  background: var(--green-600);
  box-shadow: 0 12px 40px rgba(26, 107, 60, 0.4);
  transform: translateX(-50%) translateY(-2px);
}
.kop-float:active { transform: translateX(-50%) scale(0.97); }
.float-count { font-weight: 600; }
.float-sep {
  width: 1px;
  height: 16px;
  background: rgba(255,255,255,0.3);
}
.float-total { font-weight: 800; }

.float-up-enter-active { transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1); }
.float-up-leave-active { transition: all 0.2s ease; }
.float-up-enter-from { opacity: 0; transform: translateX(-50%) translateY(20px); }
.float-up-leave-to   { opacity: 0; transform: translateX(-50%) translateY(10px); }

/* ═══ BUTTON LOADER ═══ */
.btn-spin {
  width: 18px;
  height: 18px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ═══ RESPONSIVE ═══ */
@media (max-width: 1024px) {
  .kop-grid { grid-template-columns: repeat(3, 1fr); }
}

@media (max-width: 768px) {
  .kop-shop { padding: 16px 5% 130px; }
  .kop-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
  .kop-header h1 { font-size: 28px; }

  .card-add { opacity: 1; transform: translateY(0); }
  .card-emoji { font-size: 36px; }

  .kop-flow { padding: 16px 5%; }
  .flow-box { padding-top: 16px; }

  .timer-num { width: 48px; height: 48px; font-size: 20px; }
}

@media (max-width: 520px) {
  .kop-grid { grid-template-columns: 1fr; gap: 12px; }
  .card-visual { aspect-ratio: 16/10; }
  .card-emoji { font-size: 40px; }

  .oc-item { flex-direction: column; align-items: flex-start; gap: 8px; }
  .oc-item-total { margin-left: 0; align-self: flex-end; }

  .pay-amount { flex-direction: column; gap: 4px; text-align: center; }

  .bank-row { flex-direction: column; align-items: flex-start; gap: 4px; }

  .kop-float { left: 16px; right: 16px; transform: none; justify-content: center; }
  .kop-float:hover { transform: translateY(-2px); }
  .kop-float:active { transform: scale(0.98); }
  .float-up-enter-from { transform: translateY(20px); }
  .float-up-leave-to   { transform: translateY(10px); }
}
</style>