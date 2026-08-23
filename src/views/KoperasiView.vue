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
        <h1 class="topbar-title">Koperasi Sekolah</h1>
        <button class="kop-cart-topbar" @click="openCart" aria-label="Keranjang">
          <ShoppingBag :size="20" :stroke-width="2" />
          <span v-if="cartCount > 0" class="cart-badge">{{ cartCount }}</span>
        </button>
      </div>

      <div class="kop-stats">
        <span class="stat-count">{{ filtered.length }} item</span>
        <button class="stat-filter" @click="showFilter = !showFilter">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M2 4h12M4 8h8M6 12h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </button>
      </div>

      <div class="kop-tabs">
        <button
          v-for="cat in categories"
          :key="cat"
          :class="['kop-tab', { active: activeCat === cat }]"
          @click="activeCat = cat"
        >{{ cat }}</button>
      </div>

      <div class="kop-grid">
        <div v-for="p in filtered" :key="p.id" class="kop-card" @click="addToCart(p)">
          <div class="card-visual" :style="{ background: p.bgColor }">
            <span class="card-emoji" role="img" :aria-label="p.name">{{ p.emoji }}</span>
            <span v-if="p.isNew" class="card-new">Baru</span>
          </div>
          <div class="card-body">
            <h3 class="card-name">{{ p.name }}</h3>
            <div class="card-foot">
              <span class="card-price">{{ fmt(p.numPrice) }}</span>
              <button
                class="card-add-inline"
                :disabled="p.stock === 0"
                @click.stop="addToCart(p)"
                :aria-label="'Tambah ' + p.name"
              >
                <Plus :size="16" :stroke-width="2.5" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="filtered.length === 0" class="kop-empty">
        <Package :size="40" :stroke-width="1.5" />
        <p>Belum ada produk di kategori ini.</p>
      </div>

      <!-- ═══ FLOATING CART ═══ -->
      <Transition name="float-up">
        <button
          v-if="cartCount > 0 && !cartOpen"
          class="kop-float"
          @click="openCart"
        >
          <ShoppingBag :size="18" :stroke-width="2" />
          <span class="float-count">{{ cartCount }} item</span>
          <span class="float-sep"></span>
          <span class="float-total">{{ fmt(cartTotal) }}</span>
        </button>
      </Transition>
    </div>

    <!-- ═══ CART SHEET (slide-up) ═══ -->
    <Transition name="overlay-fade">
      <div v-if="cartOpen" class="kop-overlay" @click="cartOpen = false"></div>
    </Transition>
    <Transition name="slide-up">
      <div v-if="cartOpen" class="kop-sheet" role="dialog" aria-label="Keranjang belanja">
        <div class="sheet-handle" @click="cartOpen = false">
          <span class="handle-bar"></span>
        </div>

        <div class="sheet-head">
          <h2>Keranjang <span v-if="cartCount > 0">({{ cartCount }})</span></h2>
          <button class="sheet-close" @click="cartOpen = false" aria-label="Tutup">
            <X :size="20" :stroke-width="2" />
          </button>
        </div>

        <div v-if="cart.length === 0" class="sheet-empty">
          <ShoppingBag :size="36" :stroke-width="1.5" />
          <p>Keranjang masih kosong</p>
          <span>Tambahkan produk untuk mulai belanja</span>
        </div>

        <div v-else class="sheet-body">
          <div v-for="item in cart" :key="item.product.id" class="si">
            <div class="si-visual" :style="{ background: item.product.bgColor }">
              <span>{{ item.product.emoji }}</span>
            </div>
            <div class="si-content">
              <h4>{{ item.product.name }}</h4>
              <span class="si-price">{{ fmt(item.product.numPrice) }}</span>
            </div>
            <div class="si-actions">
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
              <button class="si-del" @click="removeFromCart(item.product.id)" aria-label="Hapus">
                <Trash2 :size="15" :stroke-width="2" />
              </button>
            </div>
          </div>
        </div>

        <div v-if="cart.length > 0" class="sheet-foot">
          <div class="promo-row">
            <input class="promo-input" type="text" placeholder="Punya kode promo?" v-model="promoCode" />
            <button class="promo-btn" @click="applyPromo">Pakai</button>
          </div>
          <div class="delivery-row">
            <Info :size="14" :stroke-width="2" />
            <span>Pengambilan di koperasi sekolah</span>
            <strong class="delivery-free">Gratis</strong>
          </div>
          <div class="sheet-total-row">
            <span>Total</span>
            <strong>{{ fmt(cartTotal) }}</strong>
          </div>
          <button class="kop-btn primary full" @click="goCheckout">
            Beli Sekarang
          </button>
        </div>
      </div>
    </Transition>

    <!-- ═══ CHECKOUT VIEW ═══ -->
    <div v-if="view === 'checkout'" class="kop-flow">
      <div class="flow-box">
        <div class="flow-topbar">
          <button class="flow-back" @click="view = 'shop'">
            <ChevronLeft :size="18" :stroke-width="2" />
          </button>
          <h2 class="flow-topbar-title">Checkout</h2>
          <div style="width:36px"></div>
        </div>

        <!-- Customer Info -->
        <div class="checkout-section">
          <div class="section-label">Informasi Pengambil</div>
          <div class="info-card">
            <div class="info-card-left">
              <div class="info-avatar">{{ user?.name?.charAt(0) || '?' }}</div>
              <div>
                <strong>{{ user?.name || 'Guest' }}</strong>
                <span>{{ user?.email || '-' }}</span>
              </div>
            </div>
            <ChevronLeft :size="16" :stroke-width="2" class="info-arrow" />
          </div>
        </div>

        <!-- Order Items -->
        <div class="checkout-section">
          <div class="section-label">Pesanan Anda</div>
          <div class="order-items">
            <div v-for="item in cart" :key="item.product.id" class="oi">
              <div class="oi-visual" :style="{ background: item.product.bgColor }">
                <span>{{ item.product.emoji }}</span>
              </div>
              <div class="oi-info">
                <h4>{{ item.product.name }}</h4>
                <span class="oi-meta">{{ item.quantity }} &times; {{ fmt(item.product.numPrice) }}</span>
              </div>
              <strong class="oi-total">{{ fmt(item.product.numPrice * item.quantity) }}</strong>
            </div>
          </div>
        </div>

        <!-- Payment Method -->
        <div class="checkout-section">
          <div class="section-label">Metode Pembayaran</div>
          <div class="pay-options">
            <button
              :class="['pay-opt', { selected: payMethod === 'qris' }]"
              @click="payMethod = 'qris'"
            >
              <div class="po-icon qris-icon">
                <QrCode :size="22" :stroke-width="1.5" />
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
              <div class="po-icon transfer-icon">
                <CreditCard :size="22" :stroke-width="1.5" />
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
        </div>

        <!-- Summary -->
        <div class="checkout-section">
          <div class="summary-row">
            <span>Subtotal ({{ cartCount }} item)</span>
            <span>{{ fmt(cartTotal) }}</span>
          </div>
          <div class="summary-row">
            <span>Biaya layanan</span>
            <span class="summary-free">Gratis</span>
          </div>
          <div class="summary-row total">
            <span>Total</span>
            <strong>{{ fmt(cartTotal) }}</strong>
          </div>
        </div>

        <div class="checkout-footer">
          <span class="checkout-total-label">Total {{ fmt(cartTotal) }}</span>
          <button
            class="kop-btn primary checkout-btn"
            :disabled="!payMethod"
            @click="processPayment"
          >
            Place Order
          </button>
        </div>
      </div>
    </div>

    <!-- ═══ PENDING PAYMENT ═══ -->
    <div v-if="view === 'pending'" class="kop-flow">
      <div class="flow-box flow-center">
        <div class="flow-topbar">
          <div style="width:36px"></div>
          <h2 class="flow-topbar-title">Pembayaran</h2>
          <div style="width:36px"></div>
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
  </section>
