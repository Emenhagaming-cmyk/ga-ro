<template>
  <section id="layanan" class="feature">
    <div class="feature-shell">
      <div class="heading" v-reveal>
        <h2>Satu Kotak<br /><em>Beragam Layanan</em></h2>
        <p>
          Satu ruang untuk menemukan informasi, layanan, dan peluang yang ada di
          SMK Bahrul Ulum.
        </p>
      </div>

      <div class="bento">
        <!-- 01: SPMB Hero -->
        <div class="card card-spmb" v-reveal id="spmb">
          <div class="spmb-visual">
            <img src="/pmb_smkbu.webp" alt="SPMB SMK Bahrul Ulum" loading="lazy" width="580" height="240" />
            <div class="spmb-overlay"></div>
            <span class="spmb-badge">PMB 2027</span>
          </div>
          <div class="spmb-body">
            <div class="spmb-stats">
              <div class="stat-block">
                <span class="stat-num">{{ spmbStats.totalRegistered.toLocaleString('id-ID') }}</span>
                <span class="stat-label">Siswa Mendaftar</span>
              </div>
              <div class="stat-divider"></div>
              <div class="stat-block">
                <span class="stat-num">1</span>
                <span class="stat-label">Jurusan</span>
              </div>
            </div>
            <div class="spmb-chips">
              <span class="chip">Gelombang 1 · 2027</span>
              <span class="chip chip-accent">RPL</span>
              <span class="chip">Online & Offline</span>
            </div>
            <div class="spmb-benefits">
              <span class="benefit" v-for="b in spmbBenefits" :key="b.text">
                <component :is="b.icon" size="13" />
                {{ b.text }}
              </span>
            </div>
            <div class="spmb-meta">Batas pendaftaran: {{ spmbStats.deadline }}</div>
            <div class="spmb-actions">
              <a :href="spmbTarget()" class="btn-primary" @click.prevent="handleDaftarClick">
                Daftar Sekarang <span aria-hidden="true"></span>
              </a>
              <a href="/spmb-info" class="btn-outline" @click.stop="maybeNavigate('/spmb-info')">
                Info & Biaya <span aria-hidden="true"></span>
              </a>
            </div>
          </div>
        </div>

        <!-- 02: Berita -->
        <div class="card card-berita" v-reveal="0.06" @click="handleCardClick(items[1])">
          <div class="card-header">
            <span class="card-num">01</span>
            <div class="card-icon"><Newspaper :size="18" :stroke-width="2" /></div>
          </div>
          <div class="card-visual">
            <span class="visual-big">{{ latestNewsCount }}</span>
            <span class="visual-label">Berita</span>
          </div>
          <p class="card-desc">{{ latestNews.title || 'Kegiatan dan pengumuman terbaru dari sekolah.' }}</p>
          <div class="card-date" v-if="latestNews.publishedAt">
            <Clock :size="12" /> {{ formatDate(latestNews.publishedAt) }}
          </div>
          <span class="card-link">Baca berita <span aria-hidden="true">↗</span></span>
        </div>

        <!-- 03: Career Center -->
        <div class="card card-career" v-reveal="0.12">
          <div class="card-header">
            <span class="card-num">02</span>
            <div class="card-icon"><BriefcaseBusiness :size="18" :stroke-width="2" /></div>
          </div>
          <div class="card-visual">
            <span class="visual-big">{{ lowonganCount }}</span>
            <span class="visual-label">Lowongan Aktif</span>
          </div>
          <div class="career-bars">
            <div class="bar" v-for="i in 5" :key="i" :style="{ height: 20 + Math.random() * 60 + '%' }"></div>
          </div>
          <span class="card-link" @click.stop="handleCardClick(items[2])">Lihat peluang <span aria-hidden="true">↗</span></span>
        </div>

        <!-- 04: Tentang Sekolah -->
        <div class="card card-tentang" v-reveal="0.18" @click="handleCardClick(items[3])">
          <div class="card-header">
            <span class="card-num">03</span>
            <div class="card-icon"><School :size="18" :stroke-width="2" /></div>
          </div>
          <div class="card-visual">
            <span class="visual-big">25+</span>
            <span class="visual-label">Tahun Mendidik</span>
          </div>
          <p class="card-desc">SMK Bahrul Ulum berdiri sejak 1998, mencetak generasi qur'ani dan unggul di bidang teknologi.</p>
          <span class="card-link">Kenali kami <span aria-hidden="true">↗</span></span>
        </div>

        <!-- 05: Koperasi -->
        <div class="card card-koperasi" v-reveal="0.24" @click="handleCardClick(items[4])">
          <div class="card-header">
            <span class="card-num">04</span>
            <div class="card-icon"><ShoppingBag :size="18" :stroke-width="2" /></div>
          </div>
          <div class="card-visual">
            <span class="visual-big">16+</span>
            <span class="visual-label">Produk</span>
          </div>
          <p class="card-desc">Seragam, alat tulis, dan kebutuhan siswa lainnya tersedia secara online.</p>
          <span class="card-link">Buka koperasi <span aria-hidden="true">↗</span></span>
        </div>

        <!-- 06: Produk Siswa -->
        <div class="card card-produk" v-reveal="0.30" @click="handleCardClick(items[5])">
          <div class="card-header">
            <span class="card-num">05</span>
            <div class="card-icon"><Info :size="18" :stroke-width="2" /></div>
          </div>
          <div class="card-produk-inner">
            <div class="card-visual">
              <span class="visual-big">10+</span>
              <span class="visual-label">Karya Siswa</span>
            </div>
            <div class="produk-tags">
              <span class="chip">RPL</span>
              <span class="chip">TKJ</span>
              <span class="chip">AKL</span>
              <span class="chip">Multimedia</span>
            </div>
          </div>
          <p class="card-desc">Galeri produk digital dan kerajinan buatan siswa SMK Bahrul Ulum.</p>
          <span class="card-link">Lihat karya <span aria-hidden="true">↗</span></span>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useAuthSession } from "@/composable/useAuthSession";
