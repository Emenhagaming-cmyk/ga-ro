<template>
  <section class="el">
    <aside class="el-sidebar">
      <div class="sidebar-brand">
        <img src="/logo.webp" alt="Logo Sekolah" />
        <span class="brand-text">SMK Bahrul Ulum</span>
      </div>
      <span class="sidebar-label">MENU</span>
      <nav class="sidebar-nav">
        <button v-for="item in navItems" :key="item.id" :class="['nav-item', { active: activeNav === item.id }]" @click="activeNav = item.id">
          <component :is="item.icon" :size="18" :stroke-width="activeNav === item.id ? 2.5 : 2" />
          <span>{{ item.label }}</span>
          <span v-if="item.badge" class="nav-badge">{{ item.badge }}</span>
        </button>
      </nav>
      <div class="sidebar-promo">
        <div class="promo-icon">📚</div>
        <p class="promo-title">Tips Belajar nih buat kamu!</p>
        <p class="promo-desc">Selesaikan 1 materi setiap hari untuk membangun kebiasaan belajar yang konsisten :].</p>
      </div>
    </aside>

    <main class="el-main">
      <div class="main-topbar">
        <div class="topbar-left">
          <button class="back-btn" @click="goBack"><ChevronLeft :size="18" :stroke-width="2" /></button>
          <h1 class="topbar-title">Dashboard</h1>
        </div>
        <div class="topbar-right">
          <div class="search-box">
            <Search :size="16" :stroke-width="2" />
            <input type="text" placeholder="Cari materi..." v-model="searchQuery" />
          </div>
          <button class="topbar-icon-btn"><Bell :size="18" :stroke-width="2" /></button>
        </div>
      </div>

      <div class="stats-row">
        <div class="stat-card orange"><div class="stat-icon-wrap"><BookOpen :size="20" :stroke-width="2" /></div><div class="stat-info"><span class="stat-num">{{ materials.length }}</span><span class="stat-label">Total Materi</span></div></div>
        <div class="stat-card blue"><div class="stat-icon-wrap"><FileQuestion :size="20" :stroke-width="2" /></div><div class="stat-info"><span class="stat-num">{{ quizzes.length }}</span><span class="stat-label">Kuis Aktif</span></div></div>
        <div class="stat-card green"><div class="stat-icon-wrap"><Video :size="20" :stroke-width="2" /></div><div class="stat-info"><span class="stat-num">{{ videoCount }}</span><span class="stat-label">Video</span></div></div>
        <div class="stat-card purple"><div class="stat-icon-wrap"><FileText :size="20" :stroke-width="2" /></div><div class="stat-info"><span class="stat-num">{{ pdfCount }}</span><span class="stat-label">Modul PDF</span></div></div>
      </div>

      <div class="section-block" v-if="activeNav === 'overview' || activeNav === 'materi'">
        <div class="section-header"><h2>Lanjutkan Belajarnya dong :)</h2><span class="section-sub">Materi yang baru saja kamu akses</span></div>
        <div class="continue-grid">
          <div v-for="item in featuredMaterials" :key="item.id" class="continue-card">
            <div class="cc-visual" :style="{ background: item.bg }"><span class="cc-icon">{{ item.icon }}</span></div>
            <div class="cc-body">
              <span class="cc-subject">{{ item.subject }}</span>
              <h3 class="cc-title">{{ item.title }}</h3>
              <div class="cc-progress">
                <div class="progress-bar"><div class="progress-fill" :style="{ width: item.progress + '%' }"></div></div>
                <span class="progress-text">{{ item.progress }}%</span>
              </div>
              <div class="cc-meta"><span><Clock :size="12" /> {{ item.duration }}</span><span><BarChart3 :size="12" /> {{ item.level }}</span></div>
            </div>
          </div>
        </div>
      </div>

      <div class="section-block" v-if="activeNav === 'overview' || activeNav === 'materi'">
        <div class="section-header">
          <h2>Materi Pembelajaran</h2>
          <div class="cat-tabs">
            <button v-for="cat in categories" :key="cat" :class="['cat-tab', { active: activeCategory === cat }]" @click="activeCategory = cat">{{ cat }}</button>
          </div>
        </div>
        <div class="material-grid">
          <article v-for="item in filteredMaterials" :key="item.id" class="mat-card">
            <div class="mat-thumb" :style="{ background: item.bg }">
              <span class="mat-icon">{{ item.icon }}</span>
              <span class="mat-type">{{ item.type }}</span>
            </div>
            <div class="mat-info">
              <span class="mat-subject">{{ item.subject }}</span>
              <h3>{{ item.title }}</h3>
              <p>{{ item.desc }}</p>
              <div class="mat-actions">
                <a v-if="item.videoUrl" :href="item.videoUrl" target="_blank" rel="noopener" class="mat-btn video"><Play :size="14" /> Video</a>
                <a v-if="item.pdfUrl" :href="item.pdfUrl" target="_blank" rel="noopener" class="mat-btn pdf"><FileDown :size="14" /> PDF</a>
              </div>
              <div class="mat-meta"><span>{{ item.duration }}</span><span>{{ item.level }}</span></div>
            </div>
          </article>
        </div>
        <div v-if="filteredMaterials.length === 0" class="empty-state"><Package :size="40" :stroke-width="1.5" /><p>Ups maaf ya belum ada materi di kategori ini.</p></div>
      </div>

      <div class="section-block" v-if="activeNav === 'overview' || activeNav === 'kuis'">
        <div class="section-header"><h2>Kuis Interaktif biar makin aktif!</h2><span class="section-sub">Uji pemahamanmu dengan latihan soal</span></div>
        <div class="quiz-grid">
          <article v-for="quiz in quizzes" :key="quiz.id" class="quiz-card">
            <div class="quiz-icon-wrap"><span class="quiz-icon">{{ quiz.icon }}</span></div>
            <div class="quiz-info">
              <h3>{{ quiz.title }}</h3>
              <p>{{ quiz.desc }}</p>
              <div class="quiz-meta"><span><FileQuestion :size="12" /> {{ quiz.questions }} soal</span><span><Clock :size="12" /> {{ quiz.time }}</span></div>
            </div>
            <a :href="quiz.url" target="_blank" rel="noopener" class="quiz-btn">Mulai <ArrowRight :size="14" /></a>
          </article>
        </div>
      </div>

      <div class="section-block" v-if="activeNav === 'pencapaian'">
        <div class="section-header"><h2>Pencapaianmu</h2><span class="section-sub">Statistik dan progres belajarmu</span></div>
        <div class="achieve-grid">
          <div class="achieve-card"><div class="achieve-num orange">{{ materials.length }}</div><div class="achieve-label">Materi Tersedia</div></div>
          <div class="achieve-card"><div class="achieve-num blue">{{ quizzes.length }}</div><div class="achieve-label">Kuis Aktif</div></div>
          <div class="achieve-card"><div class="achieve-num green">{{ videoCount }}</div><div class="achieve-label">Video Pembelajaran</div></div>
          <div class="achieve-card"><div class="achieve-num purple">{{ categories.length - 1 }}</div><div class="achieve-label">Kategori</div></div>
        </div>
        <div class="info-banner">
          <Lightbulb :size="20" :stroke-width="2" class="info-icon" />
          <div class="info-text"><strong>Cara Belajar di E-Learning:</strong> Pilih materi, tonton video atau download modul PDF, lalu kerjakan kuis untuk menguji pemahaman. </div>
        </div>
      </div>
    </main>

    <aside class="el-panel">
      <div class="panel-profile">
        <div class="profile-avatar">{{ user?.name?.charAt(0) || '?' }}</div>
        <div class="profile-info"><strong>{{ user?.name || 'Guest' }}</strong><span>{{ user?.email || 'Belum login' }}</span></div>
      </div>
      <div class="panel-stats">
        <div class="ps-item"><div class="ps-num orange">{{ materials.length }}</div><div class="ps-label">Materi</div></div>
        <div class="ps-item"><div class="ps-num blue">{{ quizzes.length }}</div><div class="ps-label">Kuis</div></div>
        <div class="ps-item"><div class="ps-num green">{{ videoCount }}</div><div class="ps-label">Video</div></div>
      </div>
      <div class="panel-section">
        <h4>Aktivitas Kamu Minggu Ini :D</h4>
        <div class="week-grid">
          <div v-for="(day, i) in weekDays" :key="i" :class="['week-day', { active: day.active, today: day.today }]">
            <span class="wd-label">{{ day.label }}</span><span class="wd-num">{{ day.date }}</span>
          </div>
        </div>
      </div>
      <div class="panel-section">
        <h4>Progress per Kategori</h4>
        <div v-for="cat in categoryStats" :key="cat.name" class="cat-progress">
          <div class="cp-top"><span>{{ cat.name }}</span><span>{{ cat.count }} materi</span></div>
          <div class="cp-bar"><div class="cp-fill" :style="{ width: cat.pct + '%', background: cat.color }"></div></div>
        </div>
      </div>
    </aside>
  </section>