</template>

<script setup>
import { ref, computed, onUnmounted } from 'vue'
import {
  ChevronLeft, ShoppingBag, Plus, Minus, X, Trash2,
  ArrowRight, QrCode, CreditCard, Check, Copy,
  Info, Package, Bell
} from 'lucide-vue-next'
import { useAuthSession } from '@/composable/useAuthSession'

const { session } = useAuthSession()
const user = computed(() => session.value)

/* ─── State ─── */
const view = ref('shop')
const activeCat = ref('Semua')
const cartOpen = ref(false)
const showFilter = ref(false)
const payMethod = ref(null)
const isProcessing = ref(false)
const orderId = ref('')
const deadline = ref(null)
const timeLeft = ref({ hours: 0, minutes: 59, seconds: 59 })
const promoCode = ref('')
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
    } else {
      showToast('Stok tidak mencukupi', 'error')
    }
  } else {
    cart.value.push({ product, quantity: 1 })
  }
}

function removeFromCart(id) {
  cart.value = cart.value.filter(i => i.product.id !== id)
  if (cart.value.length === 0) cartOpen.value = false
}

function updateQty(id, delta) {
  const item = cart.value.find(i => i.product.id === id)
  if (!item) return
  const next = item.quantity + delta
  if (next <= 0) { removeFromCart(id); return }
  if (next > item.product.stock) { showToast('Stok tidak mencukupi', 'error'); return }
  item.quantity = next
}

