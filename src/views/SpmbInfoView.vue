<script setup>
import { ref, computed } from "vue";
import { useRouter } from "vue-router";
import { useAuthSession } from "@/composable/useAuthSession";
import {
  GraduationCap,
  Calculator,
  Calendar,
  FileText,
  Award,
  HelpCircle,
  Download,
  CheckCircle2,
  ChevronDown,
  ArrowRight,
  Sparkles,
  PhoneCall,
  Laptop,
  Network,
  Calculator as CalcIcon,
  ShieldCheck,
  Clock,
  Coins,
  Percent,
  Layers,
  FileCheck
} from "lucide-vue-next";

const router = useRouter();
const { spmbTarget } = useAuthSession();

function goBack() {
  if (window.history.length > 1) {
    router.back();
  } else {
    router.push("/");
  }
}

// Active quick nav section
const activeSection = ref("kalkulator");
function scrollTo(id) {
  activeSection.value = id;
  const el = document.getElementById(id);
  if (el) {
    el.scrollIntoView({ behavior: "smooth", block: "start" });
  }
}

// --- KALKULATOR BIAYA & BEASISWA STATE ---
const selectedMajor = ref("rpl");
const selectedWave = ref("gel1");
const selectedScholarship = ref("reguler");
const selectedPaymentPlan = ref("lunas");

const majors = {
  rpl: {
    id: "rpl",
    name: "Rekayasa Perangkat Lunak",
    short: "RPL",
    icon: Laptop,
    badge: "Favorit",
    spp: 350000,
    labFee: 500000,
    dsp: 3000000,
    desc: "Pemrograman Web, Mobile App, Game & Database modern.",
  },
  tkj: {
    id: "tkj",
    name: "Teknik Komputer & Jaringan",
    short: "TKJ",
    icon: Network,
    badge: "Industri 4.0",
    spp: 350000,
    labFee: 500000,
    dsp: 3000000,
    desc: "MikroTik, Cisco, Fiber Optic & Cloud Infrastructure.",
  },
  akl: {
    id: "akl",
    name: "Akuntansi Keuangan Lembaga",
    short: "AKL",
    icon: CalcIcon,
    badge: "Siap Kerja",
    spp: 300000,
    labFee: 400000,
    dsp: 2750000,
    desc: "Perbankan Mini, MYOB Digital, Pajak & Akuntansi Bisnis.",
  },
};

const waves = {
  gel1: {
    id: "gel1",
    name: "Gelombang 1 (Early Bird)",
    discountDsp: 750000,
    deadline: "31 Maret 2026",
    status: "Sedang Dibuka",
    badge: "Diskon Terbesar",
  },
  gel2: {
    id: "gel2",
    name: "Gelombang 2",
    discountDsp: 350000,
    deadline: "31 Mei 2026",
    status: "Segera Dibuka",
    badge: "Promo Menarik",
  },
  gel3: {
    id: "gel3",
    name: "Gelombang 3 (Reguler)",
    discountDsp: 0,
    deadline: "10 Juli 2026",
    status: "Penutupan",
    badge: "Kuota Terbatas",
  },
};

const scholarships = {
  reguler: {
    id: "reguler",
    name: "Jalur Reguler",
    desc: "Pendaftaran umum tanpa syarat prestasi khusus.",
    dspDiscountRate: 0,
    sppFreeMonths: 0,
  },
  rapor: {
    id: "rapor",
    name: "Prestasi Akademik (Rata-rata Rapor ≥ 85)",
    desc: "Potongan DSP 30% untuk lulusan berprestasi rapor SMP/MTs.",
    dspDiscountRate: 0.30,
    sppFreeMonths: 0,
  },
  juara: {
    id: "juara",
    name: "Prestasi Kejuaraan / Tahfidz ≥ 3 Juz",
    desc: "Potongan DSP 50% bagi peraih juara lomba atau penghafal Al-Qur'an.",
    dspDiscountRate: 0.50,
    sppFreeMonths: 1,
  },
  afirmasi: {
    id: "afirmasi",
    name: "Afirmasi / KIP / Yatim Piatu",
    desc: "Keringanan khusus potongan DSP 40% dan beasiswa perlengkapan.",
    dspDiscountRate: 0.40,
    sppFreeMonths: 2,
  },
  yayasan: {
    id: "yayasan",
    name: "Alumni Satu Yayasan / Saudara Kandung",
    desc: "Potongan DSP 20% bagi alumni SMP/MTs Bahrul Ulum atau saudara kandung.",
    dspDiscountRate: 0.20,
    sppFreeMonths: 0,
  },
};

const paymentPlans = {
  lunas: {
    id: "lunas",
    name: "Lunas di Awal",
    desc: "Diskon tambahan 5% dari total biaya masuk bersih.",
    cashbackRate: 0.05,
    installments: 1,
  },
  cicil3: {
    id: "cicil3",
    name: "Cicilan 3x (Semester 1)",
    desc: "Dibayar 3 kali: Saat Daftar Ulang, UTS, dan UAS Semester 1.",
    cashbackRate: 0,
    installments: 3,
  },
  cicil6: {
    id: "cicil6",
    name: "Cicilan 6x (Bulanan)",
    desc: "Angsuran terbagi rata selama 6 bulan pertama.",
    cashbackRate: 0,
    installments: 6,
  },
  cicil10: {
    id: "cicil10",
    name: "Cicilan 10x (Ringan 1 Tahun)",
    desc: "Skema paling ringan diangsur selama tahun pertama pembelajaran.",
    cashbackRate: 0,
    installments: 10,
  },
};

// Constant base costs
const UNIFORM_FEE = 850000; // 4 setel seragam + atribut & badge
const MPLS_INSURANCE_FEE = 200000; // MPLS, kartu pelajar digital, asuransi 1 thn

const calculation = computed(() => {
  const major = majors[selectedMajor.value];
  const wave = waves[selectedWave.value];
  const sch = scholarships[selectedScholarship.value];
  const plan = paymentPlans[selectedPaymentPlan.value];

  const baseDsp = major.dsp;
  const baseSpp = major.spp;
  const lab = major.labFee;
  const uniform = UNIFORM_FEE;
  const mpls = MPLS_INSURANCE_FEE;

  // Subtotal without discount
  const subtotalNormal = baseDsp + baseSpp + lab + uniform + mpls;

  // Discounts
  const waveDiscount = wave.discountDsp;
  const scholarshipDiscount = baseDsp * sch.dspDiscountRate;
  const sppDiscount = sch.sppFreeMonths * baseSpp;
  const initialDiscount = waveDiscount + scholarshipDiscount + sppDiscount;

  let totalAfterInitial = Math.max(0, subtotalNormal - initialDiscount);

  // Cash discount if lunas
  let cashDiscount = 0;
  if (plan.cashbackRate > 0) {
    cashDiscount = Math.round(totalAfterInitial * plan.cashbackRate);
    totalAfterInitial -= cashDiscount;
  }

  const totalDiscount = initialDiscount + cashDiscount;
  const finalTotal = totalAfterInitial;

  // Installment calculation
  let firstPayment = finalTotal;
  let perMonth = 0;

  if (plan.installments > 1) {
    // Pembayaran pertama minimal seragam + mpls + SPP + sebagian DSP
    const mandatoryFirst = uniform + mpls + baseSpp;
    const remainingDspLab = Math.max(0, finalTotal - mandatoryFirst);
    firstPayment = Math.round(mandatoryFirst + remainingDspLab / plan.installments);
    perMonth = Math.round(finalTotal / plan.installments);
  }

  return {
    baseDsp,
    baseSpp,
    lab,
    uniform,
    mpls,
    subtotalNormal,
    waveDiscount,
    scholarshipDiscount,
    sppDiscount,
    cashDiscount,
    totalDiscount,
    finalTotal,
    firstPayment,
    perMonth,
    installments: plan.installments,
  };
});