const { session, loaded, spmbTarget, BACKEND } = useAuthSession();
import { useToast } from "@/composable/useToast";
import {
  GraduationCap,
  BriefcaseBusiness,
  ShoppingBag,
  Newspaper,
  School,
  Info,
  CheckCircle,
  Clock,
  Edit,
  MessageSquare,
} from "lucide-vue-next";

const router = useRouter();
const { showToast } = useToast();

const latestNews = ref({ title: '', excerpt: '', image: '', publishedAt: '', categoryColor: '#3a6450' });
const latestNewsCount = ref(6);
const lowonganCount = ref(0);

const formatDate = (d) => {
  if (!d) return '';
  const date = new Date(d);
  return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
};

onMounted(async () => {
  try {
    const res = await fetch('/data/news.json');
    const news = await res.json();
    latestNewsCount.value = news.length || 0;
    if (news.length > 0) {
      latestNews.value = news[0];
      latestNews.value.categoryColor = news[0].categoryColor || '#3a6450';
    }
  } catch (e) {
    console.error('Gagal fetch berita', e);
  }

  try {
    const res = await fetch(BACKEND + '/api/lowongan');
    const data = await res.json();
    lowonganCount.value = data.length || data.total || 0;
  } catch (e) {
    lowonganCount.value = 12;
  }
});

function maybeNavigate(href) {
  if (window.innerWidth > 900) router.push(href);
}

function handleLinkClick(event, href) {
  if (window.innerWidth > 900) { event.preventDefault(); router.push(href); }
}

function handleCardClick(item) {
  const role = session.value.role;
  if (["/koperasi", "/produk-siswa", "/career-center"].includes(item.href) && role !== "siswa") {
    showToast("Khusus siswa, silakan login terlebih dahulu");
    return;
  }
  if (!item.href) return;
  if (window.innerWidth > 900) router.push(item.href);
  else window.location.href = item.href;
}

function handleDaftarClick() {
  if (session.value.role === "siswa") {
    showToast("Anda sudah terdaftar sebagai siswa");
    return;
  }
  window.location.href = spmbTarget();
}

const spmbBenefits = [
  { icon: CheckCircle, text: 'Gratis biaya daftar' },
  { icon: Clock, text: 'Proses 1 hari' },
  { icon: Edit, text: 'Edit kurang dari 3 hari' },
  { icon: MessageSquare, text: 'Pendaftaran mudah' },
];

const spmbStats = { totalRegistered: 1247, currentWave: 'Gelombang 1', deadline: '2027-09-15' };