function applyPromo() {
  if (promoCode.value.trim()) {
    showToast('Kode promo tidak valid', 'error')
  }
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

function openCart() {
  cartOpen.value = true
}

function goCheckout() {
  cartOpen.value = false
  orderId.value = genOrderId()
  payMethod.value = null
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
  promoCode.value = ''
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
/* ═══ DESIGN TOKENS ═══ */
.kop {
  --green-900: #0f3d22;
  --green-700: #1a6b3c;
  --green-600: #1f7d46;
  --green-500: #27965a;
  --green-200: #b8dbc6;
  --green-100: #dceee2;
  --green-50:  #f0f7f2;
  --surface:   #f8f9f7;
  --white:     #ffffff;
  --text-1:    #1a2420;
  --text-2:    #4d5f53;
  --text-3:    #7d8f84;
  --amber:     #c77d0a;
  --red:       #c53030;
  --border:    rgba(26, 107, 60, 0.08);
  --shadow-sm: 0 2px 8px rgba(20, 50, 30, 0.04);
  --shadow-md: 0 8px 24px rgba(20, 50, 30, 0.06);
  --shadow-lg: 0 16px 40px rgba(20, 50, 30, 0.1);
  --radius-sm: 12px;
  --radius-md: 16px;
  --radius-lg: 20px;
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
.kop-toast.error { background: var(--red); }
.toast-dot {
  width: 8px; height: 8px; border-radius: 50%;
  background: #4ade80; flex-shrink: 0;
}
.kop-toast.error .toast-dot { background: #fca5a5; }
.toast-enter-active { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
.toast-leave-active { transition: all 0.25s ease; }
.toast-enter-from { opacity: 0; transform: translateX(-50%) translateY(-12px); }
.toast-leave-to   { opacity: 0; transform: translateX(-50%) translateY(-8px); }

/* ═══ SHOP VIEW ═══ */
.kop-shop {
  padding: 0 7% 120px;
  max-width: 1400px;
  margin: 0 auto;
}

.kop-topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 0 16px;
  position: sticky;
  top: 0;
  background: var(--surface);
  z-index: 10;
}
.topbar-title {
  font-size: 18px;
  font-weight: 700;
  color: var(--text-1);
  margin: 0;
}

.kop-back, .kop-cart-topbar {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 44px; height: 44px;
  border: 1px solid var(--border);
  background: var(--white);
  border-radius: var(--radius-md);
  color: var(--green-700);
  cursor: pointer;
  transition: all 0.2s ease;
}
.kop-back:hover, .kop-cart-topbar:hover {
  background: var(--green-50);
  border-color: var(--green-200);
}
.kop-back:active, .kop-cart-topbar:active { transform: scale(0.96); }
.cart-badge {
  position: absolute; top: -6px; right: -6px;
  min-width: 20px; height: 20px; padding: 0 6px;
  border-radius: var(--radius-pill);
  background: var(--green-700);
  color: #fff; font-size: 11px; font-weight: 700;
  display: flex; align-items: center; justify-content: center;
}

.kop-stats {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}
.stat-count {
  font-size: 14px;
  font-weight: 600;
  color: var(--text-2);
}
.stat-filter {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 36px; height: 36px;
  border: 1px solid var(--border);
  background: var(--white);
  border-radius: var(--radius-sm);
  color: var(--text-2);
  cursor: pointer;
}

/* ═══ CATEGORY TABS ═══ */
.kop-tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 24px;
}
.kop-tab {
  padding: 8px 16px;
  border: 1.5px solid var(--border);
  border-radius: var(--radius-pill);
  background: var(--white);
  color: var(--text-2);
  font-family: inherit;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}
.kop-tab:hover { border-color: var(--green-200); color: var(--green-700); }
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
  gap: 16px;
}

.kop-card {
  border-radius: var(--radius-lg);
  overflow: hidden;
  background: var(--white);
  box-shadow: var(--shadow-sm);
  cursor: pointer;
  transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s ease;
}
.kop-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-md);
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
  font-size: 52px;
  line-height: 1;
  filter: drop-shadow(0 2px 6px rgba(0,0,0,0.08));
}
.card-new {
  position: absolute;
  top: 10px; left: 10px;
  padding: 4px 10px;
  border-radius: var(--radius-pill);
  background: var(--amber);
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.card-body {
  padding: 14px 16px 16px;
}
.card-name {
  margin: 0 0 8px;
  font-size: 14px;
  font-weight: 700;
  color: var(--text-1);
  line-height: 1.3;
}
.card-foot {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.card-price {
  font-size: 15px;
  font-weight: 800;
  color: var(--green-700);
}
.card-add-inline {
  width: 32px; height: 32px;
  border: none;
  border-radius: var(--radius-sm);
  background: var(--green-700);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 2px 8px rgba(26, 107, 60, 0.25);
}
.card-add-inline:hover { background: var(--green-600); transform: scale(1.05); }
.card-add-inline:active { transform: scale(0.92); }
.card-add-inline:disabled {
  background: var(--text-3);
  cursor: not-allowed;
  box-shadow: none;
}

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

/* ═══ FLOATING CART ═══ */
.kop-float {
  position: fixed;
  bottom: 28px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 100;
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 24px;
  border: none;
  border-radius: var(--radius-pill);
  background: var(--green-900);
  color: #fff;
  font-family: inherit;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 8px 32px rgba(15, 61, 34, 0.35);
  transition: all 0.2s ease;
}
.kop-float:hover { transform: translateX(-50%) translateY(-2px); box-shadow: 0 12px 40px rgba(15, 61, 34, 0.4); }
.kop-float:active { transform: translateX(-50%) scale(0.97); }
.float-sep { width: 1px; height: 18px; background: rgba(255,255,255,0.25); }
.float-count { opacity: 0.8; font-weight: 500; }
.float-total { color: #4ade80; }
.float-up-enter-active { transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1); }
.float-up-leave-active { transition: all 0.25s ease; }
.float-up-enter-from { opacity: 0; transform: translateX(-50%) translateY(20px); }
.float-up-leave-to   { opacity: 0; transform: translateX(-50%) translateY(12px); }

/* ═══ CART SHEET (slide-up) ═══ */
.kop-overlay {
  position: fixed;
  inset: 0;
  z-index: 900;
  background: rgba(15, 30, 20, 0.45);
  backdrop-filter: blur(4px);
}
.overlay-fade-enter-active,
.overlay-fade-leave-active { transition: opacity 0.3s ease; }
.overlay-fade-enter-from,
.overlay-fade-leave-to { opacity: 0; }

.kop-sheet {
  position: fixed;
  bottom: 0; left: 0; right: 0;
  z-index: 950;
  max-height: 85vh;
  background: var(--white);
  border-radius: var(--radius-lg) var(--radius-lg) 0 0;
  display: flex;
  flex-direction: column;
  box-shadow: 0 -8px 40px rgba(15, 30, 20, 0.12);
}
.slide-up-enter-active { transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1); }
.slide-up-leave-active { transition: transform 0.25s ease; }
.slide-up-enter-from,
.slide-up-leave-to { transform: translateY(100%); }

.sheet-handle {
  display: flex;
  justify-content: center;
  padding: 12px 0 4px;
  cursor: pointer;
}
.handle-bar {
  width: 40px; height: 4px;
  border-radius: 2px;
  background: #d1d5db;
}

.sheet-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 24px 16px;
}
.sheet-head h2 {
  margin: 0;
  font-size: 20px;
  font-weight: 700;
}
.sheet-close {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 36px; height: 36px;
  border: none;
  border-radius: var(--radius-sm);
  background: var(--green-50);
  color: var(--text-2);
  cursor: pointer;
}