</template>

<script setup>
import { ref, computed } from "vue";
import { useAuthSession } from "@/composable/useAuthSession";
import { ChevronLeft, Search, Bell, BookOpen, FileQuestion, Video, FileText, Clock, BarChart3, Play, FileDown, Package, ArrowRight, Lightbulb, Trophy, BookMarked } from "lucide-vue-next";

const { session } = useAuthSession();
const user = computed(() => session.value);

const activeNav = ref("overview");
const navItems = [
  { id: "overview", label: "Overview", icon: BookOpen, badge: null },
  { id: "materi", label: "Materi", icon: BookMarked, badge: null },
  { id: "kuis", label: "Kuis", icon: FileQuestion, badge: "3" },
  { id: "pencapaian", label: "Pencapaian", icon: Trophy, badge: null },
];

const searchQuery = ref("");
const activeCategory = ref("Semua");
const categories = ["Semua", "Pemrograman", "Jaringan", "Basis Data", "Multimedia"];

const materials = [
  { id: 1, title: "Pengenalan HTML & CSS", desc: "Belajar dasar pembuatan halaman web dengan HTML5 dan CSS3.", subject: "Pemrograman Dasar", type: "Video + Modul", icon: "🌐", bg: "#fff3e0", videoUrl: "https://youtu.be/60K7zxIjHQo", pdfUrl: "/materi/ModulDasarHTML.pdf", duration: "45 menit", level: "Pemula", category: "Pemrograman", progress: 75 },
  { id: 2, title: "Dasar MySQL & Query", desc: "Mengenal sistem basis data relasional, membuat tabel, dan query dasar SQL.", subject: "Basis Data", type: "Video + Modul", icon: "🗄️", bg: "#e3f2fd", videoUrl: "https://youtu.be/tDO0g3pbp5U", pdfUrl: "/materi/ModulDasarMYSQL.pdf", duration: "50 menit", level: "Pemula", category: "Basis Data", progress: 40 },
  { id: 3, title: "Dasar Pemrograman JavaScript", desc: "Variabel, tipe data, operator, percabangan, perulangan, dan fungsi.", subject: "Pemrograman Dasar", type: "Video", icon: "⚡", bg: "#fff8e1", videoUrl: "https://youtu.be/W6NZfL5JYT0", pdfUrl: null, duration: "60 menit", level: "Pemula", category: "Pemrograman", progress: 20 },
  { id: 4, title: "Pengenalan Jaringan Komputer", desc: "Konsep dasar jaringan, tipe jaringan, topologi, dan TCP/IP.", subject: "Jaringan Komputer", type: "Video + Modul", icon: "🔗", bg: "#f3e5f5", videoUrl: "https://youtu.be/EtD-2_Ks1IY", pdfUrl: null, duration: "40 menit", level: "Pemula", category: "Jaringan", progress: 60 },
  { id: 5, title: "PHP untuk Pemula", desc: "Belajar PHP: variabel, array, fungsi, form handling, dan koneksi MySQL.", subject: "Pemrograman Dasar", type: "Video + Modul", icon: "🐘", bg: "#fce4ec", videoUrl: "https://youtu.be/ZCkR8JfieJo", pdfUrl: null, duration: "55 menit", level: "Pemula", category: "Pemrograman", progress: 10 },
  { id: 6, title: "Relasi Tabel & Normalisasi", desc: "Memahami relasi antar tabel, primary key, foreign key, dan normalisasi 3NF.", subject: "Basis Data", type: "Modul", icon: "📊", bg: "#e8f5e9", videoUrl: null, pdfUrl: null, duration: "30 menit", level: "Menengah", category: "Basis Data", progress: 0 },
];

