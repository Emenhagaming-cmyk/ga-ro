<template>
  <section class="feature">
    <div class="feature-shell">
      <div class="heading">
        <div class="eyebrow"><span>01</span><span>Layanan Digital</span></div>
        <h2>Hal penting untuk<br><em>perjalananmu.</em></h2>
        <p>Satu ruang untuk menemukan informasi, layanan, dan peluang yang ada di SMK Bahrul Ulum.</p>
      </div>

      <div class="bento">
        <div
          v-for="(item, index) in items"
          :key="item.title"
          :class="['card', item.size, item.layout, { featured: index === 0 }]"
        >
          <div class="card-top">
            <span class="card-number">0{{ index + 1 }}</span>
            <div class="icon"><component :is="item.icon" :size="22" :stroke-width="1.8" /></div>
          </div>
          <div class="card-content">
            <h3>{{ item.title }}</h3>
            <p>{{ item.desc }}</p>
          </div>
          <button type="button">{{ item.button }} <span aria-hidden="true">↗</span></button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import {
  GraduationCap,
  BriefcaseBusiness,
  ShoppingBag,
  Newspaper,
  School,
  Info
} from "lucide-vue-next"

const items = [
  {
    title: "SPMB Online",
    desc: "Daftar peserta didik baru secara online dengan proses yang cepat dan mudah.",
    button: "Daftar sekarang",
    icon: GraduationCap,
    size: "big",
    layout: "vertical"
  },
  {
    title: "Berita",
    desc: "Kegiatan, kabar, dan pengumuman terbaru dari sekolah.",
    button: "Baca berita",
    icon: Newspaper,
    size: "small",
    layout: "vertical"
  },
  {
    title: "Career Center",
    desc: "Temukan informasi PKL dan peluang kerja untuk langkah berikutnya.",
    button: "Lihat peluang",
    icon: BriefcaseBusiness,
    size: "small",
    layout: "vertical"
  },
  {
    title: "Tentang Sekolah",
    desc: "Kenali lebih dekat profil, visi misi, dan cerita sekolah kami.",
    button: "Kenali kami",
    icon: School,
    size: "wide",
    layout: "vertical"
  },
  {
    title: "Koperasi Online",
    desc: "Belanja kebutuhan siswa dengan lebih praktis.",
    button: "Buka koperasi",
    icon: ShoppingBag,
    size: "small",
    layout: "vertical"
  },
  {
    title: "Produk Siswa",
    desc: "Lihat karya dan produk unggulan buatan siswa SMK Bahrul Ulum langsung dari galeri digital kami.",
    button: "Lihat karya",
    icon: Info,
    size: "medium",
    layout: "horizontal"
  }
]
</script>

<style scoped>
.feature {
  padding: 132px 7%;
  background: #f2f4f1;
  color: #1c2a23;
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
  margin: 0 auto 64px;
  text-align: center;
}

.eyebrow {
  display: flex;
  gap: 12px;
  align-items: center;
  justify-content: center;
  color: #3a6450;
  font-family: "Quicksand", sans-serif;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.15em;
  text-transform: uppercase;
}

.eyebrow span:first-child {
  color: #96a098;
  font-variant-numeric: tabular-nums;
}

.heading h2 {
  margin: 20px 0 0;
  font-family: "Quicksand", sans-serif;
  font-size: clamp(38px, 5vw, 66px);
  font-weight: 700;
  letter-spacing: -0.05em;
  line-height: 1.05;
}

.heading h2 em {
  color: #3a6450;
  font-family: inherit;
  font-style: normal;
  font-weight: 500;
}

.heading p {
  max-width: 520px;
  margin: 22px 0 0;
  color: #6c7a6e;
  font-family: "Quicksand", sans-serif;
  font-size: 15px;
  font-weight: 500;
  line-height: 1.75;
}

/* ---- bento grid ---- */

.bento {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  grid-auto-rows: 180px;
  gap: 14px;
}

.card {
  position: relative;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  padding: 26px;
  border: 1px solid #dfe4dd;
  border-radius: 6px;
  background: #fbfcfa;
  transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
}

.card:hover {
  z-index: 1;
  transform: translateY(-5px);
  box-shadow: 0 20px 40px rgba(35, 55, 42, 0.09);
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
}

.card h3 {
  margin: 18px 0 8px;
  color: #1a2620;
  font-size: 21px;
  font-weight: 650;
  letter-spacing: -0.025em;
}

.card p {
  max-width: 280px;
  margin: 0;
  color: #6f7e72;
  font-size: 13.5px;
  line-height: 1.62;
}

.card button {
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
}

.card button span {
  display: inline-block;
  margin-left: 5px;
  font-size: 16px;
  transition: transform 0.2s ease;
}

.card button:hover span {
  transform: translate(2px, -2px);
}

/* ---- featured (card-1 / SPMB) ---- */

.featured {
  border-color: #2b4a3c;
  background: #2b4a3c;
  color: #fff;
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

.big   { grid-column: span 6;  grid-row: span 2; }
.small { grid-column: span 3; }
.wide  { grid-column: span 6; }
.medium { grid-column: span 9; }

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
}

.horizontal .icon {
  width: 58px;
  height: 58px;
}

.horizontal .card-content {
  margin: 0;
  flex: 1;
}

.horizontal h3 {
  margin-top: 0;
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
  .feature { padding: 90px 6%; }
  .heading { margin-bottom: 42px; }
  .heading h2 { margin-top: 20px; }
  .heading p { margin-top: 20px; }
  .bento { grid-template-columns: repeat(2, 1fr); grid-auto-rows: auto; }
  .big, .wide, .medium, .small {
    grid-column: span 1;
    grid-row: auto;
    min-height: 250px;
  }
  .horizontal {
    flex-direction: column;
    align-items: stretch;
    gap: 0;
  }
  .horizontal .card-top { flex-direction: row; gap: 20px; }
  .horizontal .card-content { margin-top: auto; }
  .horizontal h3 { margin-top: 18px; }
  .horizontal button { align-self: flex-start; margin-top: 20px; }
}

@media (max-width: 520px) {
  .feature { padding: 74px 5%; }
  .bento { display: flex; flex-direction: column; }
  .card { min-height: 230px; }
}
</style>