function formatRupiah(num) {
  return "Rp " + (num || 0).toLocaleString("id-ID");
}

const waConsultUrl = computed(() => {
  const major = majors[selectedMajor.value].name;
  const wave = waves[selectedWave.value].name;
  const sch = scholarships[selectedScholarship.value].name;
  const total = formatRupiah(calculation.value.finalTotal);
  const plan = paymentPlans[selectedPaymentPlan.value].name;

  const msg = encodeURIComponent(
    `Halo Panitia SPMB SMK Bahrul Ulum Surabaya, saya tertarik mendaftar dengan rincian:\n` +
    `- Jurusan: ${major}\n` +
    `- Gelombang: ${wave}\n` +
    `- Jalur: ${sch}\n` +
    `- Skema: ${plan}\n` +
    `- Estimasi Total Biaya: ${total}\n\n` +
    `Mohon informasi langkah pendaftaran dan verifikasi berkas selanjutnya. Terima kasih!`
  );
  return `https://wa.me/6281234567890?text=${msg}`;
});

// --- TIMELINE DATA ---
const timelineSteps = [
  {
    step: "01",
    title: "Pendaftaran Akun & Form Online",
    desc: "Calon peserta didik membuat akun dan mengisi data diri, data orang tua, serta memilih jurusan di sistem SPMB.",
    date: "Februari – Juli 2026",
    tag: "Online",
  },
  {
    step: "02",
    title: "Unggah Berkas Persyaratan",
    desc: "Upload softcopy Pas Foto 3x4, Kartu Keluarga (KK), Ijazah/SKL sementara, dan berkas pendukung beasiswa jika ada.",
    date: "1x24 Jam setelah daftar",
    tag: "Mandiri",
  },
  {
    step: "03",
    title: "Verifikasi Berkas & Tes Minat Bakat",
    desc: "Panitia memeriksa keabsahan dokumen dan calon siswa mengikuti tes peminatan kejuruan (wawancara & tes logika dasar).",
    date: "Jadwal ditentukan panitia",
    tag: "Online / Tatap Muka",
  },
  {
    step: "04",
    title: "Pengumuman Hasil Seleksi",
    desc: "Hasil seleksi langsung dapat dicek realtime melalui Dashboard Siswa atau halaman pengumuman.",
    date: "1–2 Hari Kerja",
    tag: "Dashboard Siswa",
  },
  {
    step: "05",
    title: "Daftar Ulang & Pengambilan Seragam",
    desc: "Penyelesaian administrasi biaya masuk sesuai skema pilihan dan pengukuran/pengambilan 4 setel seragam sekolah.",
    date: "Juli 2026",
    tag: "Sekolah",
  },
];

// --- REQUIREMENTS DATA ---
const reqGeneral = [
  "Lulusan SMP/MTs/Paket B atau sederajat (tahun lulus 2024, 2025, atau 2026).",
  "Berusia setinggi-tingginya 21 tahun pada awal tahun pelajaran baru.",
  "Sehat jasmani dan rohani, tidak buta warna (khusus kejuruan RPL & TKJ).",
  "Berkelakuan baik dan bersedia menaati seluruh tata tertib SMK Bahrul Ulum.",
];

const reqDocuments = [
  { name: "Scan Kartu Keluarga (KK)", req: "Wajib", note: "Format JPG/PNG/PDF maks 2MB" },
  { name: "Pas Foto Berwarna 3x4", req: "Wajib", note: "Background merah/biru, rapi berseragam" },
  { name: "Scan Ijazah / SKL / Rapor Semester 1-5", req: "Wajib", note: "Surat Keterangan Lulus jika ijazah belum terbit" },
  { name: "Scan Akta Kelahiran", req: "Wajib", note: "Sebagai verifikasi data kependudukan" },
  { name: "Sertifikat / Piagam Prestasi", req: "Opsional", note: "Khusus pendaftar Jalur Prestasi / Tahfidz" },
  { name: "Kartu KIP / SKTM dari Kelurahan", req: "Opsional", note: "Khusus pendaftar Jalur Afirmasi" },
];

// --- FAQ ACCORDION ---
const faqs = ref([
  {
    q: "Apakah ijazah yang belum keluar bisa mendaftar?",
    a: "Bisa! Calon siswa cukup melampirkan Surat Keterangan Lulus (SKL) sementara dari SMP/MTs asal atau fotokopi nilai rapor semester 1–5.",
    open: true,
  },
  {
    q: "Apakah biaya masuk dapat diangsur/dicicil?",
    a: "Sangat bisa. SMK Bahrul Ulum menyediakan skema cicilan 3x, 6x, hingga 10x per bulan agar tidak memberatkan orang tua/wali murid.",
    open: false,
  },
  {
    q: "Bagaimana jika saya ingin berkonsultasi mengenai beasiswa?",
    a: "Anda dapat menggunakan kalkulator simulasi beasiswa di atas dan langsung menekan tombol 'Konsultasi WhatsApp' untuk terhubung langsung dengan tim panitia SPMB.",
    open: false,
  },
  {
    q: "Apakah ada tes seleksi masuk di SMK Bahrul Ulum?",
    a: "Tes yang dilakukan adalah Tes Minat Bakat dan Wawancara Kejuruan. Tes ini bertujuan untuk mencocokkan potensi siswa dengan jurusan (RPL, TKJ, atau AKL) yang dipilih.",
    open: false,
  },
  {
    q: "Kapan batas akhir pendaftaran Gelombang 1?",
    a: "Gelombang 1 dibuka hingga 31 Maret 2026 dengan potongan biaya DSP terbesar senilai Rp 750.000. Kuota per kelas terbatas 36 siswa.",
    open: false,
  },
]);

function toggleFaq(index) {
  faqs.value[index].open = !faqs.value[index].open;
}
</script>