.sheet-empty {
  text-align: center;
  padding: 48px 24px;
  color: var(--text-3);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}
.sheet-empty p { margin: 0; font-size: 16px; font-weight: 600; color: var(--text-2); }
.sheet-empty span { font-size: 13px; }

.sheet-body {
  flex: 1;
  overflow-y: auto;
  padding: 0 24px;
}

.si {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px 0;
  border-bottom: 1px solid var(--border);
}
.si:last-child { border-bottom: none; }
.si-visual {
  width: 60px; height: 60px;
  border-radius: var(--radius-md);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  flex-shrink: 0;
}
.si-content {
  flex: 1;
  min-width: 0;
}
.si-content h4 {
  margin: 0 0 4px;
  font-size: 14px;
  font-weight: 600;
  color: var(--text-1);
}
.si-price {
  font-size: 14px;
  font-weight: 700;
  color: var(--green-700);
}
.si-actions {
  display: flex;
  align-items: center;
  gap: 10px;
}

.qty-control {
  display: flex;
  align-items: center;
  gap: 0;
  border: 1.5px solid var(--border);
  border-radius: var(--radius-sm);
  overflow: hidden;
}
.qty-btn {
  width: 32px; height: 32px;
  border: none;
  background: var(--green-50);
  color: var(--green-700);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.15s;
}
.qty-btn:hover { background: var(--green-100); }
.qty-btn:disabled { opacity: 0.3; cursor: not-allowed; }
.qty-num {
  min-width: 32px;
  text-align: center;
  font-size: 14px;
  font-weight: 700;
}