const quizzes = [
  { id: 1, title: "Kuis HTML & CSS Dasar", desc: "10 soal pilihan ganda tentang elemen HTML, selector CSS, dan box model.", icon: "🌐", questions: 10, time: "15 menit", url: "https://forms.gle/ZgYuXDFnys6hkACW7" },
  { id: 2, title: "Kuis MySQL Dasar", desc: "10 soal tentang perintah SQL: SELECT, INSERT, UPDATE, DELETE.", icon: "🗄️", questions: 10, time: "20 menit", url: "https://forms.gle/ZgYuXDFnys6hkACW7" },
  { id: 3, title: "Kuis Jaringan Komputer", desc: "10 soal tentang topologi jaringan, protokol, dan perangkat jaringan.", icon: "🔗", questions: 10, time: "15 menit", url: "https://forms.gle/ZgYuXDFnys6hkACW7" },
];

const videoCount = computed(() => materials.filter(m => m.videoUrl).length);
const pdfCount = computed(() => materials.filter(m => m.pdfUrl).length);

const filteredMaterials = computed(() => {
  let result = activeCategory.value === "Semua" ? materials : materials.filter(m => m.category === activeCategory.value);
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    result = result.filter(m => m.title.toLowerCase().includes(q) || m.subject.toLowerCase().includes(q));
  }
  return result;
});