const items = [
  { title: "SPMB Online", href: null, icon: GraduationCap },
  { title: "Berita Hari Ini", href: "/berita", icon: Newspaper },
  { title: "Career Center", href: "/career-center", icon: BriefcaseBusiness },
  { title: "Tentang Sekolah", href: "#tentang", icon: School },
  { title: "Koperasi Online", href: "/koperasi", icon: ShoppingBag },
  { title: "Produk Siswa", href: "/produk-siswa", icon: Info },
];
</script>

<style scoped>
/* ═══════════════════ PALETTE ═══════════════════ */
.feature {
  --g900: #0f2a1a;
  --g800: #1a3a28;
  --g700: #2d5a3d;
  --g600: #3a6450;
  --g500: #4a8a62;
  --g400: #6aad80;
  --g300: #9cc7b0;
  --g200: #c8e0d2;
  --g100: #e8f0eb;
  --g50: #f5f9f6;
  --radius: 18px;
  padding: 96px 7%;
  background: var(--g50);
  color: var(--g900);
  scroll-margin-top: 90px;
}

.feature-shell { max-width: 1200px; margin: 0 auto; }

/* ═══════════════════ HEADING ═══════════════════ */
.heading {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin: 0 auto 48px;
  text-align: center;
}
.heading h2 {
  margin: 0;
  font-family: "Quicksand", sans-serif;
  font-size: clamp(30px, 4vw, 48px);
  font-weight: 800;
  letter-spacing: -0.03em;
  line-height: 1.08;
}
.heading h2 em {
  color: var(--g600);
  font-family: inherit;
  font-style: normal;
  font-weight: 500;
}
.heading p {
  max-width: 480px;
  margin: 16px 0 0;
  color: #6c7a6e;
  font-family: "Quicksand", sans-serif;
  font-size: 14px;
  font-weight: 500;
  line-height: 1.7;
}

/* ═══════════════════ GRID ═══════════════════ */
.bento {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  grid-auto-rows: minmax(170px, auto);
  gap: 14px;
}

.card {
  position: relative;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  padding: 22px;
  border-radius: var(--radius);
  transition:
    transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
    box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.card.clickable { cursor: pointer; }
.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 40px rgba(0, 0, 0, 0.10);
}

/* ═══════════════════ CARD GRID POSITIONS ═══════════════════ */
.card-spmb     { grid-column: 1 / 6;  grid-row: 1 / 3; }
.card-berita   { grid-column: 6 / 9;  grid-row: 1 / 2; }
.card-career   { grid-column: 9 / 13; grid-row: 1 / 3; }
.card-tentang  { grid-column: 6 / 9;  grid-row: 2 / 3; }
.card-koperasi { grid-column: 1 / 5;  grid-row: 3 / 4; }
.card-produk   { grid-column: 5 / 13; grid-row: 3 / 4; }

/* ═══════════════════ CARD: SPMB (Hero) ═══════════════════ */
.card-spmb {
  background: var(--g800);
  color: #fff;
  padding: 0;
  border: 2px solid var(--g700);
}
.card-spmb:hover {
  background: var(--g700);
}

.spmb-visual {
  position: relative;
  width: 100%;
  height: 200px;
  flex-shrink: 0;
  overflow: hidden;
}
.spmb-visual img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
}
.spmb-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to bottom, transparent 40%, var(--g800) 100%);
  pointer-events: none;
}
.spmb-badge {
  position: absolute;
  top: 14px;
  right: 14px;
  padding: 4px 12px;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 999px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.05em;
  color: #fff;
}

.spmb-body {
  padding: 20px 24px 22px;
  display: flex;
  flex-direction: column;
  gap: 14px;
  flex: 1;
}

.spmb-stats {
  display: flex;
  align-items: center;
  gap: 20px;
}
.stat-block { display: flex; flex-direction: column; gap: 2px; }
.stat-num {
  font-family: "Quicksand", sans-serif;
  font-size: clamp(28px, 3.5vw, 40px);
  font-weight: 800;
  letter-spacing: -0.03em;
  line-height: 1;
  color: #fff;
  font-variant-numeric: tabular-nums;
}
.stat-label {
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.55);
}
.stat-divider {
  width: 1px;
  height: 36px;
  background: rgba(255, 255, 255, 0.15);
}

