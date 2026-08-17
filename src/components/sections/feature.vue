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
        <div
          v-for="(item, index) in items"
          v-reveal="0.06 * index"
          :key="item.title"
          :class="[
            'card',
            item.size,
            item.layout,
            { featured: index === 0, clickable: item.href },
          ]"
          :id="index === 0 ? 'spmb' : undefined"
          @click="index === 0 ? undefined : handleCardClick(item)"
        >
          <div v-if="index === 0" class="spmb-banner" aria-hidden="true">
            <img :src="item.banner || '/bu.jpg'" alt="SPMB Banner" />
          </div>
          <div class="card-top">
            <span class="card-number">0{{ index + 1 }}</span>
            <div v-if="index !== 0" class="icon">
              <component :is="item.icon" :size="22" :stroke-width="1.8" />
            </div>
          </div>
          <div class="card-content">
            <h3>{{ item.title }}</h3>
            <p>{{ item.desc }}</p>
            <div v-if="index === 0" class="spmb-benefits">
              <span class="benefit" v-for="b in spmbBenefits" :key="b.text">
                <component :is="b.icon" class="benefit-icon" size="14" />
                {{ b.text }}
              </span>
            </div>
            <div v-if="index === 0" class="spmb-counter">
              <span class="counter-main">
                <strong>{{ spmbStats.totalRegistered.toLocaleString('id-ID') }}</strong> siswa sudah mendaftar {{ spmbStats.currentWave }}
              </span>
              <span class="counter-deadline">Batas: {{ spmbStats.deadline }}</span>
            </div>
            <div v-if="index === 0" class="spmb-chips">
              <span class="chip" v-for="chip in spmbChips" :key="chip">{{ chip }}</span>
              <span class="badge" v-for="major in spmbMajors" :key="major">{{ major }}</span>
            </div>
          </div>
          <a
            v-if="index === 0"
            :href="spmbTarget()"
            class="btn-daftar"
            @click.prevent="handleDaftarClick"
          >
            Daftar sekarang <span aria-hidden="true"></span>
          </a>
          <a
            v-else-if="item.href"
            :href="item.href"
            class="card-link"
            @click.stop="handleLinkClick($event, item.href)"
          >
            {{ item.button }} <span aria-hidden="true">↗</span>
          </a>
          <button v-else type="button">
            {{ item.button }} <span aria-hidden="true">↗</span>
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { useRouter } from "vue-router";
import { useAuthSession } from "@/composable/useAuthSession";
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
const { session, loaded, spmbTarget } = useAuthSession();
const { showToast } = useToast();

function maybeNavigate(href) {
  if (window.innerWidth > 900) {
    router.push(href);
  }
}

function handleLinkClick(event, href) {
  if (window.innerWidth > 900) {
    event.preventDefault();
    router.push(href);
  }
}

function handleCardClick(item) {
  const role = session.value.role;

  if (item.href === "/koperasi" || item.href === "/produk-siswa" || item.href === "/career-center") {
    if (role !== "siswa") {
      showToast("Khusus siswa, silakan login terlebih dahulu");
      return;
    }
  }

  if (!item.href) return;

  if (window.innerWidth > 900) {
    router.push(item.href);
  } else {
    window.location.href = item.href;
  }
}

function handleDaftarClick() {
  const role = session.value.role;
  if (role === "siswa") {
    showToast("Anda sudah terdaftar sebagai siswa");
    return;
  }
  window.location.href = spmbTarget();
}

const spmbChips = [
  ' Gelombang 1 · 2027',
  ' 1 Jurusan',
  ' Daftar Online',
  ' Daftar Offline'
]

const spmbMajors = ['RPL']

const spmbBenefits = [
  { icon: CheckCircle, text: 'Gratis biaya daftar' },
  { icon: Clock, text: 'Proses 1 hari' },
  { icon: Edit, text: 'Bisa edit kurang dari 3 hari' },
  { icon: MessageSquare, text: 'Pendaftaran mudah' }
]

const spmbStats = {
  totalRegistered: 1247,
  currentWave: 'Gelombang 1',
  deadline: '2027-09-15'
}

