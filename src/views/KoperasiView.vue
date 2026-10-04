<template>
  <section class="koperasi-page">
    <Transition name="fade-up">
      <div v-if="toast.show" class="kop-toast">{{ toast.message }}</div>
    </Transition>

    <!-- ═══ SHOP VIEW ═══ -->
    <div v-if="view === 'shop'">
      <header class="kop-topbar">
        <div class="kop-topbar-left">
          <button class="kop-back" @click="goBack" aria-label="Kembali">
            <ChevronLeft :size="18" :stroke-width="2.5" />
          </button>
          <img src="/logo.webp" alt="Logo Sekolah" class="kop-logo" />
          <span class="kop-brand">Koperasi</span>
        </div>
        <button class="kop-cart-btn" @click="openCart" aria-label="Buka keranjang">
          <ShoppingBag :size="18" :stroke-width="2" />
          <span>Keranjang</span>
          <span v-if="cartCount > 0" class="cart-count">{{ cartCount }}</span>
        </button>
      </header>

      <div class="kop-header">
        <h1>Koperasi Siswa</h1>
        <p>Belanja kebutuhan sekolah — seragam, alat tulis, dan perlengkapan siswa.</p>
      </div>

      <div class="filter-row">
        <div class="filter-pills">
          <button
            v-for="cat in categories"
            :key="cat"
            :class="['pill', { active: activeCategory === cat }]"
            @click="activeCategory = cat"
          >
            {{ cat }}
          </button>
        </div>
        <span class="product-count">{{ filteredProducts.length }} Produk</span>
      </div>

      <div class="produk-grid">
        <article
          v-for="item in filteredProducts"
          :key="item.id"
          class="produk-card"
          @click="openDetail(item)"
        >
          <div class="produk-image">
            <img v-if="item.image" :src="item.image" :alt="item.title" loading="lazy" class="produk-img" />
            <span v-else class="produk-emoji">{{ item.emoji }}</span>
          </div>
          <div class="produk-info">
            <div class="produk-info-left">
              <h3>{{ item.title }}</h3>
              <p>{{ item.desc }}</p>
              <span class="produk-price">{{ fmt(item.price) }}</span>
            </div>
            <button
              class="produk-add"
              :disabled="item.stock === 0"
              @click.stop="addToCart(item)"
              :aria-label="'Tambah ' + item.title + ' ke keranjang'"
            >
              <Plus :size="16" :stroke-width="2.5" />
            </button>
          </div>
        </article>
      </div>

      <div v-if="filteredProducts.length === 0" class="empty-state">
        Tidak ada produk di kategori ini.
      </div>
    </div>

    <!-- ═══ CART DRAWER (slide from right) ═══ -->
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
            <div v-for="item in cart" :key="item.product.id" class="si">
              <div class="si-visual">
                <img v-if="item.product.image" :src="item.product.image" :alt="item.product.title" class="si-img" />
                <span v-else>{{ item.product.emoji }}</span>
              </div>
              <div class="si-content">
                <h4>{{ item.product.title }}</h4>
                <span class="si-price">{{ fmt(item.product.price) }}</span>
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

          <div v-if="cart.length > 0" class="drawer-foot">
            <div class="delivery-row">
              <Info :size="14" :stroke-width="2" />
              <span>Pengambilan di koperasi sekolah</span>
              <strong class="delivery-free">Gratis</strong>
            </div>
            <div class="total-row">
              <span>Total</span>
              <strong>{{ fmt(cartTotal) }}</strong>
            </div>
            <button class="kop-btn primary full" @click="goCheckout">Beli Sekarang</button>
          </div>
        </aside>
      </div>
    </Transition>

    <!-- ═══ CHECKOUT VIEW ═══ -->
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
            <div v-for="item in cart" :key="item.product.id" class="oi">
              <div class="oi-visual">
                <img v-if="item.product.image" :src="item.product.image" :alt="item.product.title" class="si-img" />
                <span v-else>{{ item.product.emoji }}</span>
              </div>
              <div class="oi-info">
                <h4>{{ item.product.title }}</h4>
                <span class="oi-meta">{{ item.quantity }} &times; {{ fmt(item.product.price) }}</span>
              </div>
              <strong class="oi-total">{{ fmt(item.product.price * item.quantity) }}</strong>
            </div>
          </div>
        </div>

        <div class="checkout-section">
          <div class="section-label">Metode Pembayaran</div>
          <div class="pay-options">
            <button
              :class="['pay-opt', { selected: payMethod === 'qris' }]"
              @click="payMethod = 'qris'"
            >
              <div class="po-icon qris-icon"><QrCode :size="22" :stroke-width="1.5" /></div>
              <div class="po-text">
                <h4>QRIS</h4>
                <span>Scan QR untuk bayar instan</span>
              </div>
              <div v-if="payMethod === 'qris'" class="po-check"><Check :size="16" :stroke-width="3" /></div>
            </button>
            <button
              :class="['pay-opt', { selected: payMethod === 'transfer' }]"
              @click="payMethod = 'transfer'"
            >
              <div class="po-icon transfer-icon"><CreditCard :size="22" :stroke-width="1.5" /></div>
              <div class="po-text">
                <h4>Transfer Bank</h4>
                <span>Transfer ke rekening koperasi</span>
              </div>
              <div v-if="payMethod === 'transfer'" class="po-check"><Check :size="16" :stroke-width="3" /></div>
            </button>
          </div>
        </div>

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
          <button class="kop-btn primary checkout-btn" :disabled="!payMethod" @click="processPayment">
            Bayar Sekarang
          </button>
        </div>
      </div>
    </div>

    <!-- ═══ PENDING PAYMENT ═══ -->
    <div v-if="view === 'pending'" class="flow-wrap">
      <div class="flow-box">
        <div class="flow-topbar flow-center-head">
          <div class="flow-spacer"></div>
          <h2>Pembayaran</h2>
          <div class="flow-spacer"></div>
        </div>

        <div class="pend-card">
          <div class="pend-head">
            <h3>{{ payMethod === 'qris' ? 'Scan QRIS' : 'Transfer Bank' }}</h3>
            <span class="pend-badge">Menunggu Pembayaran</span>
          </div>

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
                  <span class="copy-tag"><Copy :size="13" :stroke-width="2" /> Salin</span>
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
                  <span class="copy-tag"><Copy :size="13" :stroke-width="2" /> Salin</span>
                </div>
              </div>
            </div>
          </div>

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

        <button class="kop-btn primary full" :disabled="isProcessing" @click="confirmPayment">
          <span v-if="isProcessing" class="btn-spin"></span>
          {{ isProcessing ? 'Memverifikasi...' : 'Saya Sudah Bayar' }}
        </button>
      </div>
    </div>

    <!-- ═══ SUCCESS VIEW ═══ -->
    <div v-if="view === 'success'" class="flow-wrap">
      <div class="flow-box flow-center">
        <div class="suc-circle"><Check :size="44" :stroke-width="2.5" /></div>
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
          <div class="sn-icon"><Bell :size="20" :stroke-width="2" /></div>
          <div class="sn-text">
            <strong>Penjaga koperasi telah diberitahu</strong>
            <p>Pesanan Anda akan segera disiapkan. Silakan ambil di koperasi sekolah saat jam istirahat.</p>
          </div>
        </div>

        <button class="kop-btn primary full" @click="backToShop">Kembali Belanja</button>
      </div>
    </div>

    <!-- ═══ PRODUCT DETAIL MODAL ═══ -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="selected" class="modal-overlay" @click.self="closeDetail">
          <div class="modal-content">
            <button class="modal-close" @click="closeDetail">&times;</button>
            <div class="modal-image">
              <img v-if="selected.image" :src="selected.image" :alt="selected.title" class="modal-img" />
              <span v-else class="modal-emoji">{{ selected.emoji }}</span>
            </div>
            <div class="modal-body">
              <span class="modal-category">{{ selected.category }}</span>
              <h2>{{ selected.title }}</h2>
              <p class="modal-price">{{ fmt(selected.price) }}</p>
              <p class="modal-desc">{{ selected.fullDesc }}</p>
              <div class="modal-stock">
                <strong>Stok:</strong> {{ selected.stock }} tersedia
              </div>
              <button
                class="kop-btn primary full modal-add"
                :disabled="selected.stock === 0"
                @click="addToCart(selected)"
              >
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
import { ref, computed, onUnmounted } from "vue";
import { Plus, Minus, X, Trash2, ShoppingBag, ChevronLeft, QrCode, CreditCard, Check, Copy, Info, Bell } from "lucide-vue-next";
import { useAuthSession } from "@/composable/useAuthSession";
import { getCsrfToken } from "@/services/csrf";