.spmb-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}
.chip {
  padding: 4px 10px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 999px;
  font-size: 11px;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.75);
  white-space: nowrap;
}
.card-spmb .chip-accent {
  background: rgba(106, 173, 128, 0.25);
  border-color: rgba(106, 173, 128, 0.4);
  color: #fff;
}

.spmb-benefits {
  display: flex;
  flex-wrap: wrap;
  gap: 10px 16px;
}
.benefit {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 11.5px;
  font-weight: 500;
  color: rgba(255, 255, 255, 0.7);
}

.spmb-meta {
  font-size: 11px;
  color: rgba(255, 255, 255, 0.45);
}

.spmb-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: auto;
  padding-top: 4px;
}
/* ═══════════════════ GLASS BUTTONS ═══════════════════ */
.btn-primary,
.btn-outline {
  position: relative;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  background: rgba(255, 255, 255, 0.07);
  backdrop-filter: blur(16px) saturate(1.4);
  -webkit-backdrop-filter: blur(16px) saturate(1.4);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 14px;
  color: rgba(255, 255, 255, 0.92);
  font-family: "Quicksand", sans-serif;
  font-size: 14px;
  font-weight: 700;
  text-decoration: none;
  cursor: pointer;
  overflow: hidden;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* glossy highlight — tipis, di bagian atas */
.btn-primary::before,
.btn-outline::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 45%;
  background: linear-gradient(to bottom, rgba(255, 255, 255, 0.22), transparent);
  border-radius: 14px 14px 0 0;
  pointer-events: none;
}

/* inner glow — efek cahaya dari dalam */
.btn-primary::after,
.btn-outline::after {
  content: "";
  position: absolute;
  inset: 1px;
  border-radius: 13px;
  background: linear-gradient(135deg, rgba(255,255,255,0.06) 0%, transparent 50%);
  pointer-events: none;
}

.btn-primary:hover,
.btn-outline:hover {
  background: rgba(255, 255, 255, 0.14);
  border-color: rgba(255, 255, 255, 0.3);
  transform: translateY(-2px);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
}

.btn-primary:active,
.btn-outline:active {
  transform: translateY(0) scale(0.97);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.btn-primary span,
.btn-outline span {
  display: inline-block;
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  font-size: 15px;
}
.btn-primary:hover span { transform: translateX(3px); }
.btn-outline:hover span { transform: translate(2px, -2px); }

/* ═══════════════════ CARD: Small cards (berita, tentang) ═══════════════════ */
.card-berita {
  background: #fff;
  border: 1px solid var(--g200);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}
.card-berita:hover { box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08); }

.card-tentang {
  background: var(--g100);
  border: 1px solid var(--g200);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
}
.card-tentang:hover { box-shadow: 0 12px 32px rgba(0, 0, 0, 0.07); }

/* ═══════════════════ CARD: Career Center (tall) ═══════════════════ */
.card-career {
  background: linear-gradient(135deg, var(--g700), var(--g600));
  color: #fff;
  border: 2px solid var(--g600);
}
.card-career:hover {
  background: linear-gradient(135deg, var(--g600), var(--g500));
}

/* ═══════════════════ CARD: Koperasi ═══════════════════ */
.card-koperasi {
  background: #fff;
  border: 1px solid var(--g200);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}
.card-koperasi:hover { box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08); }

/* ═══════════════════ CARD: Produk Siswa (wide) ═══════════════════ */
.card-produk {
  background: linear-gradient(135deg, var(--g100), #e0ede4);
  border: 1px solid var(--g200);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
}
.card-produk:hover { box-shadow: 0 12px 32px rgba(0, 0, 0, 0.07); }

/* ═══════════════════ SHARED: Card header ═══════════════════ */
.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 14px;
}
.card-num {
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.1em;
  color: var(--g300);
}
.card-career .card-num { color: rgba(255, 255, 255, 0.4); }

.card-icon {
  display: grid;
  place-items: center;
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: var(--g100);
  color: var(--g600);
}
.card-career .card-icon {
  background: rgba(255, 255, 255, 0.12);
  color: #fff;
}
.card-produk .card-icon {
  background: rgba(58, 100, 80, 0.12);
}