const items = [
  {
    title: "SPMB Online",
    desc: "Daftar peserta didik baru secara online dengan proses yang cepat dan mudah.",
    button: "Daftar sekarang",
    banner: "/pmb_smkbu.jpg",
    icon: GraduationCap,
    size: "big",
    layout: "vertical",
  },
  {
    title: "Berita Hari Ini",
    desc: "Kegiatan, kabar, dan pengumuman terbaru dari sekolah.",
    button: "Baca berita",
    href: "/berita",
    icon: Newspaper,
    size: "small",
    layout: "vertical",
  },
  {
    title: "Career Center",
    desc: "Temukan informasi PKL, magang, dan peluang kerja untuk langkah berikutnya.",
    button: "Lihat peluang",
    href: "/career-center",
    icon: BriefcaseBusiness,
    size: "small",
    layout: "vertical",
  },
  {
    title: "Tentang Sekolah",
    desc: "Kenali lebih dekat profil, visi misi, dan cerita sekolah kami.",
    button: "Kenali kami",
    icon: School,
    size: "wide",
    layout: "vertical",
  },
  {
    title: "Koperasi Online",
    desc: "Belanja kebutuhan siswa dengan lebih praktis.",
    button: "Buka koperasi",
    href: "/koperasi",
    icon: ShoppingBag,
    size: "small",
    layout: "vertical",
  },
  {
    title: "Produk Siswa",
    desc: "Lihat karya dan produk unggulan buatan siswa SMK Bahrul Ulum langsung dari galeri digital kami.",
    button: "Lihat karya",
    href: "/produk-siswa",
    icon: Info,
    size: "medium",
    layout: "horizontal",
  },
];
</script>

<style scoped>
.feature {
  padding: 96px 7%;
  background: #f2f4f1;
  color: #1c2a23;
  scroll-margin-top: 90px;
}

#spmb {
  scroll-margin-top: 100px;
}

.feature-shell {
  max-width: 1180px;
  margin: 0 auto;
}

/* ---- heading ---- */

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
  color: #3a6450;
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

/* ---- bento grid ---- */

.bento {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  grid-auto-rows: minmax(170px, auto);
  gap: 12px;
}

.card {
  position: relative;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  padding: 22px;
  border: 2px solid #d4dbd8;
  border-radius: 18px;
  background: #fbfcfa;
  transition:
    transform 0.3s ease,
    box-shadow 0.3s ease,
    background 0.3s ease;
}

.card:hover {
  z-index: 1;
  transform: translateY(-5px);
  box-shadow: 0 20px 40px rgba(35, 55, 42, 0.12);
}

.card.clickable {
  cursor: pointer;
}

.card:not(.featured):hover {
  background: #fff;
}

.card-top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
}

.card-number {
  color: #98a299;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.12em;
}

.icon {
  display: grid;
  width: 44px;
  height: 44px;
  place-items: center;
  border-radius: 50%;
  background: #e3ece5;
  color: #3a6450;
}

.card-content {
  margin-top: auto;
  min-height: 0;
}

.card h3 {
  margin: 14px 0 6px;
  color: #1a2620;
  font-size: 19px;
  font-weight: 650;
  letter-spacing: -0.025em;
  line-height: 1.25;
}

.card p {
  max-width: 100%;
  margin: 0;
  color: #6f7e72;
  font-size: 13px;
  line-height: 1.6;
}

.card button,
.card-link {
  align-self: flex-start;
  margin-top: 20px;
  padding: 0;
  border: 0;
  background: transparent;
  color: #3a6450;
  font-family: inherit;
  font-size: 12px;
  font-weight: 800;
  cursor: pointer;
  text-decoration: none;
}

.card button span,
.card-link span {
  display: inline-block;
  margin-left: 5px;
  font-size: 16px;
  transition: transform 0.2s ease;
}

.card button:hover span,
.card-link:hover span {
  transform: translate(2px, -2px);
}

.featured {
  display: flex;
  flex-direction: column;
  padding: 0;
  border: 3px solid #1e3a2f;
  border-radius: 20px;
  background: #2b4a3c;
  color: #fff;
  overflow: hidden;
  position: relative;
}

.featured::before {
  content: "";
  position: absolute;
  inset: 0;
  background-image:
    radial-gradient(circle at 20% 20%, rgba(255,255,255,0.06) 1px, transparent 1px),
    radial-gradient(circle at 80% 80%, rgba(255,255,255,0.04) 1px, transparent 1px);
  background-size: 60px 60px, 80px 80px;
  pointer-events: none;
  z-index: 0;
}

.featured .spmb-banner,
.featured .card-content,
.featured .btn-daftar {
  position: relative;
  z-index: 1;
}

.featured .spmb-banner {
  width: 100%;
  height: 220px;
  flex-shrink: 0;
}

.featured .spmb-banner img {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
}

.featured .card-top {
  display: none;
}

.featured .card-content {
  padding: 22px 26px 18px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  flex: 1;
  min-height: 0;
}

.featured .card-content h3 {
  margin: 0 0 10px;
  font-size: 24px;
  font-weight: 800;
  color: #fff;
  line-height: 1.2;
}

.featured .card-content p {
  color: rgba(255, 255, 255, 0.82);
  font-size: 14px;
  line-height: 1.7;
  margin: 0;
  max-width: 100%;
}

.spmb-benefits {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 10px;
}

.benefit {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 11.5px;
  color: rgba(255,255,255,0.85);
  white-space: nowrap;
}