const { BACKEND } = useAuthSession();

const activeCategory = ref("Semua");
const selected = ref(null);

/* ─── Cart & checkout state ─── */
const view = ref("shop");
const cartOpen = ref(false);
const payMethod = ref(null);
const isProcessing = ref(false);
const orderId = ref("");
const deadline = ref(null);
const timeLeft = ref({ hours: 0, minutes: 0, seconds: 0 });
let timerInterval = null;

const toast = ref({ show: false, message: "", type: "success" });
let toastTimer = null;

const cart = ref([]);

const categories = ["Semua", "Seragam", "Alat Tulis", "Aksesoris", "Minuman"];

const products = [
  {
    id: 1,
    title: "Seragam Putih",
    desc: "Kaos seragam putih lengan pendek bahan katun premium.",
    category: "Seragam",
    price: 75000,
    stock: 48,
    emoji: "👕",
    image: "/produk/buku.jpg",
    fullDesc: "Seragam putih standar SMK Bahrul Ulum dengan bordir logo sekolah di dada kiri. Bahan cotton combed 30s, adem, tidak gampang kusut. Tersedia ukuran S-XXL."
  },
  {
    id: 2,
    title: "Rok/Celana Biru Tua",
    desc: "Rok plisket putri & celana panjang putra warna navy.",
    category: "Seragam",
    price: 85000,
    stock: 42,
    emoji: "👖",
    image: "/produk/pensil.jpg",
    fullDesc: "Rok plisket putri (panjang lutut) dan celana panjang putra bahan polyester wol premium. Warna biru tua (navy) standar sekolah. Jahitan rapi, tahan lama."
  },
  {
    id: 3,
    title: "Jaket Almamater",
    desc: "Jaket bomber almamater dengan bordir logo sekolah.",
    category: "Seragam",
    price: 185000,
    stock: 15,
    emoji: "🧥",
    image: "/produk/bis.jpg",
    fullDesc: "Jaket almamater model bomber bahan parachute/softshell, doublé fleece hangat. Bordir logo sekolah dada kiri, angkatan di lengan kanan, nama di dada kanan. Warna hitam/navy. Pre-order minimal 10 pcs."
  },
  {
    id: 4,
    title: "Buku Tulis Sekolah",
    desc: "Buku tulis 38 lembar, cover bergambar logo sekolah.",
    category: "Alat Tulis",
    price: 8000,
    stock: 120,
    emoji: "📓",
    image: "/produk/sabuk.jpg",
    fullDesc: "Buku tulis standar sekolah 38 lembar (76 halaman), kertas HVS 70gsm putih bersih. Cover karton 260gsm dengan logo sekolah. Pilihan: garis (SD/SMP) atau berkotak 5mm (SMK/Math)."
  },
  {
    id: 5,
    title: "Pensil 2B & Penghapus",
    desc: "Set pensil 2B kayu berkualitas + penghapus non-debu.",
    category: "Alat Tulis",
    price: 5000,
    stock: 200,
    emoji: "✏️",
    image: "/produk/topi.jpg",
    fullDesc: "Pensil kayu 2B standar nasional (SNI), grafis halus, mudah dikoreksi. Penghapus karet vinyl putih non-debu, tidak mengotorkan kertas. Dijual per set (3 pensil + 1 penghapus)."
  },
  {
    id: 6,
    title: "Pulpen Gel Hitam 0.5mm",
    desc: "Pulpen gel tinta hitam cepat kering, tidak bocor.",
    category: "Alat Tulis",
    price: 3000,
    stock: 300,
    emoji: "🖊️",
    image: "/produk/teh.jpg",
    fullDesc: "Pulpen gel tipe jarum 0.5mm, tinta berbasis air quick-dry, anti macet. Grip karet ergonomis. Cocok untuk catatan harian, ujian, dan penandatanganan dokumen."
  },
  {
    id: 7,
    title: "Sabuk Sekolah Hitam",
    desc: "Sabuk kulit sintetis hitam lebar 3cm, buckle anti karat.",
    category: "Aksesoris",
    price: 25000,
    stock: 60,
    emoji: "🧢",
    image: "/produk/dasi.jpeg",
    fullDesc: "Sabuk sekolah standar lebar 3 cm, bahan PU premium kuat & fleksibel. Buckle logam matte anti gores. Lubang presisi, potong sesuai pinggang (tersedia 80-110 cm). Wajib seragam harian."
  },
  {
    id: 8,
    title: "Dasi Sekolah Merah",
    desc: "Dasi polyester merah marun dengan logo sekolah.",
    category: "Aksesoris",
    price: 20000,
    stock: 55,
    emoji: "👔",
    image: "/produk/sepatu.jpg",
    fullDesc: "Dasi seragam sekolah warna merah marun (maroon) dengan motif logo SMK Bahrul Ulum tenun halus. Panjang 140 cm, lebar 7 cm. Bahan polyester twill anti kusut, bentuk tetap rapi."
  },
  {
    id: 9,
    title: "Topi Sekolah",
    desc: "Topi baseball hitam/navy bordir logo sekolah, adjustable.",
    category: "Aksesoris",
    price: 35000,
    stock: 40,
    emoji: "🧢",
    image: "/produk/hasduk.jpg",
    fullDesc: "Topi model baseball 6 panel, bahan twill cotton polyester. Bordir logo sekolah depan, strap buckle adjustable di belakang. Sirkulasi udara lubang bordir. Cocok olahraga & kegiatan outdoor."
  },
  {
    id: 10,
    title: "Air Mineral 600ml",
    desc: "Air mineral botol 600ml, segar & higienis.",
    category: "Minuman",
    price: 4000,
    stock: 100,
    emoji: "💧",
    image: "/produk/air.jpg",
    fullDesc: "Air mineral berkualitas, sumber mata air terlindungi, proses filtrasi multi-stage. Botol PET BPA-free 600ml, praktis dibawa ke kelas & lapangan. Harga koperasi lebih murah dari pasar."
  },
  {
    id: 11,
    title: "Teh Botol 350ml",
    desc: "Teh manis botol 350ml rasa jasmine, manis pas.",
    category: "Minuman",
    price: 5000,
    stock: 80,
    emoji: "🍵",
    image: "/produk/ser.jpg",
    fullDesc: "Teh siap minum rasa jasmine premium, gula aren asli, tanpa pengawet berbahaya. Botol 350ml praktis ukuran segelas. Cocok teman makan siang di kantin."
  },
  {
    id: 12,
    title: "Kaos Kaki Putih Sekolah",
    desc: "Kaos kaki putih cotton stretch, tinggi mata kaki standar.",
    category: "Aksesoris",
    price: 12000,
    stock: 90,
    emoji: "🧦",
    image: "/produk/kaoskaki.jpg",
    fullDesc: "Kaos kaki putih sekolah bahan cotton combed + spandex, elastis mengikuti kaki. Tinggi mata kaki 15 cm (standar seragam). Anti pelit, menyerap keringat, tahan lama dicuci berulang."
  },
];