/* ═══════════════════ SHARED: Visual (big number) ═══════════════════ */
.card-visual {
  display: flex;
  flex-direction: column;
  gap: 2px;
  margin-bottom: 12px;
}
.visual-big {
  font-family: "Quicksand", sans-serif;
  font-size: clamp(36px, 4.5vw, 52px);
  font-weight: 800;
  letter-spacing: -0.04em;
  line-height: 1;
  color: var(--g700);
}
.card-career .visual-big { color: #fff; }
.card-tentang .visual-big { color: var(--g600); }
.visual-label {
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.04em;
  color: var(--g400);
  margin-top: 4px;
}
.card-career .visual-label { color: rgba(255, 255, 255, 0.55); }

/* ═══════════════════ SHARED: Description ═══════════════════ */
.card-desc {
  margin: 0 0 auto;
  font-size: 13px;
  line-height: 1.6;
  color: #6c7a6e;
  max-width: 100%;
}
.card-career .card-desc,
.card-tentang .card-desc { color: rgba(255, 255, 255, 0.7); }

/* ═══════════════════ SHARED: Date ═══════════════════ */
.card-date {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  margin-top: 8px;
  font-size: 11px;
  font-weight: 500;
  color: var(--g400);
}

/* ═══════════════════ SHARED: Link ═══════════════════ */
.card-link {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  margin-top: 14px;
  font-size: 12px;
  font-weight: 800;
  color: var(--g600);
  text-decoration: none;
  cursor: pointer;
  transition: color 0.2s;
}
.card-link span {
  display: inline-block;
  transition: transform 0.2s;
}
.card-link:hover span { transform: translate(2px, -2px); }
.card-career .card-link { color: rgba(255, 255, 255, 0.8); }
.card-career .card-link:hover { color: #fff; }

/* ═══════════════════ CAREER: Bar chart ═══════════════════ */
.career-bars {
  display: flex;
  align-items: flex-end;
  gap: 6px;
  height: 60px;
  margin: auto 0 8px;
}
.bar {
  flex: 1;
  background: rgba(255, 255, 255, 0.15);
  border-radius: 4px 4px 0 0;
  min-height: 8px;
  transition: background 0.2s;
}
.card-career:hover .bar {
  background: rgba(255, 255, 255, 0.25);
}

/* ═══════════════════ PRODUK: Inner layout ═══════════════════ */
.card-produk-inner {
  display: flex;
  align-items: flex-start;
  gap: 24px;
  margin-bottom: 10px;
}
.produk-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  align-self: center;
}
.card-produk .chip {
  background: rgba(58, 100, 80, 0.08);
  border-color: rgba(58, 100, 80, 0.15);
  color: var(--g600);
}

/* ═══════════════════ RESPONSIVE ═══════════════════ */
@media (max-width: 1024px) {
  .card-spmb     { grid-column: 1 / 7; grid-row: 1 / 2; }
  .card-berita   { grid-column: 7 / 13; }
  .card-career   { grid-column: 1 / 7; grid-row: 2 / 3; }
  .card-tentang  { grid-column: 7 / 13; }
  .card-koperasi { grid-column: 1 / 7; }
  .card-produk   { grid-column: 7 / 13; }
}

@media (max-width: 768px) {
  .feature { padding: 64px 5%; }
  .heading { margin-bottom: 32px; }
  .bento {
    grid-template-columns: repeat(2, 1fr);
    grid-auto-rows: auto;
  }
  .card-spmb,
  .card-berita,
  .card-career,
  .card-tentang,
  .card-koperasi,
  .card-produk {
    grid-column: span 1;
    grid-row: auto;
  }
  .card-spmb { grid-column: 1 / -1; }
  .card-produk { grid-column: 1 / -1; }
  .card-produk-inner { flex-direction: column; gap: 12px; }
  .career-bars { height: 50px; }
}

@media (max-width: 520px) {
  .feature { padding: 48px 4%; }
  .bento { gap: 10px; }
  .card { padding: 16px; }
  .spmb-visual { height: 160px; }
  .spmb-body { padding: 16px 18px 18px; gap: 10px; }
  .spmb-stats { gap: 14px; }
  .stat-num { font-size: 24px; }
  .spmb-benefits { gap: 6px 12px; }
  .benefit { font-size: 10.5px; }
  .spmb-actions { flex-direction: column; gap: 10px; }
  .btn-primary, .btn-outline { width: 100%; justify-content: center; }
}
</style>