.si-del {
  width: 32px; height: 32px;
  border: none;
  border-radius: var(--radius-sm);
  background: transparent;
  color: var(--text-3);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.15s;
}
.si-del:hover { background: #fef2f2; color: var(--red); }

.sheet-foot {
  padding: 16px 24px 28px;
  border-top: 1px solid var(--border);
}

.promo-row {
  display: flex;
  gap: 8px;
  margin-bottom: 12px;
}
.promo-input {
  flex: 1;
  padding: 10px 14px;
  border: 1.5px solid var(--border);
  border-radius: var(--radius-sm);
  font-family: inherit;
  font-size: 13px;
  background: var(--green-50);
  outline: none;
  transition: border-color 0.2s;
}
.promo-input:focus { border-color: var(--green-500); }
.promo-input::placeholder { color: var(--text-3); }
.promo-btn {
  padding: 10px 18px;
  border: 1.5px solid var(--green-700);
  border-radius: var(--radius-sm);
  background: transparent;
  color: var(--green-700);
  font-family: inherit;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
}
.promo-btn:hover { background: var(--green-50); }

.delivery-row {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 14px;
  border-radius: var(--radius-sm);
  background: var(--green-50);
  font-size: 13px;
  color: var(--text-2);
  margin-bottom: 12px;
}
.delivery-free {
  margin-left: auto;
  color: var(--green-700);
  font-size: 13px;
}

.sheet-total-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  font-size: 16px;
}
.sheet-total-row span { color: var(--text-2); }
.sheet-total-row strong { font-size: 18px; color: var(--text-1); }