/* ─── Cart computed ─── */
const filteredProducts = computed(() => {
  if (activeCategory.value === "Semua") return products;
  return products.filter((p) => p.category === activeCategory.value);
});

const cartCount = computed(() => cart.value.reduce((s, i) => s + i.quantity, 0));
const cartTotal = computed(() => cart.value.reduce((s, i) => s + i.product.price * i.quantity, 0));

/* ─── Cart actions ─── */
function addToCart(product) {
  if (product.stock === 0) return;
  const existing = cart.value.find((i) => i.product.id === product.id);
  if (existing) {
    if (existing.quantity < product.stock) {
      existing.quantity++;
      showToast("Ditambahkan ke keranjang");
    } else {
      showToast("Stok tidak mencukupi", "error");
    }
  } else {
    cart.value.push({ product, quantity: 1 });
    showToast("Ditambahkan ke keranjang");
  }
}

function removeFromCart(id) {
  cart.value = cart.value.filter((i) => i.product.id !== id);
  if (cart.value.length === 0) cartOpen.value = false;
}

function updateQty(id, delta) {
  const item = cart.value.find((i) => i.product.id === id);
  if (!item) return;
  const next = item.quantity + delta;
  if (next <= 0) { removeFromCart(id); return; }
  if (next > item.product.stock) { showToast("Stok tidak mencukupi", "error"); return; }
  item.quantity = next;
}