.benefit-icon {
  flex-shrink: 0;
  color: rgba(255,255,255,0.7);
}

.spmb-counter {
  display: flex;
  flex-direction: column;
  gap: 4px;
  margin-bottom: 10px;
  padding: 10px 12px;
  background: rgba(255,255,255,0.08);
  border-radius: 10px;
  border: 1px solid rgba(255,255,255,0.12);
}

.counter-main {
  font-size: 13px;
  font-weight: 600;
  color: #fff;
  font-variant-numeric: tabular-nums;
}

.counter-deadline {
  font-size: 11px;
  color: rgba(255,255,255,0.65);
}

.spmb-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: auto;
  margin-bottom: 12px;
}

.chip {
  background: rgba(255,255,255,0.12);
  font-size: 12px;
  padding: 4px 10px;
  border-radius: 999px;
  white-space: nowrap;
}

.badge {
  background: rgba(255,255,255,0.1);
  border: 1px solid rgba(255,255,255,0.25);
  font-size: 11px;
  padding: 4px 10px;
  border-radius: 999px;
  white-space: nowrap;
}

@media (max-width: 520px) {
  .spmb-benefits { gap: 6px; }
  .benefit { font-size: 10.5px; }
  .spmb-counter { padding: 8px 10px; }
  .counter-main { font-size: 12px; }
  .counter-deadline { font-size: 10px; }
  .spmb-chips { gap: 6px; }
  .chip { font-size: 11px; padding: 3px 8px; }
  .badge { font-size: 10px; padding: 3px 8px; }
}

.featured .btn-daftar {
  align-self: flex-end;
  padding: 9px 18px;
  margin: 14px 0 0;
  border: none;
  border-radius: 10px;
  color: #fff;
  font-family: inherit;
  font-size: 14px;
  font-weight: 800;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.2s ease;
  position: relative;
  z-index: 10;
  pointer-events: auto;
}

.featured .btn-daftar:hover {
  transform: translateX(4px);
}

.featured .btn-daftar:active {
  transform: translateX(4px) scale(0.97);
}

.featured .card-link:hover span {
  transform: translate(2px, -2px);
}

.featured .card-link:active,
.featured .card button:active {
  transform: scale(0.97);
}

.featured::after {
  position: absolute;
  right: -38px;
  bottom: -50px;
  width: 170px;
  height: 170px;
  border: 1px solid rgba(255, 255, 255, 0.18);
  border-radius: 50%;
  content: "";
}

.featured .card-number,
.featured p {
  color: rgba(255, 255, 255, 0.58);
}

.featured .icon {
  background: rgba(255, 255, 255, 0.13);
  color: #fff;
}

.featured h3,
.featured button {
  color: #fff;
}

/* ---- bento sizes ---- */

.big {
  grid-column: span 6;
  grid-row: span 2;
}
.small {
  grid-column: span 3;
}
.wide {
  grid-column: span 6;
}
.medium {
  grid-column: span 9;
}

/* ---- horizontal layout (medium card) ---- */

.horizontal {
  flex-direction: row;
  align-items: center;
  gap: 28px;
}

.horizontal .card-top {
  flex-direction: column;
  align-items: flex-start;
  gap: 40px;
  flex-shrink: 0;
}

.horizontal .icon {
  width: 58px;
  height: 58px;
}

.horizontal .card-content {
  margin: 0;
  flex: 1;
  min-width: 0;
}

.horizontal h3 {
  margin-top: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.horizontal p {
  max-width: 540px;
}

.horizontal button {
  margin-top: 0;
  align-self: flex-end;
  flex-shrink: 0;
}

/* ---- responsive ---- */

@media (max-width: 900px) {
  .feature {
    padding: 72px 6%;
  }
  .heading {
    margin-bottom: 36px;
  }
  .heading h2 {
    margin-top: 16px;
  }
  .heading p {
    margin-top: 14px;
  }
  .bento {
    grid-template-columns: repeat(2, 1fr);
    grid-auto-rows: auto;
  }
  .big,
  .wide,
  .medium,
  .small {
    grid-column: span 1;
    grid-row: auto;
    min-height: 220px;
  }
  .horizontal {
    flex-direction: column;
    align-items: stretch;
    gap: 0;
  }
  .horizontal .card-top {
    flex-direction: row;
    gap: 20px;
  }
  .horizontal .card-content {
    margin-top: auto;
  }
  .horizontal h3 {
    margin-top: 14px;
  }
  .horizontal button {
    align-self: flex-start;
    margin-top: 16px;
  }
}

@media (max-width: 520px) {
  .feature {
    padding: 60px 5%;
  }
  .bento {
    display: flex;
    flex-direction: column;
  }
  .card {
    min-height: 200px;
  }
}
</style>