/* ═══ BUTTONS ═══ */
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
}
.kop-btn.primary {
  background: var(--green-700);
  color: #fff;
  box-shadow: 0 4px 16px rgba(26, 107, 60, 0.25);
}
.kop-btn.primary:hover { background: var(--green-600); transform: translateY(-1px); }
.kop-btn.primary:active { transform: translateY(0) scale(0.98); }
.kop-btn.primary:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }
.kop-btn.full { width: 100%; }

.btn-spin {
  width: 18px; height: 18px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ═══ FLOW VIEWS ═══ */
.kop-flow {
  min-height: 100vh;
  min-height: 100dvh;
  display: flex;
  justify-content: center;
  padding: 0 7%;
}
.flow-box {
  width: 100%;
  max-width: 520px;
  padding: 20px 0 40px;
}
.flow-center {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.flow-topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 28px;
}
.flow-topbar-title {
  font-size: 18px;
  font-weight: 700;
  margin: 0;
}
.flow-back {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 36px; height: 36px;
  border: 1px solid var(--border);
  background: var(--white);
  border-radius: var(--radius-sm);
  color: var(--green-700);
  cursor: pointer;
  transition: all 0.2s ease;
}
.flow-back:hover { background: var(--green-50); }

/* ═══ CHECKOUT ═══ */
.checkout-section {
  margin-bottom: 24px;
}
.section-label {
  font-size: 13px;
  font-weight: 700;
  color: var(--text-3);
  text-transform: uppercase;
  letter-spacing: 0.06em;
  margin-bottom: 12px;
}

.info-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 16px;
  background: var(--white);
  border-radius: var(--radius-md);
  border: 1px solid var(--border);
}
.info-card-left {
  display: flex;
  align-items: center;
  gap: 12px;
}
.info-avatar {
  width: 40px; height: 40px;
  border-radius: 50%;
  background: var(--green-100);
  color: var(--green-700);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  font-weight: 700;
}
.info-card-left strong {
  display: block;
  font-size: 14px;
  color: var(--text-1);
}
.info-card-left span {
  font-size: 12px;
  color: var(--text-3);
}
.info-arrow { color: var(--text-3); transform: rotate(180deg); }