/* ─── Checkout flow ─── */
function openCart() {
  cartOpen.value = true;
}

function goCheckout() {
  cartOpen.value = false;
  orderId.value = genOrderId();
  payMethod.value = null;
  view.value = "checkout";
}

function processPayment() {
  view.value = "pending";
  startTimer();
}

async function confirmPayment() {
  isProcessing.value = true;
  try {
    const csrf = await getCsrfToken();
    const res = await fetch(`${BACKEND}/koperasi/checkout`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrf,
        "X-Requested-With": "XMLHttpRequest",
      },
      credentials: "same-origin",
      body: JSON.stringify({
        items: cart.value.map((i) => ({
          id: i.product.id,
          title: i.product.title,
          qty: i.quantity,
          price: i.product.price,
        })),
        metode: payMethod.value,
      }),
    });

    if (!res.ok) {
      const data = await res.json().catch(() => ({}));
      throw new Error(data.message || "Gagal menyimpan pesanan.");
    }
  } catch (e) {
    showToast(e.message || "Gagal memproses pembayaran.", "error");
    isProcessing.value = false;
    view.value = "checkout";
    return;
  }

  isProcessing.value = false;
  if (timerInterval) clearInterval(timerInterval);
  view.value = "success";
  showToast("Pembayaran berhasil dikonfirmasi!");
}

function backToShop() {
  cart.value = [];
  payMethod.value = null;
  orderId.value = "";
  isProcessing.value = false;
  view.value = "shop";
}

function startTimer() {
  if (timerInterval) clearInterval(timerInterval);
  deadline.value = Date.now() + 3600000;
  const tick = () => {
    const rem = deadline.value - Date.now();
    if (rem <= 0) {
      clearInterval(timerInterval);
      showToast("Batas waktu habis, pembayaran dibatalkan", "error");
      backToShop();
      return;
    }
    timeLeft.value = {
      hours: Math.floor(rem / 3600000),
      minutes: Math.floor((rem % 3600000) / 60000),
      seconds: Math.floor((rem % 60000) / 1000),
    };
  };
  tick();
  timerInterval = setInterval(tick, 1000);
}

/* ─── Helpers ─── */
function fmt(n) { return "Rp " + n.toLocaleString("id-ID"); }
function pad(n) { return String(n).padStart(2, "0"); }

function genOrderId() {
  const d = new Date();
  return `KOP-${d.getFullYear()}${pad(d.getMonth() + 1)}${pad(d.getDate())}-${Math.floor(Math.random() * 9000) + 1000}`;
}

function showToast(message, type = "success") {
  if (toastTimer) clearTimeout(toastTimer);
  toast.value = { show: true, message, type };
  toastTimer = setTimeout(() => { toast.value.show = false; }, 2500);
}