<template>
  <section class="spmb-info-page">
    <!-- Top Navigation Bar -->
    <div class="top-bar">
      <button type="button" class="back-button" @click="goBack">
        <span class="back-icon">&lt;</span>
        <span>Kembali ke Beranda</span>
      </button>
      <div class="top-badge">
        <Sparkles :size="14" />
        <span>SPMB Tahun Pelajaran 2026/2027</span>
      </div>
    </div>

    <!-- Header Banner -->
    <header class="page-header">
      <div class="header-main">
        <span class="page-label">Pusat Informasi &amp; Biaya</span>
        <h1>Panduan SPMB, Rincian Biaya &amp; Beasiswa</h1>
        <p>
          Temukan informasi transparan alur pendaftaran, kuota jurusan, syarat berkas, serta simulasi biaya pendidikan dan beasiswa secara instan di SMK Bahrul Ulum Surabaya.
        </p>
      </div>

      <div class="header-stats">
        <div class="stat-pill highlight">
          <span class="stat-label">Gelombang Aktif</span>
          <strong>Gelombang 1</strong>
          <span class="stat-sub">Diskon DSP Rp 750rb</span>
        </div>
        <div class="stat-pill">
          <span class="stat-label">Pilihan Jurusan</span>
          <strong>3 Kejuruan</strong>
          <span class="stat-sub">RPL · TKJ · AKL</span>
        </div>
        <div class="stat-pill">
          <span class="stat-label">Kemudahan Bayar</span>
          <strong>Cicilan s/d 10x</strong>
          <span class="stat-sub">Bebas Bunga</span>
        </div>
      </div>
    </header>

    <!-- Quick Navigation Pills -->
    <nav class="quick-nav">
      <button
        type="button"
        class="nav-pill"
        :class="{ active: activeSection === 'kalkulator' }"
        @click="scrollTo('kalkulator')"
      >
        <Calculator :size="16" />
        <span>Kalkulator Biaya</span>
      </button>
      <button
        type="button"
        class="nav-pill"
        :class="{ active: activeSection === 'alur' }"
        @click="scrollTo('alur')"
      >
        <Calendar :size="16" />
        <span>Alur Pendaftaran</span>
      </button>
      <button
        type="button"
        class="nav-pill"
        :class="{ active: activeSection === 'syarat' }"
        @click="scrollTo('syarat')"
      >
        <FileText :size="16" />
        <span>Syarat &amp; Dokumen</span>
      </button>
      <button
        type="button"
        class="nav-pill"
        :class="{ active: activeSection === 'jurusan' }"
        @click="scrollTo('jurusan')"
      >
        <GraduationCap :size="16" />
        <span>Jurusan &amp; Kuota</span>
      </button>
      <button
        type="button"
        class="nav-pill"
        :class="{ active: activeSection === 'brosur' }"
        @click="scrollTo('brosur')"
      >
        <Download :size="16" />
        <span>Unduh Brosur</span>
      </button>
      <button
        type="button"
        class="nav-pill"
        :class="{ active: activeSection === 'faq' }"
        @click="scrollTo('faq')"
      >
        <HelpCircle :size="16" />
        <span>FAQ</span>
      </button>
    </nav>

    <!-- SECTION 1: KALKULATOR BIAYA & SIMULASI BEASISWA -->
    <section id="kalkulator" class="content-section calc-section">
      <div class="section-heading">
        <div class="heading-badge">
          <Calculator :size="14" />
          <span>Simulasi Biaya Cerdas</span>
        </div>
        <h2>Kalkulator Estimasi Biaya &amp; Beasiswa</h2>
        <p>Pilih jurusan, gelombang, dan jalur beasiswa Anda untuk melihat rincian biaya yang transparan dan skema cicilan yang fleksibel.</p>
      </div>

      <div class="calc-grid">
        <!-- Controls Left -->
        <div class="calc-controls">
          <!-- Step A: Jurusan -->
          <div class="control-group">
            <label class="control-label">
              <span class="step-num">1</span>
              <span>Pilih Program Keahlian / Jurusan</span>
            </label>
            <div class="major-options">
              <button
                v-for="m in Object.values(majors)"
                :key="m.id"
                type="button"
                class="option-card"
                :class="{ selected: selectedMajor === m.id }"
                @click="selectedMajor = m.id"
              >
                <div class="option-header">
                  <component :is="m.icon" :size="20" class="option-icon" />
                  <span class="option-badge">{{ m.badge }}</span>
                </div>
                <strong class="option-title">{{ m.short }} - {{ m.name }}</strong>
                <p class="option-desc">{{ m.desc }}</p>
                <div class="option-meta">
                  <span>SPP: {{ formatRupiah(m.spp) }}/bln</span>
                </div>
              </button>
            </div>
          </div>

          <!-- Step B: Gelombang -->
          <div class="control-group">
            <label class="control-label">
              <span class="step-num">2</span>
              <span>Pilih Gelombang Pendaftaran</span>
            </label>
            <div class="wave-options">
              <button
                v-for="w in Object.values(waves)"
                :key="w.id"
                type="button"
                class="wave-card"
                :class="{ selected: selectedWave === w.id }"
                @click="selectedWave = w.id"
              >
                <div class="wave-head">
                  <strong>{{ w.name }}</strong>
                  <span class="wave-tag" v-if="w.discountDsp > 0">Hemat {{ formatRupiah(w.discountDsp) }}</span>
                </div>
                <span class="wave-date">Batas: {{ w.deadline }}</span>
              </button>
            </div>
          </div>

          <!-- Step C: Kategori Jalur & Beasiswa -->
          <div class="control-group">
            <label class="control-label">
              <span class="step-num">3</span>
              <span>Pilih Jalur Masuk / Potongan Beasiswa</span>
            </label>
            <div class="select-wrapper">
              <select v-model="selectedScholarship" class="custom-select">
                <option v-for="sch in Object.values(scholarships)" :key="sch.id" :value="sch.id">
                  {{ sch.name }}
                </option>
              </select>
            </div>
            <p class="field-hint">
              {{ scholarships[selectedScholarship].desc }}
            </p>
          </div>

          <!-- Step D: Skema Pembayaran -->
          <div class="control-group">
            <label class="control-label">
              <span class="step-num">4</span>
              <span>Pilih Skema Pembayaran Biaya Masuk</span>
            </label>
            <div class="payment-options">
              <button
                v-for="p in Object.values(paymentPlans)"
                :key="p.id"
                type="button"
                class="plan-btn"
                :class="{ selected: selectedPaymentPlan === p.id }"
                @click="selectedPaymentPlan = p.id"
              >
                <strong>{{ p.name }}</strong>
                <span v-if="p.cashbackRate > 0" class="plan-badge">Diskon 5%</span>
                <span class="plan-desc">{{ p.desc }}</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Output Result Right (Sticky Card) -->
        <aside class="calc-result-panel">
          <div class="result-card">
            <div class="result-header">
              <div class="result-title">
                <FileCheck :size="18" />
                <span>Rincian Estimasi Biaya</span>
              </div>
              <span class="result-badge-jurusan">{{ majors[selectedMajor].short }}</span>
            </div>

            <!-- Itemized Costs -->
            <div class="cost-items">
              <div class="cost-row">
                <span class="cost-name">Dana Pengembangan (DSP/Gedung)</span>
                <span class="cost-value">{{ formatRupiah(calculation.baseDsp) }}</span>
              </div>
              <div class="cost-row">
                <span class="cost-name">SPP Bulan Pertama (Juli 2026)</span>
                <span class="cost-value">{{ formatRupiah(calculation.baseSpp) }}</span>
              </div>
              <div class="cost-row">
                <span class="cost-name">Paket 4 Setel Seragam &amp; Atribut</span>
                <span class="cost-value">{{ formatRupiah(calculation.uniform) }}</span>
              </div>
              <div class="cost-row">
                <span class="cost-name">Modul Pembelajaran &amp; Praktikum Lab (1 Thn)</span>
                <span class="cost-value">{{ formatRupiah(calculation.lab) }}</span>
              </div>
              <div class="cost-row">
                <span class="cost-name">MPLS, Kartu Pelajar &amp; Asuransi</span>
                <span class="cost-value">{{ formatRupiah(calculation.mpls) }}</span>
              </div>

              <div class="cost-divider"></div>

              <div class="cost-row subtotal">
                <span>Subtotal Biaya Standar</span>
                <span>{{ formatRupiah(calculation.subtotalNormal) }}</span>
              </div>

              <!-- Discounts -->
              <div v-if="calculation.waveDiscount > 0" class="cost-row discount">
                <span>Potongan Gelombang</span>
                <span>- {{ formatRupiah(calculation.waveDiscount) }}</span>
              </div>
              <div v-if="calculation.scholarshipDiscount > 0" class="cost-row discount">
                <span>Beasiswa {{ scholarships[selectedScholarship].name }}</span>
                <span>- {{ formatRupiah(calculation.scholarshipDiscount) }}</span>
              </div>
              <div v-if="calculation.sppDiscount > 0" class="cost-row discount">
                <span>Bebas SPP {{ scholarships[selectedScholarship].sppFreeMonths }} Bulan</span>
                <span>- {{ formatRupiah(calculation.sppDiscount) }}</span>
              </div>
              <div v-if="calculation.cashDiscount > 0" class="cost-row discount">
                <span>Potongan Pelunasan Awal (5%)</span>
                <span>- {{ formatRupiah(calculation.cashDiscount) }}</span>
              </div>
            </div>

            <!-- Total Box -->
            <div class="total-box">
              <div class="total-label-row">
                <span>Total Estimasi Biaya Bersih</span>
                <span class="save-badge" v-if="calculation.totalDiscount > 0">
                  Hemat {{ formatRupiah(calculation.totalDiscount) }}
                </span>
              </div>
              <div class="total-amount">{{ formatRupiah(calculation.finalTotal) }}</div>

              <!-- Installment Simulation -->
              <div v-if="calculation.installments > 1" class="installment-info">
                <div class="install-row">
                  <span>Pembayaran Awal / Daftar Ulang:</span>
                  <strong>{{ formatRupiah(calculation.firstPayment) }}</strong>
                </div>
                <div class="install-row">
                  <span>Angsuran per Termin ({{ calculation.installments }}x):</span>
                  <strong>{{ formatRupiah(calculation.perMonth) }} / bulan</strong>
                </div>
              </div>
            </div>

            <!-- Action CTAs -->
            <div class="result-actions">
              <a :href="spmbTarget()" class="btn-daftar-now">
                <span>Daftar Online Sekarang</span>
                <ArrowRight :size="16" />
              </a>

              <a :href="waConsultUrl" target="_blank" rel="noopener noreferrer" class="btn-wa-consult">
                <PhoneCall :size="16" />
                <span>Konsultasi Rincian via WA</span>
              </a>
            </div>

            <p class="result-note">
              *Estimasi di atas bersifat simulasi resmi. Biaya final divalidasi oleh panitia SPMB saat verifikasi berkas asli.
            </p>
          </div>
        </aside>
      </div>
    </section>

    <!-- SECTION 2: ALUR PENDAFTARAN -->
    <section id="alur" class="content-section">
      <div class="section-heading">
        <div class="heading-badge">
          <Calendar :size="14" />
          <span>Langkah Mudah</span>
        </div>
        <h2>Alur &amp; Prosedur Pendaftaran SPMB</h2>
        <p>5 langkah mudah dan terstruktur untuk bergabung menjadi peserta didik baru SMK Bahrul Ulum.</p>
      </div>

      <div class="timeline-container">
        <div
          v-for="(item, idx) in timelineSteps"
          :key="item.step"
          class="timeline-card"
        >
          <div class="timeline-step-header">
            <span class="step-badge">{{ item.step }}</span>
            <span class="step-tag">{{ item.tag }}</span>
          </div>
          <h3>{{ item.title }}</h3>
          <p>{{ item.desc }}</p>
          <div class="step-footer">
            <Clock :size="14" />
            <span>{{ item.date }}</span>
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION 3: SYARAT & DOKUMEN -->
    <section id="syarat" class="content-section">
      <div class="section-heading">
        <div class="heading-badge">
          <FileText :size="14" />
          <span>Administrasi</span>
        </div>
        <h2>Persyaratan &amp; Dokumen Wajib</h2>
        <p>Pastikan Anda telah menyiapkan dokumen kelengkapan berkas sebelum mengisi formulir.</p>
      </div>

      <div class="requirements-grid">
        <!-- Syarat Umum -->
        <div class="req-box">
          <div class="req-header">
            <ShieldCheck :size="20" class="req-icon" />
            <h3>Ketentuan &amp; Syarat Umum</h3>
          </div>
          <ul class="req-list">
            <li v-for="(rule, idx) in reqGeneral" :key="idx">
              <CheckCircle2 :size="16" class="check-icon" />
              <span>{{ rule }}</span>
            </li>
          </ul>
        </div>

        <!-- Berkas Dokumen -->
        <div class="req-box">
          <div class="req-header">
            <Layers :size="20" class="req-icon" />
            <h3>Dokumen yang Perlu Diunggah</h3>
          </div>
          <div class="doc-table">
            <div v-for="doc in reqDocuments" :key="doc.name" class="doc-item">
              <div class="doc-info">
                <strong>{{ doc.name }}</strong>
                <span>{{ doc.note }}</span>
              </div>
              <span class="doc-badge" :class="{ required: doc.req === 'Wajib' }">
                {{ doc.req }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION 4: JURUSAN & KUOTA -->
    <section id="jurusan" class="content-section">
      <div class="section-heading">
        <div class="heading-badge">
          <GraduationCap :size="14" />
          <span>Program Kejuruan</span>
        </div>
        <h2>Kompetensi Keahlian &amp; Kuota Kelas</h2>
        <p>SMK Bahrul Ulum menghadirkan kurikulum berbasis industri dengan sertifikasi keahlian terstandar nasional.</p>
      </div>

      <div class="majors-showcase">
        <div class="major-card">
          <div class="major-head">
            <Laptop :size="28" class="m-icon" />
            <div>
              <span class="m-tag">Terakreditasi A</span>
              <h3>Rekayasa Perangkat Lunak (RPL)</h3>
            </div>
          </div>
          <p class="m-desc">
            Mempelajari logika pemrograman, fullstack web development, mobile app (Flutter/Kotlin), cloud database, UI/UX design, dan AI prompt integration.
          </p>
          <div class="m-details">
            <div class="m-stat">
              <span>Daya Tampung:</span>
              <strong>72 Siswa (2 Kelas)</strong>
            </div>
            <div class="m-stat">
              <span>Mitra Industri:</span>
              <strong>PT Telkom, PT Software House, Startup</strong>
            </div>
            <div class="m-stat">
              <span>Prospek Karir:</span>
              <strong>Web/Mobile Developer, Software Tester, UI Designer</strong>
            </div>
          </div>
        </div>

        <div class="major-card">
          <div class="major-head">
            <Network :size="28" class="m-icon" />
            <div>
              <span class="m-tag">Terakreditasi A</span>
              <h3>Teknik Komputer &amp; Jaringan (TKJ)</h3>
            </div>
          </div>
          <p class="m-desc">
            Fokus pada perakitan komputer, instalasi jaringan Fiber Optic, routing MikroTik &amp; Cisco, cyber security dasar, dan administrasi server Linux/Cloud.
          </p>
          <div class="m-details">
            <div class="m-stat">
              <span>Daya Tampung:</span>
              <strong>72 Siswa (2 Kelas)</strong>
            </div>
            <div class="m-stat">
              <span>Mitra Industri:</span>
              <strong>ISP Provider, PT Fiber Connect, Datacenter</strong>
            </div>
            <div class="m-stat">
              <span>Prospek Karir:</span>
              <strong>Network Engineer, IT Support, Fiber Tech, SysAdmin</strong>
            </div>
          </div>
        </div>

        <div class="major-card">
          <div class="major-head">
            <CalcIcon :size="28" class="m-icon" />
            <div>
              <span class="m-tag">Terakreditasi A</span>
              <h3>Akuntansi &amp; Keuangan Lembaga (AKL)</h3>
            </div>
          </div>
          <p class="m-desc">
            Menguasai pembukuan keuangan modern, software MYOB &amp; Accurate, pengelolaan kas mini bank sekolah, perpajakan e-Faktur, dan audit keuangan lembaga.
          </p>
          <div class="m-details">
            <div class="m-stat">
              <span>Daya Tampung:</span>
              <strong>36 Siswa (1 Kelas)</strong>
            </div>
            <div class="m-stat">
              <span>Mitra Industri:</span>
              <strong>Bank Jatim, BMT Mitra, Kantor Akuntan Publik</strong>
            </div>
            <div class="m-stat">
              <span>Prospek Karir:</span>
              <strong>Staff Akunting, Teller Bank, Staff Pajak, Kasir</strong>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION 5: UNDUH BROSUR -->
    <section id="brosur" class="content-section download-section">
      <div class="download-banner">
        <div class="download-content">
          <div class="heading-badge light">
            <Download :size="14" />
            <span>Dokumen Resmi</span>
          </div>
          <h2>Unduh Brosur &amp; Panduan Pendaftaran SPMB</h2>
          <p>
            Dapatkan informasi lengkap format cetak PDF yang dapat dibagikan kepada orang tua atau keluarga mengenai rincian program studi, fasilitas, dan jadwal SPMB.
          </p>
          <div class="download-buttons">
            <a href="/data/news.json" download="Brosur_SPMB_SMK_Bahrul_Ulum.pdf" class="btn-download-primary">
              <Download :size="18" />
              <span>Unduh Brosur SPMB 2026 (PDF)</span>
            </a>
            <a href="/data/news.json" download="Panduan_Tata_Tertib_SPMB.pdf" class="btn-download-outline">
              <FileText :size="18" />
              <span>Panduan Seragam &amp; Tata Tertib</span>
            </a>
          </div>
        </div>
        <div class="download-decor" aria-hidden="true">
          <div class="doc-mockup">
            <div class="doc-mockup-head"></div>
            <div class="doc-mockup-body"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION 6: FAQ -->
    <section id="faq" class="content-section">
      <div class="section-heading">
        <div class="heading-badge">
          <HelpCircle :size="14" />
          <span>Tanya Jawab</span>
        </div>
        <h2>Pertanyaan yang Sering Diajukan</h2>
        <p>Pertanyaan umum seputar pendaftaran, tes seleksi, dan biaya di SMK Bahrul Ulum.</p>
      </div>

      <div class="faq-list">
        <div
          v-for="(faq, idx) in faqs"
          :key="idx"
          class="faq-card"
          :class="{ open: faq.open }"
        >
          <button type="button" class="faq-trigger" @click="toggleFaq(idx)">
            <span class="faq-q">{{ faq.q }}</span>
            <ChevronDown :size="18" class="faq-chevron" />
          </button>
          <div v-show="faq.open" class="faq-answer">
            <p>{{ faq.a }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- BOTTOM CALL TO ACTION -->
    <div class="bottom-cta">
      <div class="cta-inner">
        <h2>Siap Bergabung dengan SMK Bahrul Ulum?</h2>
        <p>Pendaftaran Gelombang 1 kuota terbatas. Amankan kursi kejuruan favoritmu sekarang!</p>
        <div class="cta-actions">
          <a :href="spmbTarget()" class="btn-cta-main">
            <span>Daftar SPMB Online Sekarang</span>
            <ArrowRight :size="18" />
          </a>
          <a :href="waConsultUrl" target="_blank" rel="noopener noreferrer" class="btn-cta-secondary">
            <PhoneCall :size="18" />
            <span>Chat Panitia WhatsApp</span>
          </a>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.spmb-info-page {
  padding: 80px 7% 100px;
  min-height: 100vh;
  min-height: 100dvh;
  background: var(--background-page, #eef4ec);
  color: var(--text, #1c2a23);
  font-family: inherit;
}

/* Top bar */
.top-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 24px;
  flex-wrap: wrap;
}

.back-button {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  border: 1px solid rgba(47, 91, 58, 0.18);
  background: var(--surface, #ffffff);
  color: var(--primary, #3a6450);
  border-radius: var(--radius-pill, 999px);
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.back-button:hover {
  background: rgba(58, 100, 80, 0.08);
  transform: translateY(-1px);
}

.back-icon {
  font-size: 16px;
  line-height: 1;
}

.top-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  background: rgba(58, 100, 80, 0.1);
  color: var(--primary, #3a6450);
  border-radius: var(--radius-pill, 999px);
  font-size: 13px;
  font-weight: 700;
}

/* Page Header */
.page-header {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 32px;
  align-items: center;
  padding: 36px 40px;
  background: var(--surface, #ffffff);
  border: 1px solid var(--border, #dfe4dd);
  border-radius: var(--radius-xl, 24px);
  box-shadow: var(--shadow-sm, 0 4px 12px rgba(35, 55, 42, 0.05));
  margin-bottom: 28px;
}

.page-label {
  display: inline-block;
  font-size: 12px;
  font-weight: 800;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--primary, #3a6450);
  margin-bottom: 8px;
}

.header-main h1 {
  font-size: 32px;
  font-weight: 800;
  line-height: 1.25;
  color: var(--text, #1c2a23);
  margin: 0 0 12px;
  letter-spacing: -0.02em;
}

.header-main p {
  font-size: 15px;
  line-height: 1.6;
  color: var(--text-secondary, #647067);
  max-width: 680px;
  margin: 0;
}

.header-stats {
  display: flex;
  gap: 12px;
}

.stat-pill {
  display: flex;
  flex-direction: column;
  padding: 16px 20px;
  background: var(--background, #f2f4f1);
  border: 1px solid var(--border, #dfe4dd);
  border-radius: var(--radius-lg, 20px);
  min-width: 140px;
}

.stat-pill.highlight {
  background: var(--primary-light, #e8f0e6);
  border-color: rgba(58, 100, 80, 0.25);
}

.stat-label {
  font-size: 11px;
  text-transform: uppercase;
  font-weight: 700;
  color: var(--text-secondary, #647067);
  margin-bottom: 4px;
}

.stat-pill strong {
  font-size: 18px;
  font-weight: 800;
  color: var(--primary, #3a6450);
  line-height: 1.2;
}

.stat-sub {
  font-size: 12px;
  color: var(--text-secondary, #647067);
  margin-top: 2px;
}

/* Quick Navigation */
.quick-nav {
  display: flex;
  gap: 10px;
  overflow-x: auto;
  padding: 6px 0 24px;
  margin-bottom: 8px;
  scrollbar-width: none;
}

.quick-nav::-webkit-scrollbar {
  display: none;
}

.nav-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  background: var(--surface, #ffffff);
  border: 1px solid var(--border, #dfe4dd);
  border-radius: var(--radius-pill, 999px);
  font-size: 14px;
  font-weight: 700;
  color: var(--text-secondary, #647067);
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.2s ease;
}

.nav-pill:hover {
  color: var(--primary, #3a6450);
  border-color: var(--primary, #3a6450);
}

.nav-pill.active {
  background: var(--primary, #3a6450);
  color: #ffffff;
  border-color: var(--primary, #3a6450);
  box-shadow: 0 4px 12px rgba(58, 100, 80, 0.2);
}

/* Content Sections */
.content-section {
  padding: 40px;
  background: var(--surface, #ffffff);
  border: 1px solid var(--border, #dfe4dd);
  border-radius: var(--radius-xl, 24px);
  box-shadow: var(--shadow-sm, 0 4px 12px rgba(35, 55, 42, 0.05));
  margin-bottom: 36px;
}

.section-heading {
  margin-bottom: 32px;
}

.heading-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 12px;
  background: rgba(58, 100, 80, 0.1);
  color: var(--primary, #3a6450);
  border-radius: var(--radius-pill, 999px);
  font-size: 12px;
  font-weight: 700;
  margin-bottom: 8px;
}

.heading-badge.light {
  background: rgba(255, 255, 255, 0.2);
  color: #ffffff;
}

.section-heading h2 {
  font-size: 26px;
  font-weight: 800;
  color: var(--text, #1c2a23);
  margin: 0 0 8px;
  letter-spacing: -0.02em;
}

.section-heading p {
  font-size: 15px;
  color: var(--text-secondary, #647067);
  margin: 0;
  max-width: 650px;
}

/* --- KALKULATOR SECTION STYLES --- */
.calc-grid {
  display: grid;
  grid-template-columns: 1fr 420px;
  gap: 32px;
  align-items: start;
}

.calc-controls {
  display: flex;
  flex-direction: column;
  gap: 28px;
}

.control-group {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.control-label {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 15px;
  font-weight: 800;
  color: var(--text, #1c2a23);
}

.step-num {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: var(--primary, #3a6450);
  color: #ffffff;
  font-size: 12px;
  font-weight: 800;
}

/* Major Cards */
.major-options {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}

.option-card {
  display: flex;
  flex-direction: column;
  text-align: left;
  padding: 16px;
  background: var(--surface-soft, #fbfcfa);
  border: 2px solid var(--border, #dfe4dd);
  border-radius: var(--radius, 16px);
  cursor: pointer;
  transition: all 0.2s ease;
}

.option-card:hover {
  border-color: rgba(58, 100, 80, 0.4);
  transform: translateY(-2px);
}

.option-card.selected {
  background: var(--primary-light, #e8f0e6);
  border-color: var(--primary, #3a6450);
  box-shadow: 0 4px 14px rgba(58, 100, 80, 0.12);
}

.option-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
}

.option-icon {
  color: var(--primary, #3a6450);
}

.option-badge {
  font-size: 10px;
  font-weight: 800;
  padding: 2px 6px;
  background: rgba(58, 100, 80, 0.15);
  color: var(--primary, #3a6450);
  border-radius: 6px;
}

.option-title {
  font-size: 14px;
  font-weight: 800;
  color: var(--text, #1c2a23);
  margin-bottom: 4px;
  line-height: 1.3;
}

.option-desc {
  font-size: 12px;
  color: var(--text-secondary, #647067);
  margin: 0 0 10px;
  line-height: 1.4;
  flex-grow: 1;
}

.option-meta {
  font-size: 12px;
  font-weight: 700;
  color: var(--primary, #3a6450);
}

/* Wave Options */
.wave-options {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}

.wave-card {
  display: flex;
  flex-direction: column;
  text-align: left;
  padding: 14px 16px;
  background: var(--surface-soft, #fbfcfa);
  border: 2px solid var(--border, #dfe4dd);
  border-radius: var(--radius, 16px);
  cursor: pointer;
  transition: all 0.2s ease;
}

.wave-card:hover {
  border-color: rgba(58, 100, 80, 0.4);
}

.wave-card.selected {
  background: var(--primary-light, #e8f0e6);
  border-color: var(--primary, #3a6450);
}

.wave-head {
  display: flex;
  flex-direction: column;
  gap: 4px;
  margin-bottom: 6px;
}

.wave-head strong {
  font-size: 13px;
  font-weight: 800;
  color: var(--text, #1c2a23);
}

.wave-tag {
  font-size: 11px;
  font-weight: 800;
  color: #166534;
  background: #dcfce7;
  padding: 2px 6px;
  border-radius: 4px;
  width: fit-content;
}

.wave-date {
  font-size: 11px;
  color: var(--text-secondary, #647067);
}

/* Select & Hint */
.select-wrapper {
  position: relative;
}

.custom-select {
  width: 100%;
  padding: 14px 16px;
  background: var(--surface-soft, #fbfcfa);
  border: 2px solid var(--border, #dfe4dd);
  border-radius: var(--radius, 16px);
  font-size: 14px;
  font-weight: 700;
  color: var(--text, #1c2a23);
  outline: none;
  cursor: pointer;
  transition: border-color 0.2s;
}

.custom-select:focus {
  border-color: var(--primary, #3a6450);
}

.field-hint {
  font-size: 13px;
  color: var(--text-secondary, #647067);
  margin: 0;
  font-style: italic;
}

/* Payment Plans */
.payment-options {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
}

.plan-btn {
  display: flex;
  flex-direction: column;
  text-align: left;
  padding: 14px 16px;
  background: var(--surface-soft, #fbfcfa);
  border: 2px solid var(--border, #dfe4dd);
  border-radius: var(--radius, 16px);
  cursor: pointer;
  transition: all 0.2s ease;
}

.plan-btn:hover {
  border-color: rgba(58, 100, 80, 0.4);
}

.plan-btn.selected {
  background: var(--primary-light, #e8f0e6);
  border-color: var(--primary, #3a6450);
}

.plan-btn strong {
  font-size: 14px;
  font-weight: 800;
  color: var(--text, #1c2a23);
  margin-bottom: 2px;
}

.plan-badge {
  font-size: 10px;
  font-weight: 800;
  color: #166534;
  background: #dcfce7;
  padding: 2px 6px;
  border-radius: 4px;
  width: fit-content;
  margin-bottom: 4px;
}

.plan-desc {
  font-size: 11px;
  color: var(--text-secondary, #647067);
  line-height: 1.3;
}

/* --- RESULT CARD STICKY PANEL --- */
.calc-result-panel {
  position: sticky;
  top: 100px;
}

.result-card {
  background: var(--surface, #ffffff);
  border: 2px solid rgba(58, 100, 80, 0.2);
  border-radius: var(--radius-xl, 24px);
  padding: 24px;
  box-shadow: 0 12px 32px rgba(35, 55, 42, 0.08);
}

.result-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-bottom: 16px;
  border-bottom: 1px solid var(--border, #dfe4dd);
  margin-bottom: 16px;
}

.result-title {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 16px;
  font-weight: 800;
  color: var(--text, #1c2a23);
}

.result-badge-jurusan {
  font-size: 12px;
  font-weight: 800;
  padding: 4px 10px;
  background: var(--primary, #3a6450);
  color: #ffffff;
  border-radius: var(--radius-pill, 999px);
}

.cost-items {
  display: flex;
  flex-direction: column;
  gap: 10px;
  font-size: 13px;
}

.cost-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}

.cost-name {
  color: var(--text-secondary, #647067);
}

.cost-value {
  font-weight: 700;
  font-variant-numeric: tabular-nums;
  color: var(--text, #1c2a23);
}

.cost-divider {
  height: 1px;
  background: var(--border, #dfe4dd);
  margin: 4px 0;
}

.cost-row.subtotal {
  font-weight: 800;
  color: var(--text, #1c2a23);
}

.cost-row.discount {
  color: #166534;
  font-weight: 700;
}

.cost-row.discount span:last-child {
  font-variant-numeric: tabular-nums;
}

/* Total Box */
.total-box {
  background: var(--primary-light, #e8f0e6);
  border: 1px solid rgba(58, 100, 80, 0.2);
  border-radius: var(--radius, 16px);
  padding: 16px;
  margin: 20px 0 16px;
}

.total-label-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 6px;
}

.total-label-row span:first-child {
  font-size: 12px;
  font-weight: 800;
  text-transform: uppercase;
  color: var(--text-secondary, #647067);
}

.save-badge {
  font-size: 11px;
  font-weight: 800;
  color: #166534;
  background: #dcfce7;
  padding: 2px 8px;
  border-radius: var(--radius-pill, 999px);
}

.total-amount {
  font-size: 28px;
  font-weight: 900;
  color: var(--primary, #3a6450);
  font-variant-numeric: tabular-nums;
  line-height: 1.2;
}

.installment-info {
  margin-top: 12px;
  padding-top: 10px;
  border-top: 1px dashed rgba(58, 100, 80, 0.25);
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-size: 12px;
}

.install-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 6px;
}

.install-row span {
  color: var(--text-secondary, #647067);
}

.install-row strong {
  color: var(--primary, #3a6450);
  font-variant-numeric: tabular-nums;
}

/* CTAs */
.result-actions {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.btn-daftar-now {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 14px 20px;
  background: var(--primary, #3a6450);
  color: #ffffff;
  font-size: 15px;
  font-weight: 800;
  border-radius: var(--radius, 16px);
  text-decoration: none;
  box-shadow: 0 4px 16px rgba(58, 100, 80, 0.25);
  transition: all 0.2s ease;
}

.btn-daftar-now:hover {
  background: var(--primary-dark, #2a5238);
  transform: translateY(-2px);
}

.btn-wa-consult {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 18px;
  background: rgba(37, 211, 102, 0.12);
  color: #128c7e;
  border: 1px solid rgba(37, 211, 102, 0.3);
  font-size: 14px;
  font-weight: 800;
  border-radius: var(--radius, 16px);
  text-decoration: none;
  transition: all 0.2s ease;
}

.btn-wa-consult:hover {
  background: rgba(37, 211, 102, 0.2);
  transform: translateY(-1px);
}

.result-note {
  font-size: 11px;
  color: var(--text-muted, #96a098);
  margin: 12px 0 0;
  line-height: 1.4;
  text-align: center;
}

/* --- TIMELINE SECTION --- */
.timeline-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
}

.timeline-card {
  display: flex;
  flex-direction: column;
  padding: 20px;
  background: var(--surface-soft, #fbfcfa);
  border: 1px solid var(--border, #dfe4dd);
  border-radius: var(--radius, 16px);
  transition: all 0.2s ease;
}

.timeline-card:hover {
  border-color: var(--primary, #3a6450);
  transform: translateY(-2px);
}

.timeline-step-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}

.step-badge {
  font-size: 14px;
  font-weight: 900;
  color: var(--primary, #3a6450);
  background: var(--primary-light, #e8f0e6);
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}

.step-tag {
  font-size: 11px;
  font-weight: 800;
  color: var(--text-secondary, #647067);
  background: var(--background, #f2f4f1);
  padding: 2px 8px;
  border-radius: var(--radius-pill, 999px);
}

.timeline-card h3 {
  font-size: 15px;
  font-weight: 800;
  color: var(--text, #1c2a23);
  margin: 0 0 8px;
  line-height: 1.3;
}

.timeline-card p {
  font-size: 13px;
  color: var(--text-secondary, #647067);
  line-height: 1.5;
  margin: 0 0 16px;
  flex-grow: 1;
}

.step-footer {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 700;
  color: var(--primary, #3a6450);
  padding-top: 10px;
  border-top: 1px solid var(--border, #dfe4dd);
}

/* --- REQUIREMENTS SECTION --- */
.requirements-grid {
  display: grid;
  grid-template-columns: 1fr 1.2fr;
  gap: 24px;
}

.req-box {
  background: var(--surface-soft, #fbfcfa);
  border: 1px solid var(--border, #dfe4dd);
  border-radius: var(--radius-lg, 20px);
  padding: 24px;
}

.req-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 20px;
  padding-bottom: 12px;
  border-bottom: 1px solid var(--border, #dfe4dd);
}

.req-icon {
  color: var(--primary, #3a6450);
}

.req-header h3 {
  font-size: 18px;
  font-weight: 800;
  margin: 0;
  color: var(--text, #1c2a23);
}

.req-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.req-list li {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  font-size: 14px;
  line-height: 1.5;
  color: var(--text, #1c2a23);
}

.check-icon {
  color: var(--primary, #3a6450);
  flex-shrink: 0;
  margin-top: 2px;
}

.doc-table {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.doc-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 12px 16px;
  background: #ffffff;
  border: 1px solid var(--border, #dfe4dd);
  border-radius: var(--radius, 16px);
}

.doc-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.doc-info strong {
  font-size: 13px;
  color: var(--text, #1c2a23);
}

.doc-info span {
  font-size: 11px;
  color: var(--text-secondary, #647067);
}

.doc-badge {
  font-size: 11px;
  font-weight: 800;
  padding: 4px 8px;
  background: var(--background, #f2f4f1);
  color: var(--text-secondary, #647067);
  border-radius: var(--radius-pill, 999px);
  white-space: nowrap;
}

.doc-badge.required {
  background: #fee2e2;
  color: #b91c1c;
}

/* --- MAJORS SHOWCASE --- */
.majors-showcase {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}

.major-card {
  display: flex;
  flex-direction: column;
  padding: 24px;
  background: var(--surface-soft, #fbfcfa);
  border: 1px solid var(--border, #dfe4dd);
  border-radius: var(--radius-lg, 20px);
  transition: all 0.2s ease;
}

.major-card:hover {
  border-color: var(--primary, #3a6450);
  transform: translateY(-3px);
  box-shadow: var(--shadow, 0 12px 28px rgba(35, 55, 42, 0.07));
}

.major-head {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 16px;
}

.m-icon {
  color: var(--primary, #3a6450);
  flex-shrink: 0;
}

.m-tag {
  font-size: 10px;
  font-weight: 800;
  color: var(--primary, #3a6450);
  background: rgba(58, 100, 80, 0.1);
  padding: 2px 8px;
  border-radius: var(--radius-pill, 999px);
  display: inline-block;
  margin-bottom: 4px;
}

.major-head h3 {
  font-size: 16px;
  font-weight: 800;
  margin: 0;
  color: var(--text, #1c2a23);
  line-height: 1.3;
}

.m-desc {
  font-size: 13px;
  color: var(--text-secondary, #647067);
  line-height: 1.5;
  margin: 0 0 20px;
  flex-grow: 1;
}

.m-details {
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding-top: 14px;
  border-top: 1px solid var(--border, #dfe4dd);
  font-size: 12px;
}

.m-stat {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.m-stat span {
  color: var(--text-secondary, #647067);
}

.m-stat strong {
  color: var(--text, #1c2a23);
}

/* --- DOWNLOAD SECTION --- */
.download-section {
  background: linear-gradient(135deg, var(--primary, #3a6450) 0%, var(--primary-dark, #2a5238) 100%);
  color: #ffffff;
  border: none;
}

.download-banner {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 32px;
  align-items: center;
}

.download-content h2 {
  color: #ffffff;
  font-size: 26px;
  font-weight: 800;
  margin: 8px 0 12px;
}

.download-content p {
  color: rgba(255, 255, 255, 0.85);
  font-size: 15px;
  line-height: 1.6;
  max-width: 600px;
  margin: 0 0 24px;
}

.download-buttons {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

.btn-download-primary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  background: #ffffff;
  color: var(--primary, #3a6450);
  font-weight: 800;
  font-size: 14px;
  border-radius: var(--radius, 16px);
  text-decoration: none;
  transition: all 0.2s ease;
}

.btn-download-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

.btn-download-outline {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  background: rgba(255, 255, 255, 0.15);
  color: #ffffff;
  border: 1px solid rgba(255, 255, 255, 0.3);
  font-weight: 700;
  font-size: 14px;
  border-radius: var(--radius, 16px);
  text-decoration: none;
  transition: all 0.2s ease;
}

.btn-download-outline:hover {
  background: rgba(255, 255, 255, 0.25);
}

/* --- FAQ SECTION --- */
.faq-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.faq-card {
  border: 1px solid var(--border, #dfe4dd);
  border-radius: var(--radius, 16px);
  background: var(--surface-soft, #fbfcfa);
  overflow: hidden;
  transition: all 0.2s ease;
}

.faq-card.open {
  border-color: var(--primary, #3a6450);
  background: #ffffff;
}

.faq-trigger {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 18px 20px;
  background: none;
  border: none;
  text-align: left;
  font-size: 15px;
  font-weight: 800;
  color: var(--text, #1c2a23);
  cursor: pointer;
}

.faq-chevron {
  color: var(--text-secondary, #647067);
  transition: transform 0.2s ease;
}

.faq-card.open .faq-chevron {
  transform: rotate(180deg);
  color: var(--primary, #3a6450);
}

.faq-answer {
  padding: 0 20px 18px;
  font-size: 14px;
  line-height: 1.6;
  color: var(--text-secondary, #647067);
}

.faq-answer p {
  margin: 0;
}

/* --- BOTTOM CTA --- */
.bottom-cta {
  background: var(--surface, #ffffff);
  border: 2px solid var(--primary, #3a6450);
  border-radius: var(--radius-xl, 24px);
  padding: 48px 40px;
  text-align: center;
  box-shadow: var(--shadow-lg, 0 20px 40px rgba(35, 55, 42, 0.12));
}

.cta-inner h2 {
  font-size: 30px;
  font-weight: 800;
  color: var(--text, #1c2a23);
  margin: 0 0 10px;
  letter-spacing: -0.02em;
}

.cta-inner p {
  font-size: 16px;
  color: var(--text-secondary, #647067);
  max-width: 550px;
  margin: 0 auto 28px;
}

.cta-actions {
  display: flex;
  justify-content: center;
  gap: 14px;
  flex-wrap: wrap;
}

.btn-cta-main {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 16px 28px;
  background: var(--primary, #3a6450);
  color: #ffffff;
  font-size: 16px;
  font-weight: 800;
  border-radius: var(--radius, 16px);
  text-decoration: none;
  box-shadow: 0 6px 20px rgba(58, 100, 80, 0.3);
  transition: all 0.2s ease;
}

.btn-cta-main:hover {
  background: var(--primary-dark, #2a5238);
  transform: translateY(-2px);
}

.btn-cta-secondary {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 16px 24px;
  background: var(--surface-soft, #fbfcfa);
  color: var(--primary, #3a6450);
  border: 1px solid var(--border-strong, #d4dbd8);
  font-size: 15px;
  font-weight: 800;
  border-radius: var(--radius, 16px);
  text-decoration: none;
  transition: all 0.2s ease;
}

.btn-cta-secondary:hover {
  background: rgba(58, 100, 80, 0.08);
  transform: translateY(-1px);
}

/* --- RESPONSIVE MEDIA QUERIES --- */
@media (max-width: 1024px) {
  .calc-grid {
    grid-template-columns: 1fr;
  }
  .calc-result-panel {
    position: static;
  }
  .majors-showcase {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .spmb-info-page {
    padding: 70px 4% 80px;
  }
  .page-header {
    grid-template-columns: 1fr;
    padding: 24px 20px;
  }
  .header-stats {
    flex-wrap: wrap;
  }
  .stat-pill {
    flex: 1 1 calc(50% - 12px);
    min-width: 0;
  }
  .content-section {
    padding: 24px 18px;
  }
  .major-options,
  .wave-options,
  .payment-options {
    grid-template-columns: 1fr;
  }
  .requirements-grid {
    grid-template-columns: 1fr;
  }
  .download-banner {
    grid-template-columns: 1fr;
  }
  .download-decor {
    display: none;
  }
  .cta-actions {
    flex-direction: column;
  }
  .btn-cta-main,
  .btn-cta-secondary {
    width: 100%;
    justify-content: center;
  }
}
</style>