.order-items {
  background: var(--white);
  border-radius: var(--radius-md);
  border: 1px solid var(--border);
  overflow: hidden;
}
.oi {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 16px;
}
.oi + .oi { border-top: 1px solid var(--border); }
.oi-visual {
  width: 52px; height: 52px;
  border-radius: var(--radius-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  flex-shrink: 0;
}
.oi-info { flex: 1; min-width: 0; }
.oi-info h4 {
  margin: 0 0 2px;
  font-size: 14px;
  font-weight: 600;
  color: var(--text-1);
}
.oi-meta {
  font-size: 12px;
  color: var(--text-3);
}
.oi-total {
  font-size: 14px;
  font-weight: 700;
  color: var(--text-1);
}

.pay-options {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.pay-opt {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px 16px;
  background: var(--white);
  border: 2px solid var(--border);
  border-radius: var(--radius-md);
  cursor: pointer;
  transition: all 0.2s ease;
  text-align: left;
}
.pay-opt:hover { border-color: var(--green-200); }
.pay-opt.selected { border-color: var(--green-700); background: var(--green-50); }

.po-icon {
  width: 44px; height: 44px;
  border-radius: var(--radius-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.qris-icon { background: #eef2ff; color: #4f46e5; }
.transfer-icon { background: #fef3c7; color: #d97706; }

.po-text { flex: 1; }
.po-text h4 {
  margin: 0 0 2px;
  font-size: 14px;
  font-weight: 700;
  color: var(--text-1);
}
.po-text span {
  font-size: 12px;
  color: var(--text-3);
}

.po-check {
  width: 28px; height: 28px;
  border-radius: 50%;
  background: var(--green-700);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
  font-size: 14px;
  color: var(--text-2);
}
.summary-row + .summary-row { border-top: 1px solid var(--border); }
.summary-free { color: var(--green-700); font-weight: 600; }
.summary-row.total {
  border-top: 2px solid var(--text-1);
  margin-top: 4px;
  padding-top: 14px;
  font-size: 16px;
  color: var(--text-1);
}
.summary-row.total strong { font-size: 18px; }

.checkout-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 20px 0;
  border-top: 1px solid var(--border);
  margin-top: 8px;
  position: sticky;
  bottom: 0;
  background: var(--surface);
}
.checkout-total-label {
  font-size: 14px;
  font-weight: 700;
  color: var(--text-1);
}
.checkout-btn {
  flex: 1;
  max-width: 240px;
  padding: 14px 32px;
  border: none;
  border-radius: var(--radius-pill);
  background: var(--green-900);
  color: #fff;
  font-family: inherit;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
}
.checkout-btn:hover { background: var(--green-700); }
.checkout-btn:disabled { opacity: 0.4; cursor: not-allowed; }

/* ═══ PENDING PAYMENT ═══ */
.pend-card {
  width: 100%;
  background: var(--white);
  border-radius: var(--radius-lg);
  border: 1px solid var(--border);
  overflow: hidden;
  margin-bottom: 24px;
}
.pend-head {
  padding: 20px 24px;
  border-bottom: 1px solid var(--border);
}
.pend-head h3 { margin: 0 0 6px; font-size: 18px; font-weight: 700; }
.pend-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: var(--radius-pill);
  background: #fef3c7;
  color: #92400e;
  font-size: 12px;
  font-weight: 600;
}
.pend-body { padding: 24px; }

.qr-wrap { display: flex; justify-content: center; margin-bottom: 20px; }
.qr-code {
  width: 180px; height: 180px;
  background: #fff;
  border: 2px solid #e5e7eb;
  border-radius: 16px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  position: relative;
}
.qr-inner {
  width: 120px; height: 120px;
  position: relative;
}
.qr-block {
  position: absolute;
  width: 28px; height: 28px;
  border: 4px solid #1a2420;
  border-radius: 4px;
}
.qr-block.tl { top: 0; left: 0; }
.qr-block.tr { top: 0; right: 0; }
.qr-block.bl { bottom: 0; left: 0; }
.qr-dots {
  position: absolute;
  inset: 0;
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  grid-template-rows: repeat(5, 1fr);
  gap: 4px;
  padding: 32px;
}
.qr-dot {
  width: 100%; height: 100%;
  background: #1a2420;
  border-radius: 2px;
}
.qr-brand {
  margin-top: 8px;
  font-size: 11px;
  font-weight: 800;
  color: var(--text-3);
  letter-spacing: 0.1em;
}

.pend-amount {
  text-align: center;
  font-size: 24px;
  font-weight: 800;
  color: var(--text-1);
  margin: 0 0 8px;
}
.pend-hint {
  text-align: center;
  font-size: 13px;
  color: var(--text-3);
  margin: 0;
}

.bank-details {
  background: var(--green-50);
  border-radius: var(--radius-md);
  overflow: hidden;
}
.bank-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 16px;
}
.bank-row + .bank-row { border-top: 1px solid var(--border); }
.bank-label {
  font-size: 13px;
  color: var(--text-3);
}
.bank-val {
  display: flex;
  align-items: center;
  gap: 10px;
}
.bank-val strong { font-size: 14px; }
.bank-amount { color: var(--green-700); }
.bank-copy { cursor: pointer; }
.bank-copy:hover { background: rgba(0,0,0,0.02); }
.copy-tag {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 4px 10px;
  border-radius: var(--radius-pill);
  background: var(--green-100);
  color: var(--green-700);
  font-size: 11px;
  font-weight: 600;
  cursor: pointer;
}

.pend-timer {
  padding: 20px 24px;
  border-top: 1px solid var(--border);
  text-align: center;
}
.timer-label {
  font-size: 13px;
  color: var(--text-3);
  display: block;
  margin-bottom: 12px;
}
.timer-row {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-bottom: 12px;
}
.timer-cell {
  display: flex;
  flex-direction: column;
  align-items: center;
}
.timer-num {
  width: 52px; height: 52px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: var(--radius-sm);
  background: var(--green-50);
  font-size: 22px;
  font-weight: 800;
  color: var(--green-900);
}
.timer-unit {
  font-size: 11px;
  color: var(--text-3);
  margin-top: 4px;
}
.timer-colon {
  font-size: 22px;
  font-weight: 800;
  color: var(--text-3);
  padding-bottom: 18px;
}
.timer-warn {
  font-size: 12px;
  color: var(--amber);
  margin: 0;
}

/* ═══ SUCCESS VIEW ═══ */
.suc-circle {
  width: 88px; height: 88px;
  border-radius: 50%;
  background: var(--green-100);
  color: var(--green-700);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 24px;
  animation: pop-in 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes pop-in {
  from { transform: scale(0.6); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
.suc-title {
  font-size: 24px;
  font-weight: 800;
  color: var(--text-1);
  margin: 0 0 8px;
  text-align: center;
}
.suc-sub {
  font-size: 14px;
  color: var(--text-3);
  margin: 0 0 28px;
  text-align: center;
}

.suc-card {
  width: 100%;
  background: var(--white);
  border-radius: var(--radius-md);
  border: 1px solid var(--border);
  overflow: hidden;
  margin-bottom: 20px;
}
.suc-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 20px;
}
.suc-row + .suc-row { border-top: 1px solid var(--border); }
.suc-row span { font-size: 13px; color: var(--text-3); }
.suc-row strong { font-size: 14px; color: var(--text-1); }
.suc-amount { color: var(--green-700) !important; font-size: 16px !important; }
.suc-status {
  display: inline-block;
  padding: 4px 12px;
  border-radius: var(--radius-pill);
  background: var(--green-100);
  color: var(--green-700);
  font-size: 12px;
  font-weight: 700;
}

.suc-notif {
  display: flex;
  gap: 14px;
  padding: 16px;
  background: var(--green-50);
  border-radius: var(--radius-md);
  border: 1px solid var(--border);
  margin-bottom: 24px;
  width: 100%;
}
.sn-icon {
  width: 40px; height: 40px;
  border-radius: 50%;
  background: var(--green-100);
  color: var(--green-700);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.sn-text strong {
  display: block;
  font-size: 13px;
  color: var(--text-1);
  margin-bottom: 4px;
}
.sn-text p {
  margin: 0;
  font-size: 12px;
  color: var(--text-3);
  line-height: 1.5;
}

/* ═══ RESPONSIVE ═══ */
@media (max-width: 1024px) {
  .kop-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 768px) {
  .kop-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
  .kop-shop { padding: 0 5% 120px; }
  .kop-float { bottom: 20px; padding: 12px 20px; font-size: 13px; }
}
@media (max-width: 520px) {
  .kop-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
  .card-emoji { font-size: 40px; }
  .card-body { padding: 10px 12px 12px; }
  .card-name { font-size: 13px; }
  .card-price { font-size: 13px; }
  .sheet-foot { padding: 14px 20px 24px; }
  .flow-box { padding: 16px 0 32px; }
  .checkout-footer { flex-direction: column; gap: 12px; }
  .checkout-btn { max-width: 100%; }
}
</style>