const featuredMaterials = computed(() => materials.filter(m => m.progress > 0).slice(0, 4));

const weekDays = [
  { label: "Min", date: 17, active: true, today: false },
  { label: "Sen", date: 18, active: true, today: false },
  { label: "Sel", date: 19, active: true, today: false },
  { label: "Rab", date: 20, active: true, today: false },
  { label: "Kam", date: 21, active: false, today: true },
  { label: "Jum", date: 22, active: false, today: false },
  { label: "Sab", date: 23, active: false, today: false },
];

const categoryStats = computed(() => {
  const colors = { Pemrograman: "#3a6450", "Basis Data": "#3b82f6", Jaringan: "#6b7a5e", Multimedia: "#2a8a6a" };
  return categories.filter(c => c !== "Semua").map(c => {
    const count = materials.filter(m => m.category === c).length;
    return { name: c, count, pct: Math.round((count / materials.length) * 100), color: colors[c] || "#999" };
  });
});

function goBack() { window.history.back(); }
</script><style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
</style>

<style scoped>
.el{--primary:#3a6450;--primary-light:#e8f0e6;--accent:#7db88d;--accent-pale:#c7d9c3;--blue-500:#3b82f6;--blue-100:#dbeafe;--teal-500:#2a8a6a;--teal-100:#d4f0e7;--purple-500:#6b7a5e;--purple-100:#e8ede4;--surface:#f2f4f1;--white:#fff;--text-1:#1c2a23;--text-2:#647067;--text-3:#96a098;--border:#dfe4dd;--radius:16px;--radius-sm:10px;display:grid;grid-template-columns:240px 1fr 280px;min-height:100vh;background:var(--surface);font-family:'Plus Jakarta Sans',system-ui,sans-serif;color:var(--text-1);-webkit-font-smoothing:antialiased}
.el-sidebar{background:var(--white);border-right:1px solid var(--border);padding:24px 16px;display:flex;flex-direction:column;position:sticky;top:0;height:100vh;overflow-y:auto}
.sidebar-brand{display:flex;align-items:center;gap:10px;margin-bottom:28px;padding:0 8px}
.sidebar-brand img{height:38px;border-radius:10px;background:rgba(255,255,255,0.9);padding:4px}
.brand-text{font-size:14px;font-weight:800;color:var(--text-1)}
.sidebar-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-3);padding:0 8px;margin-bottom:8px}
.sidebar-nav{display:flex;flex-direction:column;gap:4px;margin-bottom:auto}
.nav-item{display:flex;align-items:center;gap:10px;padding:10px 12px;border:none;border-radius:var(--radius-sm);background:0 0;color:var(--text-2);font-family:inherit;font-size:13px;font-weight:600;cursor:pointer;transition:all .15s;text-align:left}
.nav-item:hover{background:#f5f5f5;color:var(--text-1)}
.nav-item.active{background:var(--primary-light);color:var(--primary)}
.nav-badge{margin-left:auto;min-width:20px;height:20px;padding:0 6px;border-radius:10px;background:#96a098;color:#fff;font-size:10px;font-weight:700;display:flex;align-items:center;justify-content:center}
.sidebar-promo{margin-top:20px;padding:20px 16px;border-radius:var(--radius);background:linear-gradient(135deg,#e8f0e6,#f2f4f1);border:1px solid rgba(58,100,80,.1)}
.promo-icon{font-size:32px;margin-bottom:8px}
.promo-title{margin:0 0 4px;font-size:14px;font-weight:700;color:var(--text-1)}
.promo-desc{margin:0;font-size:12px;color:var(--text-2);line-height:1.5}
.el-main{padding:24px 32px;overflow-y:auto}
.main-topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:28px}
.topbar-left{display:flex;align-items:center;gap:12px}
.back-btn{display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border:1px solid var(--border);background:var(--white);border-radius:var(--radius-sm);color:var(--text-2);cursor:pointer}
.back-btn:hover{background:#f5f5f5}
.topbar-title{font-size:20px;font-weight:800;margin:0}
.topbar-right{display:flex;align-items:center;gap:10px}
.search-box{display:flex;align-items:center;gap:8px;padding:8px 14px;background:var(--white);border:1px solid var(--border);border-radius:var(--radius-sm);color:var(--text-3)}
.search-box input{border:none;background:0 0;outline:none;font-family:inherit;font-size:13px;color:var(--text-1);width:160px}
.search-box input::placeholder{color:var(--text-3)}
.topbar-icon-btn{display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border:1px solid var(--border);background:var(--white);border-radius:var(--radius-sm);color:var(--text-2);cursor:pointer}
.topbar-icon-btn:hover{background:#f5f5f5}
.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:28px}
.stat-card{display:flex;align-items:center;gap:14px;padding:18px 16px;background:var(--white);border-radius:var(--radius);border:1px solid var(--border)}
.stat-icon-wrap{width:44px;height:44px;border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.stat-card.orange .stat-icon-wrap{background:var(--primary-light);color:var(--primary)}
.stat-card.blue .stat-icon-wrap{background:var(--blue-100);color:var(--blue-500)}
.stat-card.green .stat-icon-wrap{background:var(--teal-100);color:var(--teal-500)}
.stat-card.purple .stat-icon-wrap{background:var(--purple-100);color:var(--purple-500)}
.stat-num{display:block;font-size:22px;font-weight:800;line-height:1}
.stat-label{font-size:12px;color:var(--text-3)}
.section-block{margin-bottom:32px}
.section-header{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:20px}
.section-header h2{margin:0;font-size:18px;font-weight:800}
.section-sub{font-size:13px;color:var(--text-3)}
.continue-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-bottom:32px}
.continue-card{display:flex;background:var(--white);border-radius:var(--radius);border:1px solid var(--border);overflow:hidden;transition:transform .2s,box-shadow .2s}
.continue-card:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.06)}
.cc-visual{width:100px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.cc-icon{font-size:36px}
.cc-body{padding:16px;flex:1;min-width:0}
.cc-subject{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--primary)}
.cc-title{margin:4px 0 10px;font-size:14px;font-weight:700;color:var(--text-1);line-height:1.3}
.cc-progress{display:flex;align-items:center;gap:10px;margin-bottom:10px}
.progress-bar{flex:1;height:6px;background:#f0f0f0;border-radius:3px;overflow:hidden}
.progress-fill{height:100%;background:var(--primary);border-radius:3px;transition:width .3s}
.progress-text{font-size:12px;font-weight:700;color:var(--primary);min-width:32px}
.cc-meta{display:flex;gap:16px;font-size:11px;color:var(--text-3)}
.cc-meta span{display:inline-flex;align-items:center;gap:4px}
.cat-tabs{display:flex;gap:6px;flex-wrap:wrap}
.cat-tab{padding:6px 14px;border:1.5px solid var(--border);border-radius:20px;background:var(--white);color:var(--text-2);font-family:inherit;font-size:12px;font-weight:600;cursor:pointer;transition:all .15s}
.cat-tab:hover{border-color:var(--primary);color:var(--primary)}
.cat-tab.active{background:var(--primary);border-color:var(--primary);color:#fff}
.material-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px}
.mat-card{border-radius:var(--radius);overflow:hidden;background:var(--white);border:1px solid var(--border);transition:transform .2s,box-shadow .2s}
.mat-card:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,.06)}
.mat-thumb{position:relative;aspect-ratio:16/9;display:grid;place-items:center}
.mat-icon{font-size:44px}
.mat-type{position:absolute;top:10px;right:10px;padding:3px 8px;border-radius:8px;background:rgba(0,0,0,.5);color:#fff;font-size:10px;font-weight:700;backdrop-filter:blur(4px)}
.mat-info{padding:16px}
.mat-subject{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--primary)}
.mat-info h3{margin:6px 0 4px;font-size:14px;font-weight:700;color:var(--text-1);line-height:1.3}
.mat-info p{margin:0;font-size:12px;color:var(--text-3);line-height:1.5}
.mat-actions{display:flex;gap:6px;margin-top:12px}
.mat-btn{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:8px;font-size:11px;font-weight:700;text-decoration:none;transition:all .15s}
.mat-btn.video{background:rgba(239,68,68,.1);color:#96a098}
.mat-btn.video:hover{background:#96a098;color:#fff}
.mat-btn.pdf{background:rgba(59,130,246,.1);color:#3b82f6}
.mat-btn.pdf:hover{background:#3b82f6;color:#fff}
.mat-meta{display:flex;justify-content:space-between;margin-top:12px;padding-top:10px;border-top:1px solid var(--border);font-size:11px;color:var(--text-3)}
.empty-state{text-align:center;padding:48px;color:var(--text-3);display:flex;flex-direction:column;align-items:center;gap:12px}
.quiz-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px}
.quiz-card{display:flex;align-items:center;gap:16px;padding:20px;background:var(--white);border-radius:var(--radius);border:1px solid var(--border);transition:transform .2s,box-shadow .2s}
.quiz-card:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.06)}
.quiz-icon-wrap{width:52px;height:52px;border-radius:var(--radius-sm);background:var(--primary-light);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.quiz-icon{font-size:28px}
.quiz-info{flex:1;min-width:0}
.quiz-info h3{margin:0 0 4px;font-size:14px;font-weight:700;color:var(--text-1)}
.quiz-info p{margin:0;font-size:12px;color:var(--text-3);line-height:1.4}
.quiz-meta{display:flex;gap:14px;margin-top:8px;font-size:11px;color:var(--text-3)}
.quiz-meta span{display:inline-flex;align-items:center;gap:4px}
.quiz-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:var(--radius-sm);background:var(--primary);color:#fff;font-size:12px;font-weight:700;text-decoration:none;transition:all .15s;flex-shrink:0}
.quiz-btn:hover{background:#ea580c;transform:translateY(-1px)}
.achieve-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px}
.achieve-card{text-align:center;padding:24px 16px;background:var(--white);border-radius:var(--radius);border:1px solid var(--border)}
.achieve-num{font-size:28px;font-weight:800;margin-bottom:6px}
.achieve-num.orange{color:var(--primary)}
.achieve-num.blue{color:var(--blue-500)}
.achieve-num.green{color:var(--teal-500)}
.achieve-num.purple{color:var(--purple-500)}
.achieve-label{font-size:12px;color:var(--text-3);font-weight:600}
.info-banner{display:flex;align-items:flex-start;gap:12px;padding:16px 20px;border-radius:var(--radius);background:linear-gradient(135deg,#e8f0e6,#f2f4f1);border:1px solid rgba(58,100,80,.1)}
.info-icon{color:var(--primary);flex-shrink:0;margin-top:2px}
.info-text{font-size:13px;color:var(--text-2);line-height:1.6}
.info-text strong{color:var(--text-1)}
.el-panel{background:var(--white);border-left:1px solid var(--border);padding:24px 16px;display:flex;flex-direction:column;position:sticky;top:0;height:100vh;overflow-y:auto}
.panel-profile{display:flex;align-items:center;gap:12px;padding-bottom:20px;border-bottom:1px solid var(--border);margin-bottom:20px}
.profile-avatar{width:44px;height:44px;border-radius:50%;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;flex-shrink:0}
.profile-info strong{display:block;font-size:13px;color:var(--text-1);margin-bottom:2px}
.profile-info span{font-size:11px;color:var(--text-3)}
.panel-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;padding-bottom:20px;border-bottom:1px solid var(--border);margin-bottom:20px}
.ps-item{text-align:center;padding:12px 4px;border-radius:var(--radius-sm);background:var(--surface)}
.ps-num{font-size:20px;font-weight:800;line-height:1}
.ps-num.orange{color:var(--primary)}
.ps-num.blue{color:var(--blue-500)}
.ps-num.green{color:var(--teal-500)}
.ps-label{font-size:10px;color:var(--text-3);margin-top:4px}
.panel-section{margin-bottom:20px}
.panel-section h4{margin:0 0 12px;font-size:12px;font-weight:700;color:var(--text-2);text-transform:uppercase;letter-spacing:.06em}
.week-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:4px}
.week-day{display:flex;flex-direction:column;align-items:center;gap:2px;padding:6px 2px;border-radius:8px}
.week-day.active{background:var(--primary-light)}
.week-day.today{background:var(--primary);color:#fff;border-radius:8px}
.week-day.today .wd-label,.week-day.today .wd-num{color:#fff}
.wd-label{font-size:9px;font-weight:600;color:var(--text-3)}
.wd-num{font-size:14px;font-weight:700;color:var(--text-1)}
.achieve-list{display:flex;flex-direction:column;gap:10px}
.ach-item{display:flex;align-items:center;gap:12px;padding:12px;border-radius:var(--radius-sm);background:var(--surface);border:1px solid var(--border)}
.ach-icon{font-size:24px}
.ach-info{flex:1}
.ach-info strong{display:block;font-size:12px;color:var(--text-1)}
.ach-info span{font-size:11px;color:var(--text-3)}
.ach-date{font-size:10px;color:var(--text-3)}
@media(max-width:1200px){.el{grid-template-columns:200px 1fr}.el-panel{display:none}}
@media(max-width:768px){.el{grid-template-columns:1fr}.el-sidebar{display:none}.stats-row{grid-template-columns:repeat(2,1fr)}.continue-grid,.material-grid,.quiz-grid{grid-template-columns:1fr}.achieve-grid{grid-template-columns:repeat(2,1fr)}}
.cat-progress{margin-bottom:12px}
.cp-top{display:flex;justify-content:space-between;font-size:11px;font-weight:600;color:var(--text-2);margin-bottom:4px}
.cp-bar{height:6px;background:#f0f0f0;border-radius:3px;overflow:hidden}
.cp-fill{height:100%;border-radius:3px;transition:width .3s}
</style>