async function copyText(text) {
  try {
    await navigator.clipboard.writeText(text);
    showToast("Berhasil disalin!");
  } catch {
    showToast("Gagal menyalin", "error");
  }
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

function goBack() {
  window.history.back();
}

onUnmounted(() => {
  if (timerInterval) clearInterval(timerInterval);
  if (toastTimer) clearTimeout(toastTimer);
});
</script>

<style scoped>
.koperasi-page {
  min-height: 100vh;
  min-height: 100dvh;
  background: #ffffff;
  color: #111827;
  overflow-x: hidden;
}

/* ─── Topbar ─── */
.kop-topbar {
  position: sticky;
  top: 0;
  z-index: 100;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 14px max(5%, 32px);
  background: rgba(255, 255, 255, 0.92);
  backdrop-filter: blur(8px);
  border-bottom: 1px solid #f0f1f3;
}
.kop-topbar-left { display: flex; align-items: center; gap: 12px; }
.kop-back {
  width: 40px; height: 40px;
  border: 1px solid #ececed; border-radius: 50%;
  background: #fff; color: #111827;
  display: grid; place-items: center;
  cursor: pointer; transition: all 0.2s ease;
}
.kop-back:hover { background: #f7f7f8; }
.kop-logo {
  height: 34px; width: auto;
  border-radius: 9px;
  background: #fff; border: 1px solid #ececed;
  padding: 3px;
}
.kop-brand { font-size: 16px; font-weight: 800; letter-spacing: -0.02em; color: #111827; }
.kop-cart-btn {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 10px 18px;
  border: 1px solid #ececed; border-radius: 999px;
  background: #fff; color: #111827;
  font-size: 14px; font-weight: 600;
  cursor: pointer; transition: all 0.2s ease;
}
.kop-cart-btn:hover { background: #f7f7f8; border-color: #d9d9dc; }
.cart-count {
  min-width: 20px; height: 20px; padding: 0 6px;
  border-radius: 999px;
  background: #1c2a23; color: #fff;
  font-size: 11px; font-weight: 700;
  display: grid; place-items: center;
}

/* ─── Header ─── */
.kop-header { padding: 56px max(5%, 32px) 12px; }
.kop-header h1 {
  margin: 0 0 12px;
  font-size: clamp(36px, 5vw, 56px);
  font-weight: 800;
  letter-spacing: -0.03em;
  line-height: 1.05;
  color: #111827;
}
.kop-header p {
  margin: 0;
  max-width: 520px;
  font-size: 15px;
  line-height: 1.7;
  color: #6b7280;
}

/* ─── Filters ─── */
.filter-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
  padding: 28px max(5%, 32px) 8px;
}
.filter-pills { display: flex; flex-wrap: wrap; gap: 8px; }
.pill {
  padding: 9px 18px;
  border: 1px solid #e7e7e9; border-radius: 999px;
  background: #fff; color: #4b5563;
  font-size: 13px; font-weight: 600;
  cursor: pointer; transition: all 0.2s ease;
}
.pill:hover { border-color: #1c2a23; color: #1c2a23; }
.pill.active { background: #1c2a23; border-color: #1c2a23; color: #fff; }
.product-count { font-size: 13px; color: #9ca3af; font-weight: 500; }

/* ─── Product grid ─── */
.produk-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 28px 20px;
  padding: 20px max(4%, 24px) 80px;
}
.produk-card {
  cursor: pointer;
  border-radius: 12px;
  background: #fff;
  min-width: 0;
}
.produk-image {
  aspect-ratio: 4 / 3;
  background: #f5f6f7;
  border-radius: 12px;
  display: grid;
  place-items: center;
  overflow: hidden;
  transition: transform 0.3s ease;
}
.produk-card:hover .produk-image { transform: translateY(-3px); }
.produk-img {
  width: 100%; height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}
.produk-card:hover .produk-img { transform: scale(1.05); }
.produk-emoji { font-size: 52px; }
.produk-info { padding: 14px 2px 0; display: flex; align-items: center; justify-content: space-between; gap: 10px; }
.produk-info-left { min-width: 0; }
.produk-info h3 {
  margin: 0 0 4px;
  font-size: 15px; font-weight: 700;
  color: #111827; letter-spacing: -0.01em;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.produk-info p {
  margin: 0 0 8px;
  font-size: 13px; color: #9ca3af;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.produk-price { font-size: 14px; font-weight: 700; color: #111827; }
.produk-add {
  width: 34px; height: 34px;
  border: none; border-radius: 50%;
  background: #1c2a23; color: #fff;
  display: grid; place-items: center;
  cursor: pointer;
  transition: all 0.2s ease;
  flex-shrink: 0;
}
.produk-add:hover { background: #2f3d35; transform: scale(1.08); }
.produk-add:disabled { background: #d1d5db; cursor: not-allowed; transform: none; }

.empty-state { text-align: center; padding: 80px 0; color: #9ca3af; }

/* ─── Toast ─── */
.kop-toast {
  position: fixed;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 10002;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 22px;
  border-radius: 999px;
  background: #1c2a23;
  color: #fff;
  font-size: 13px;
  font-weight: 600;
  box-shadow: 0 12px 30px rgba(17, 24, 39, 0.18);
}
.kop-toast.error { background: #b91c1c; }
.fade-up-enter-active, .fade-up-leave-active { transition: all 0.25s ease; }
.fade-up-enter-from, .fade-up-leave-to { opacity: 0; transform: translateX(-50%) translateY(-8px); }

/* ─── Cart drawer ─── */
.drawer-overlay {
  position: fixed;
  inset: 0;
  z-index: 9000;
  background: rgba(17, 24, 39, 0.45);
  display: flex;
  justify-content: flex-end;
}
.drawer {
  width: min(420px, 100%);
  height: 100%;
  box-sizing: border-box;
  background: #fff;
  display: flex;
  flex-direction: column;
  padding: 20px 22px;
  box-shadow: -20px 0 60px rgba(17, 24, 39, 0.15);
}
.drawer-head {
  display: flex; align-items: center; justify-content: space-between;
  padding-bottom: 16px; border-bottom: 1px solid #f0f1f3;
}
.drawer-head h2 { margin: 0; font-size: 17px; font-weight: 800; color: #111827; }
.drawer-close {
  width: 34px; height: 34px;
  border: none; border-radius: 50%;
  background: #f5f6f7; color: #111827;
  display: grid; place-items: center;
  cursor: pointer;
}
.drawer-close:hover { background: #e9eaec; }
.drawer-body {
  flex: 1; overflow-y: auto;
  padding: 16px 0;
  display: flex; flex-direction: column; gap: 12px;
}
.drawer-empty { text-align: center; padding: 60px 0; color: #9ca3af; }
.drawer-empty p { font-weight: 700; color: #6b7280; margin: 12px 0 2px; }
.drawer-empty span { font-size: 13px; }
.drawer-foot {
  flex-shrink: 0;
  border-top: 1px solid #f0f1f3;
  padding: 16px 0 env(safe-area-inset-bottom, 8px);
  display: flex; flex-direction: column; gap: 12px;
}
.si {
  display: flex; align-items: center; gap: 12px;
  padding: 12px;
  border-radius: 14px;
  border: 1px solid #f0f1f3;
  background: #fafbfc;
}
.si-visual {
  width: 52px; height: 52px;
  border-radius: 10px;
  background: #f5f6f7;
  display: grid; place-items: center;
  font-size: 24px;
  overflow: hidden;
  flex-shrink: 0;
}
.si-img { width: 100%; height: 100%; object-fit: cover; }
.si-content { flex: 1; min-width: 0; }
.si-content h4 { margin: 0 0 2px; font-size: 14px; font-weight: 700; color: #111827; }
.si-price { font-size: 12px; color: #6b7280; font-weight: 600; }
.si-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
.qty-control { display: flex; align-items: center; gap: 2px; background: #fff; border: 1px solid #e7e7e9; border-radius: 10px; padding: 3px; }
.qty-btn {
  width: 26px; height: 26px;
  border: none; border-radius: 8px;
  background: transparent; color: #111827;
  display: grid; place-items: center;
  cursor: pointer;
}
.qty-btn:disabled { opacity: 0.35; cursor: not-allowed; }
.qty-btn:hover:not(:disabled) { background: #f5f6f7; }
.qty-num { min-width: 22px; text-align: center; font-size: 13px; font-weight: 700; color: #111827; }
.si-del {
  width: 32px; height: 32px;
  border: none; border-radius: 10px;
  background: #feebee; color: #c62839;
  display: grid; place-items: center;
  cursor: pointer;
}
.si-del:hover { background: #fdd7dc; }
.delivery-row {
  display: flex; align-items: center; gap: 8px;
  font-size: 13px; color: #6b7280;
  padding: 10px 12px; background: #fafbfc; border-radius: 12px;
}
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
  padding: 13px 24px;
  border: none; border-radius: 12px;
  font-size: 14px; font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}
.kop-btn.primary { background: #1c2a23; color: #fff; }
.kop-btn.primary:hover { background: #2f3d35; }
.kop-btn.primary:disabled { background: #d1d5db; cursor: not-allowed; }
.kop-btn.full { width: 100%; min-height: 48px; }

/* ─── Checkout / pending / success flows ─── */
.flow-wrap {
  min-height: calc(100vh - 90px);
  background: #fafbfc;
  display: flex;
  justify-content: center;
  padding: 32px max(5%, 32px);
}
.flow-box {
  width: 100%;
  max-width: 540px;
  background: #fff;
  border: 1px solid #f0f1f3;
  border-radius: 16px;
  box-shadow: 0 8px 30px rgba(17, 24, 39, 0.06);
  padding: 24px 28px;
  align-self: flex-start;
}
.flow-topbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
.flow-topbar h2 { font-size: 18px; font-weight: 800; color: #111827; margin: 0; }
.flow-back {
  width: 36px; height: 36px;
  border: 1px solid #ececed; border-radius: 10px;
  background: #fff; color: #111827;
  display: grid; place-items: center;
  cursor: pointer;
}
.flow-back:hover { background: #f7f7f8; }
.flow-spacer { width: 36px; }
.flow-center-head { justify-content: center; gap: 8px; }

.checkout-section { margin-bottom: 24px; }
.section-label {
  font-size: 12px; font-weight: 800;
  text-transform: uppercase; letter-spacing: 0.08em;
  color: #9ca3af; margin-bottom: 12px;
}
.order-items { display: flex; flex-direction: column; gap: 10px; }
.oi {
  display: flex; align-items: center; gap: 12px;
  padding: 12px; border-radius: 14px;
  background: #fafbfc; border: 1px solid #f0f1f3;
}
.oi-visual {
  width: 48px; height: 48px; border-radius: 10px;
  background: #f5f6f7;
  display: grid; place-items: center; font-size: 22px;
  overflow: hidden; flex-shrink: 0;
}
.oi-info { flex: 1; min-width: 0; }
.oi-info h4 { margin: 0 0 2px; font-size: 14px; font-weight: 700; color: #111827; }
.oi-meta { font-size: 12px; color: #9ca3af; }
.oi-total { font-size: 13px; font-weight: 800; color: #111827; }

.pay-options { display: flex; flex-direction: column; gap: 10px; }
.pay-opt {
  display: flex; align-items: center; gap: 12px;
  padding: 14px 16px;
  border: 1.5px solid #ececed; border-radius: 14px;
  background: #fff; text-align: left;
  cursor: pointer; transition: all 0.2s ease;
}
.pay-opt:hover { border-color: #d1d5db; }
.pay-opt.selected { border-color: #1c2a23; background: #f8faf9; }
.po-icon {
  width: 42px; height: 42px; border-radius: 12px;
  display: grid; place-items: center; flex-shrink: 0;
}
.qris-icon { background: #eef2ef; color: #1c2a23; }
.transfer-icon { background: #eef1f7; color: #1d4ed8; }
.po-text { flex: 1; }
.po-text h4 { margin: 0 0 2px; font-size: 14px; font-weight: 700; color: #111827; }
.po-text span { font-size: 12px; color: #9ca3af; }
.po-check {
  width: 22px; height: 22px; border-radius: 50%;
  background: #1c2a23; color: #fff;
  display: grid; place-items: center;
}

.summary-row {
  display: flex; justify-content: space-between; align-items: center;
  font-size: 13px; color: #6b7280; padding: 6px 0;
}
.summary-free { color: #1c2a23; font-weight: 700; }
.summary-row.total { border-top: 1px solid #f0f1f3; margin-top: 6px; padding-top: 12px; }
.summary-row.total strong { font-size: 17px; color: #111827; }

.checkout-footer { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
.checkout-total-label { font-size: 14px; font-weight: 700; color: #111827; }
.checkout-btn { flex: 1; max-width: 220px; }

/* ─── Pending payment ─── */
.pend-card {
  background: #fafbfc; border: 1px solid #f0f1f3;
  border-radius: 16px; padding: 22px; margin-bottom: 16px;
}
.pend-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.pend-head h3 { margin: 0; font-size: 15px; font-weight: 800; color: #111827; }
.pend-badge {
  padding: 5px 12px; border-radius: 999px;
  background: #fef3c7; color: #b45309;
  font-size: 11px; font-weight: 700;
}
.pend-body { text-align: center; }
.qr-wrap { display: flex; justify-content: center; margin-bottom: 12px; }
.qr-code {
  width: 200px; height: 200px;
  background: #fff; border: 1px solid #ececed; border-radius: 18px;
  padding: 16px;
  display: grid; place-items: center;
}
.qr-inner { position: relative; width: 100%; height: 100%; }
.qr-block { position: absolute; width: 42px; height: 42px; border: 8px solid #111827; }
.qr-block.tl { top: 4px; left: 4px; border-right-color: transparent; border-bottom-color: transparent; }
.qr-block.tr { top: 4px; right: 4px; border-left-color: transparent; border-bottom-color: transparent; }
.qr-block.bl { bottom: 4px; left: 4px; border-right-color: transparent; border-top-color: transparent; }
.qr-dots {
  position: absolute; inset: 52px 44px 44px 52px;
  display: grid; grid-template-columns: repeat(5, 1fr); gap: 4px;
}
.qr-dot { background: #111827; }
.qr-brand { position: absolute; bottom: 8px; left: 50%; transform: translateX(-50%); font-size: 9px; font-weight: 800; letter-spacing: 0.08em; color: #9ca3af; }
.pend-amount { margin: 4px 0 8px; font-size: 26px; font-weight: 800; color: #111827; }
.pend-hint { font-size: 13px; color: #9ca3af; margin: 0; }

.bank-details { display: flex; flex-direction: column; gap: 10px; text-align: left; }
.bank-row {
  display: flex; justify-content: space-between; align-items: center; gap: 12px;
  background: #fff; border: 1px solid #f0f1f3;
  padding: 12px 14px; border-radius: 12px;
  font-size: 13px;
}
.bank-label { color: #9ca3af; font-size: 12px; }
.bank-val { display: flex; align-items: center; gap: 10px; }
.bank-copy { cursor: pointer; }
.bank-copy:hover { border-color: #d1d5db; }
.copy-tag {
  display: inline-flex; align-items: center; gap: 4px;
  font-size: 11px; font-weight: 700; color: #1c2a23;
}
.bank-amount { font-size: 15px; }

.pend-timer {
  margin-top: 18px; text-align: center;
  padding-top: 16px; border-top: 1px solid #ececed;
}
.timer-label { display: block; font-size: 12px; color: #9ca3af; margin-bottom: 10px; }
.timer-row { display: flex; justify-content: center; align-items: center; gap: 8px; }
.timer-cell { display: flex; flex-direction: column; align-items: center; }
.timer-num {
  font-size: 24px; font-weight: 800; color: #111827;
  font-variant-numeric: tabular-nums;
}
.timer-unit { font-size: 10px; text-transform: uppercase; color: #9ca3af; }
.timer-colon { font-size: 20px; font-weight: 800; color: #1c2a23; }
.timer-warn { font-size: 11px; color: #b45309; margin: 12px 0 0; }

.btn-spin {
  width: 16px; height: 16px; border-radius: 50%;
  border: 2px solid rgba(255,255,255,0.35); border-top-color: #fff;
  animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ─── Success ─── */
.flow-center { text-align: center; }
.suc-circle {
  width: 76px; height: 76px; border-radius: 50%;
  background: #e6f6ee; color: #166534;
  display: grid; place-items: center;
  margin: 16px auto 20px;
}
.suc-title { margin: 0 0 6px; font-size: 22px; font-weight: 800; color: #111827; }
.suc-sub { margin: 0 0 20px; font-size: 14px; color: #6b7280; }
.suc-card {
  background: #fafbfc; border: 1px solid #f0f1f3;
  border-radius: 16px; padding: 8px 18px; margin-bottom: 16px; text-align: left;
}
.suc-row { display: flex; justify-content: space-between; align-items: center; font-size: 13px; padding: 10px 0; border-bottom: 1px solid #f0f1f3; }
.suc-row:last-child { border-bottom: none; }
.suc-row span { color: #6b7280; }
.suc-amount { color: #166534; font-size: 15px; }
.suc-status { color: #166534; font-weight: 800; }
.suc-notif {
  display: flex; gap: 12px; text-align: left;
  background: #eef2ef; border-radius: 14px; padding: 14px 16px; margin-bottom: 18px;
}
.sn-icon {
  width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0;
  background: #fff; color: #1c2a23;
  display: grid; place-items: center;
}
.sn-text strong { font-size: 13px; color: #111827; }
.sn-text p { margin: 4px 0 0; font-size: 12px; color: #4b5563; line-height: 1.6; }

/* ─── Detail modal ─── */
.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  background: rgba(17, 24, 39, 0.5);
  backdrop-filter: blur(4px);
}
.modal-content {
  position: relative;
  width: 100%;
  max-width: 640px;
  max-height: 85vh;
  overflow-y: auto;
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 24px 60px rgba(17, 24, 39, 0.2);
}
.modal-close {
  position: absolute;
  top: 16px; right: 16px; z-index: 10;
  width: 36px; height: 36px;
  border-radius: 50%;
  border: none;
  background: rgba(255, 255, 255, 0.9);
  color: #111827; font-size: 22px;
  cursor: pointer;
  display: grid; place-items: center;
  box-shadow: 0 2px 12px rgba(17, 24, 39, 0.2);
}
.modal-close:hover { background: #fff; }
.modal-image {
  width: 100%;
  aspect-ratio: 16 / 9;
  background: #f5f6f7;
  display: grid;
  place-items: center;
  border-radius: 16px 16px 0 0;
}
.modal-img { width: 100%; height: 100%; object-fit: cover; object-position: center; }
.modal-emoji { font-size: 72px; }
.modal-body { padding: 28px 32px 36px; }
.modal-category {
  display: inline-flex;
  padding: 4px 10px;
  border-radius: 999px;
  background: #f0f1f3;
  color: #4b5563;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
}
.modal-body h2 { margin: 14px 0 8px; font-size: 22px; font-weight: 800; color: #111827; }
.modal-price { margin: 0 0 16px; font-size: 18px; font-weight: 800; color: #111827; }
.modal-desc { font-size: 15px; color: #4b5563; line-height: 1.8; }
.modal-stock {
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid #f0f1f3;
  font-size: 13px;
  color: #6b7280;
}
.modal-add { margin-top: 18px; }

.modal-enter-active, .modal-leave-active { transition: opacity 0.25s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }

@media (max-width: 1024px) {
  .produk-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}

@media (max-width: 768px) {
  .kop-header { padding-top: 36px; }
  .produk-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 24px 14px; padding-bottom: 56px; }
  .flow-wrap { padding: 16px; }
  .flow-box { padding: 20px; }
  .checkout-footer { flex-direction: column; }
  .checkout-btn { max-width: 100%; }
}

@media (max-width: 480px) {
  .produk-grid { grid-template-columns: 1fr; }
  .kop-brand { display: none; }
}
</style>