<template>
  <div class="ds-root">
    <div v-if="loading" class="ds-loading" role="status">Memuat dashboard…</div>

    <div v-else-if="loadError" class="ds-empty" role="alert">
      <div class="ds-empty-icon">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#3a6450" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12.01" y2="8"/><line x1="12" y1="12" x2="12" y2="16"/></svg>
      </div>
      <h3>Gagal Memuat Data</h3>
      <p>{{ loadError }}</p>
      <button type="button" class="btn-ds btn-ds-primary" @click="reload">Coba Lagi</button>
    </div>

    <div v-else-if="data && !pendaftaran" class="ds-empty" role="status" aria-label="Belum ada data pendaftaran">
      <div class="ds-empty-icon">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#3a6450" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 0 0 0-2 2v16a2 0 0 0 2 2h12a2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
      </div>
      <h3>Belum Ada Pendaftaran</h3>
      <p>Kamu belum mengisi formulir pendaftaran. Yuk mulai sekarang!</p>
      <a :href="`${BACKEND}/pendaftaran/create`" class="btn-ds btn-ds-primary">Daftar Sekarang</a>
    </div>

    <template v-else-if="pendaftaran">
      <router-link to="/" class="ds-back-btn" aria-label="Kembali ke beranda">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
        Kembali
      </router-link>

      <div class="ds-banner">
        <div class="ds-banner-text">
          <p class="ds-banner-greeting">Selamat datang, {{ pendaftaran.nama_lengkap }}! 👋</p>
          <h1 class="ds-banner-title">Semangat mengejar prestasi!</h1>
          <p class="ds-banner-subtitle">Pantau status pendaftaran dan kelola formulir kamu di sini.</p>
        </div>
        <div class="ds-banner-doodle-wrap">
          <img :src="`${BACKEND}/images/doodle-studying.png`" alt="" class="ds-banner-doodle" aria-hidden="true" />
        </div>
      </div>

      <div v-if="!canEdit" class="ds-announce" :class="`ds-announce--${pendaftaran.status}`" role="status" aria-live="polite">
        <div class="ds-announce-icon" v-html="announceIcon"></div>
        <div class="ds-announce-body">
          <p class="ds-announce-eyebrow">{{ announceEyebrow }}</p>
          <p class="ds-announce-text">
            <template v-if="pendaftaran.status === 'baru'">Batas waktu edit telah berakhir. Hubungi admin bila ingin mengubah data.</template>
            <template v-else-if="pendaftaran.status === 'diterima'">Selamat kamu resmi jadi bagian dari SMK Bahrul Ulum dan Kamu diterima di jurusan <strong>{{ pendaftaran.jurusan_pilihan }} !</strong></template>
            <template v-else-if="pendaftaran.status === 'ditolak'">Maaf, pendaftaran Anda tidak diterima. Hubungi admin untuk info lebih lanjut.</template>
            <template v-else>Formulir kamu sedang diproses admin. Pantau status secara berkala.</template>
          </p>
          <a v-if="pendaftaran.status === 'diterima'" :href="`${BACKEND}/pendaftaran/bukti`" class="btn-ds btn-ds-primary ds-announce-cta">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 0 0 1-2 2H5a2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Unduh Bukti Diterima
          </a>
        </div>
      </div>

      <div class="ds-stats">
        <div class="ds-stat-card">
          <div class="ds-stat-icon" :style="{ background: badge.bg, color: badge.color }">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          </div>
          <div class="ds-stat-info">
            <span class="ds-stat-label">Status Pendaftaran</span>
            <span class="ds-stat-value" :style="{ color: badge.color }">{{ badge.text.toUpperCase() }}</span>
          </div>
        </div>
        <div class="ds-stat-card">
          <div class="ds-stat-icon" style="background:#eef5ff;color:#1d4ed8;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
          </div>
          <div class="ds-stat-info">
            <span class="ds-stat-label">Jurusan Pilihan</span>
            <span class="ds-stat-value">{{ pendaftaran.jurusan_pilihan }}</span>
          </div>
        </div>
        <div class="ds-stat-card">
          <div class="ds-stat-icon" style="background:#f0fdf4;color:#166534;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          </div>
          <div class="ds-stat-info">
            <span class="ds-stat-label">Tanggal Daftar</span>
            <span class="ds-stat-value">{{ tgl(pendaftaran.created_at) }}</span>
          </div>
        </div>
      </div>

      <div class="ds-card" id="status-section">
        <div class="ds-card-header">
          <h2 class="ds-card-title">Status Pendaftaran</h2>
          <span class="ds-badge" :style="{ background: badge.bg, color: badge.color }">{{ badge.text.toUpperCase() }}</span>
        </div>
        <div class="ds-detail-grid">
          <div class="ds-detail-item">
            <span class="ds-detail-label">Nama Lengkap</span>
            <span class="ds-detail-value">{{ pendaftaran.nama_lengkap }}</span>
          </div>
          <div class="ds-detail-item">
            <span class="ds-detail-label">NISN</span>
            <span class="ds-detail-value">{{ pendaftaran.nisn || "-" }}</span>
          </div>
          <div class="ds-detail-item">
            <span class="ds-detail-label">Asal Sekolah</span>
            <span class="ds-detail-value">{{ pendaftaran.asal_sekolah }}</span>
          </div>
          <div class="ds-detail-item">
            <span class="ds-detail-label">Jurusan Pilihan</span>
            <span class="ds-detail-value">{{ pendaftaran.jurusan_pilihan }}</span>
          </div>
          <div class="ds-detail-item">
            <span class="ds-detail-label">Tanggal Daftar</span>
            <span class="ds-detail-value">{{ tgl(pendaftaran.created_at) }}</span>
          </div>
          <div v-if="pendaftaran.status_updated_at" class="ds-detail-item">
            <span class="ds-detail-label">Status Diperbarui</span>
            <span class="ds-detail-value">{{ tglJam(pendaftaran.status_updated_at) }}</span>
          </div>
        </div>
      </div>

      <div v-if="canEdit" class="ds-actions">
        <button type="button" class="btn-ds btn-ds-outline" @click="scrollToEdit">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 4H4a2 0 0 0-2 2v14a2 0 0 0 2 2h14a2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          Edit Formulir
        </button>
      </div>

      <div v-if="canEdit" class="ds-card ds-card--edit" id="edit-section">
        <div class="ds-card-header">
          <h2 class="ds-card-title">Edit Formulir Pendaftaran</h2>
        </div>
        <p class="ds-deadline-text">
          Batas waktu edit: <strong class="ds-deadline-highlight">{{ deadlineText }}</strong> (sisa {{ hoursLeft }} jam).
        </p>
        <form novalidate @submit.prevent="submitEdit">
          <div class="ds-form-row">
            <div class="form-group">
              <label for="edit-nama-lengkap">Nama Lengkap</label>
              <input type="text" id="edit-nama-lengkap" v-model="form.nama_lengkap" required>
            </div>
            <div class="form-group">
              <label for="edit-nisn">NISN</label>
              <input type="text" id="edit-nisn" v-model="form.nisn">
            </div>
          </div>
          <div class="ds-form-row">
            <div class="form-group">
              <label for="edit-tempat-lahir">Tempat Lahir</label>
              <input type="text" id="edit-tempat-lahir" v-model="form.tempat_lahir" required>
            </div>
            <div class="form-group">
              <label for="edit-tanggal-lahir">Tanggal Lahir</label>
              <input type="date" id="edit-tanggal-lahir" v-model="form.tanggal_lahir" required>
            </div>
          </div>
          <div class="ds-form-row">
            <div class="form-group">
              <label for="edit-jenis-kelamin">Jenis Kelamin</label>
              <select id="edit-jenis-kelamin" v-model="form.jenis_kelamin" required>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
              </select>
            </div>
            <div class="form-group">
              <label for="edit-no-hp">No. HP / WhatsApp</label>
              <input type="tel" id="edit-no-hp" v-model="form.no_hp" required>
            </div>
          </div>
          <div class="form-group">
            <label for="edit-alamat">Alamat Lengkap</label>
            <textarea id="edit-alamat" v-model="form.alamat" required></textarea>
          </div>
          <hr class="section-divider" aria-hidden="true">
          <div class="form-group">
            <label for="edit-asal-sekolah">Asal Sekolah</label>
            <input type="text" id="edit-asal-sekolah" v-model="form.asal_sekolah" required>
          </div>
          <div class="form-group">
            <label for="edit-jurusan-pilihan">Jurusan Pilihan</label>
            <select id="edit-jurusan-pilihan" v-model="form.jurusan_pilihan" required>
              <option value="RPL">Rekayasa Perangkat Lunak (RPL)</option>
            </select>
          </div>
          <hr class="section-divider" aria-hidden="true">
          <div class="ds-form-row">
            <div class="form-group">
              <label for="edit-nama-orangtua">Nama Orang Tua</label>
              <input type="text" id="edit-nama-orangtua" v-model="form.nama_orang_tua" required>
            </div>
            <div class="form-group">
              <label for="edit-no-hp-orangtua">No. HP Orang Tua</label>
              <input type="tel" id="edit-no-hp-orangtua" v-model="form.no_hp_orang_tua">
            </div>
          </div>
          <div class="ds-form-actions">
            <button type="submit" class="btn-ds btn-ds-primary" :disabled="saving">
              {{ saving ? "Menyimpan…" : "Simpan Perubahan" }}
            </button>
          </div>
        </form>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from "vue";
import { fetchJson } from "@/services/fetchJson";
import { getCsrfToken } from "@/services/csrf";
import { BACKEND } from "@/composable/useAuthSession";
import { useToast } from "@/composable/useToast";

const { showToast } = useToast();

const loading = ref(true);
const loadError = ref("");
const data = ref(null); // { user, pendaftaran, can_edit, deadline }
const saving = ref(false);
const form = reactive({
  nama_lengkap: "",
  nisn: "",
  tempat_lahir: "",
  tanggal_lahir: "",
  jenis_kelamin: "Laki-laki",
  no_hp: "",
  alamat: "",
  asal_sekolah: "",
  jurusan_pilihan: "RPL",
  nama_orang_tua: "",
  no_hp_orang_tua: "",
});

const pendaftaran = computed(() => data.value?.pendaftaran || null);
const canEdit = computed(() => !!data.value?.can_edit);

const BADGE = {
  baru: { text: "Baru", color: "#b45309", bg: "#fef3c7" },
  diproses: { text: "Diproses", color: "#1d4ed8", bg: "#dbeafe" },
  diterima: { text: "Diterima", color: "#166534", bg: "#dcfce7" },
  ditolak: { text: "Ditolak", color: "#b91c1c", bg: "#fee2e2" },
};
const badge = computed(
  () => BADGE[pendaftaran.value?.status] || { text: pendaftaran.value?.status || "-", color: "#666", bg: "#eee" }
);

// Ikon pengumuman statis per status (dipakai via v-html, tanpa data user)
const ANNOUNCE_ICON = {
  baru: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
  diproses: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
  diterima: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
  ditolak: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
};
const ANNOUNCE_EYEBROW = {
  baru: "Informasi Penting",
  diproses: "Status Pendaftaran",
  diterima: "Pengumuman Hasil Seleksi",
  ditolak: "Pengumuman Hasil Seleksi",
};
const announceIcon = computed(() => ANNOUNCE_ICON[pendaftaran.value?.status] || ANNOUNCE_ICON.diproses);
const announceEyebrow = computed(() => ANNOUNCE_EYEBROW[pendaftaran.value?.status] || "Status Pendaftaran");

const deadlineText = computed(() => {
  if (!data.value?.deadline) return "-";
  return tglJam(data.value.deadline);
});
const hoursLeft = computed(() => {
  if (!data.value?.deadline) return 0;
  return Math.max(0, Math.round((new Date(data.value.deadline).getTime() - Date.now()) / 3600000));
});

function tgl(value) {
  if (!value) return "-";
  return new Date(value).toLocaleDateString("id-ID", { day: "numeric", month: "short", year: "numeric" });
}
function tglJam(value) {
  if (!value) return "-";
  const d = new Date(value);
  return (
    d.toLocaleDateString("id-ID", { day: "numeric", month: "short", year: "numeric" }) +
    " " +
    d.toLocaleTimeString("id-ID", { hour: "2-digit", minute: "2-digit" })
  );
}

function fillForm(p) {
  Object.assign(form, {
    nama_lengkap: p.nama_lengkap || "",
    nisn: p.nisn || "",
    tempat_lahir: p.tempat_lahir || "",
    tanggal_lahir: p.tanggal_lahir ? String(p.tanggal_lahir).slice(0, 10) : "",
    jenis_kelamin: p.jenis_kelamin || "Laki-laki",
    no_hp: p.no_hp || "",
    alamat: p.alamat || "",
    asal_sekolah: p.asal_sekolah || "",
    jurusan_pilihan: p.jurusan_pilihan || "RPL",
    nama_orang_tua: p.nama_orang_tua || "",
    no_hp_orang_tua: p.no_hp_orang_tua || "",
  });
}

async function loadData() {
  loadError.value = "";
  try {
    const res = await fetchJson(`${BACKEND}/siswa/data`);
    if (!res.ok) throw new Error("Server sibuk, coba lagi nanti.");
    data.value = await res.json();
    if (pendaftaran.value) fillForm(pendaftaran.value);
  } catch (e) {
    loadError.value = e.message || "Tidak bisa memuat data.";
  } finally {
    loading.value = false;
  }
}

function reload() {
  loading.value = true;
  loadData();
}

function scrollToEdit() {
  document.getElementById("edit-section")?.scrollIntoView({ behavior: "smooth", block: "start" });
}

async function submitEdit() {
  saving.value = true;
  try {
    const csrf = await getCsrfToken();
    const res = await fetchJson(`${BACKEND}/pendaftaran/${pendaftaran.value.id}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrf },
      body: JSON.stringify(form),
    });
    const out = await res.json().catch(() => ({}));
    if (!res.ok) {
      const first = out.errors ? Object.values(out.errors)[0]?.[0] : null;
      throw new Error(first || out.message || "Gagal menyimpan perubahan.");
    }
    showToast(out.message || "Data pendaftaran berhasil diperbarui.");
    await loadData();
  } catch (e) {
    showToast(e.message || "Gagal menyimpan perubahan.");
  } finally {
    saving.value = false;
  }
}

// Pantau perubahan status dari admin (pengganti location.reload() di versi Blade)
let pollTimer = null;
async function pollStatus() {
  try {
    const res = await fetchJson(`${BACKEND}/dashboard-siswa/snapshot`);
    if (!res.ok) return;
    const snap = await res.json();
    if (pendaftaran.value && snap.status && snap.status !== pendaftaran.value.status) {
      await loadData();
      showToast("Status pendaftaran kamu diperbarui.");
    }
  } catch {
    /* abaikan gangguan jaringan sesaat */
  }
}

onMounted(() => {
  loadData();
  pollTimer = setInterval(pollStatus, 15000);
});
onUnmounted(() => clearInterval(pollTimer));
</script>
<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700;800&display=swap");

.ds-root {
  font-family: "Quicksand", sans-serif;
  --ds-color-primary: #3a6450;
  --ds-color-primary-dark: #2f5b45;
  --ds-color-text: #1c2a23;
  --ds-color-text-secondary: #647067;
  --ds-color-border: #e8ece6;
  --ds-color-bg: #f8faf6;
  --ds-radius-sm: 8px;
  --ds-radius-md: 12px;
  --ds-radius-lg: 16px;
  --ds-shadow-sm: 0 4px 12px rgba(35, 55, 42, 0.08);
  --ds-transition: all 0.2s ease;
  --ds-gap: 10px;        /* jarak antar section/kartu */
  --ds-gap-inner: 12px;  /* jarak internal dalam kartu */
}

.ds-loading {
  padding: 60px 20px; text-align: center;
  font-size: 13px; font-weight: 700; color: var(--ds-color-text-secondary);
}

.ds-back-btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 7px 14px; border-radius: var(--ds-radius-sm); font-weight: 700; font-size: 12px;
  color: var(--ds-color-primary); background: #fff; border: 1.5px solid #dfe4dd;
  text-decoration: none; margin-bottom: var(--ds-gap); transition: var(--ds-transition);
}
.ds-back-btn:hover { background: #f0f4ee; border-color: var(--ds-color-primary); transform: translateX(-2px); }

.ds-banner {
  display: flex; align-items: center; justify-content: space-between; gap: 16px;
  background: linear-gradient(135deg, #2f5b45 0%, #3a6450 50%, #4a7a62 100%);
  border-radius: var(--ds-radius-lg); padding: 22px 26px; margin-bottom: var(--ds-gap);
  position: relative; overflow: hidden;
}
.ds-banner::before {
  content: ''; position: absolute; top: -50%; right: -10%; width: 260px; height: 260px;
  background: radial-gradient(circle, rgba(125, 184, 141, 0.25), transparent 70%);
  border-radius: 50%; pointer-events: none;
}
.ds-banner::after {
  content: ''; position: absolute; bottom: -30%; left: 20%; width: 180px; height: 180px;
  background: radial-gradient(circle, rgba(255,255,255,0.08), transparent 70%);
  border-radius: 50%; pointer-events: none;
}
.ds-banner-text { position: relative; z-index: 1; color: #fff; flex: 1; min-width: 0; }
.ds-banner-greeting { font-size: 13px; font-weight: 600; opacity: 0.85; margin: 0 0 4px; }
.ds-banner-title { font-size: 22px; font-weight: 800; margin: 0; letter-spacing: -0.02em; line-height: 1.2; }
.ds-banner-subtitle { font-size: 13px; font-weight: 500; opacity: 0.8; margin: 5px 0 0; line-height: 1.45; }
.ds-banner-doodle-wrap {
  position: relative; z-index: 1; flex-shrink: 0; margin-left: 8px;
  width: 120px; height: 120px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  background: radial-gradient(circle at 35% 30%, #ffffff 0%, #f1f7f0 72%);
  box-shadow: 0 10px 22px rgba(11, 30, 20, 0.28), 0 0 0 6px rgba(255, 255, 255, 0.14);
  animation: ds-float 4s ease-in-out infinite;
}
.ds-banner-doodle {
  height: 88px; width: auto; object-fit: contain;
  filter: drop-shadow(0 5px 10px rgba(0, 0, 0, 0.12));
}
@keyframes ds-float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }

.ds-stats { display: grid; grid-template-columns: repeat(3,1fr); gap: var(--ds-gap); margin-bottom: var(--ds-gap); }
.ds-stat-card {
  display: flex; align-items: center; gap: 12px;
  background: #fff; border: 1px solid var(--ds-color-border); border-radius: var(--ds-radius-md);
  padding: 12px 14px; transition: var(--ds-transition); position: relative; overflow: hidden;
}
.ds-stat-card::before {
  content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
  background: var(--ds-color-primary); opacity: 0; transition: var(--ds-transition);
}
.ds-stat-card:hover { box-shadow: var(--ds-shadow-sm); transform: translateY(-2px); }
.ds-stat-card:hover::before { opacity: 1; }
.ds-stat-icon {
  width: 38px; height: 38px; border-radius: var(--ds-radius-sm);
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.ds-stat-icon svg { width: 18px; height: 18px; }
.ds-stat-info { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.ds-stat-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--ds-color-text-secondary); }
.ds-stat-value { font-size: 13.5px; font-weight: 800; color: var(--ds-color-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.ds-card {
  background: #fff; border: 1px solid var(--ds-color-border); border-radius: var(--ds-radius-lg);
  padding: 18px 20px; margin-bottom: var(--ds-gap);
}
.ds-card--edit { margin-top: var(--ds-gap); scroll-margin-top: 12px; }
.ds-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--ds-gap-inner); flex-wrap: wrap; gap: 8px; }
.ds-card-title { font-size: 15px; font-weight: 800; color: var(--ds-color-text); margin: 0; letter-spacing: -0.01em; }
.ds-badge { padding: 4px 12px; border-radius: 999px; font-weight: 800; font-size: 11px; letter-spacing: 0.02em; }

.ds-announce {
  display: flex; flex-direction: column; align-items: center; text-align: center;
  border-radius: var(--ds-radius-lg); border: 1px solid; border-left-width: 5px;
  padding: 16px 20px; margin-bottom: var(--ds-gap);
}
.ds-announce--diterima { background: #f0fdf4; border-color: #d1ecd6; border-left-color: #166534; }
.ds-announce--ditolak { background: #fef2f2; border-color: #f5d6d6; border-left-color: #b91c1c; }
.ds-announce--diproses { background: #eff6ff; border-color: #d3e2fb; border-left-color: #1d4ed8; }
.ds-announce--baru { background: #fffbeb; border-color: #f3e4bd; border-left-color: #b45309; }
.ds-announce-icon {
  width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center; margin-bottom: 10px;
}
.ds-announce-icon svg { width: 18px; height: 18px; }
.ds-announce--diterima .ds-announce-icon { background: #dcfce7; color: #166534; }
.ds-announce--ditolak .ds-announce-icon { background: #fee2e2; color: #b91c1c; }
.ds-announce--diproses .ds-announce-icon { background: #dbeafe; color: #1d4ed8; }
.ds-announce--baru .ds-announce-icon { background: #fef3c7; color: #b45309; }
.ds-announce-body { flex: 1; min-width: 0; display: flex; flex-direction: column; align-items: center; }
.ds-announce-eyebrow {
  font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
  margin: 0 0 3px;
}
.ds-announce--diterima .ds-announce-eyebrow { color: #166534; }
.ds-announce--ditolak .ds-announce-eyebrow { color: #b91c1c; }
.ds-announce--diproses .ds-announce-eyebrow { color: #1d4ed8; }
.ds-announce--baru .ds-announce-eyebrow { color: #b45309; }
.ds-announce-text { font-size: 13px; line-height: 1.6; color: var(--ds-color-text); margin: 0; }
.ds-announce-text strong { font-weight: 800; }
.ds-announce-cta { margin-top: 10px; padding: 8px 16px; font-size: 12px; }
.ds-announce-cta svg { width: 14px; height: 14px; }

.ds-detail-grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(200px,1fr)); gap: 8px; }
.ds-detail-item {
  background: var(--ds-color-bg); border: 1px solid #eef1ec; border-radius: var(--ds-radius-sm);
  padding: 10px 12px; transition: var(--ds-transition);
}
.ds-detail-item:hover { border-color: #dde4da; }
.ds-detail-label { display: block; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--ds-color-text-secondary); margin-bottom: 2px; }
.ds-detail-value { font-size: 13px; font-weight: 700; color: var(--ds-color-text); word-break: break-word; }

.ds-actions { display: flex; gap: 8px; margin-bottom: var(--ds-gap); flex-wrap: wrap; }
.btn-ds {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 9px 18px; border-radius: var(--ds-radius-sm); font-weight: 700; font-size: 12.5px;
  text-decoration: none; transition: var(--ds-transition); border: none; cursor: pointer; font-family: inherit;
  white-space: nowrap;
}
.btn-ds svg { width: 14px; height: 14px; }
.btn-ds-primary { background: var(--ds-color-primary); color: #fff; box-shadow: 0 4px 12px rgba(58,100,80,0.2); }
.btn-ds-primary:hover { background: var(--ds-color-primary-dark); transform: translateY(-1px); }
.btn-ds-primary:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }
.btn-ds-outline { background: #fff; color: var(--ds-color-primary); border: 1.5px solid #dfe4dd; }
.btn-ds-outline:hover { background: #f0f4ee; border-color: var(--ds-color-primary); }

.ds-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.ds-form-actions { margin-top: 14px; }

.ds-deadline-text { font-size: 12px; color: var(--ds-color-text-secondary); margin: -6px 0 12px; line-height: 1.6; }
.ds-deadline-highlight { color: #b45309; }

.ds-empty { text-align: center; padding: 60px 20px; }
.ds-empty-icon {
  width: 68px; height: 68px; border-radius: var(--ds-radius-lg); background: #e8f0e6;
  display: flex; align-items: center; justify-content: center; margin: 0 auto 14px;
}
.ds-empty-icon svg { width: 40px; height: 40px; }
.ds-empty h3 { font-size: 18px; font-weight: 800; color: var(--ds-color-text); margin: 0 0 6px; }
.ds-empty p { font-size: 13px; color: var(--ds-color-text-secondary); margin: 0 auto 16px; max-width: 400px; }

@media (max-width: 768px) {
  .ds-banner { flex-direction: column; text-align: center; padding: 20px; }
  .ds-banner-doodle-wrap { width: 110px; height: 110px; margin-top: 12px; margin-left: 0; }
  .ds-banner-doodle { height: 80px; }
  .ds-banner-title { font-size: 19px; }
  .ds-stats { grid-template-columns: 1fr; }
  .ds-form-row { grid-template-columns: 1fr; }
  .ds-detail-grid { grid-template-columns: 1fr; }
  .ds-card { padding: 16px; }
  .ds-card-header { flex-direction: column; align-items: flex-start; gap: 6px; }
}

@media (max-width: 480px) {
  .ds-banner { padding: 18px 16px; }
  .ds-banner-title { font-size: 18px; }
  .ds-stat-card { padding: 10px 12px; }
  .btn-ds { width: 100%; justify-content: center; }
  .ds-actions { flex-direction: column; }
}

.section-divider { border: none; border-top: 1px solid var(--ds-color-border); margin: 12px 0; }

.form-group { margin-bottom: 10px; }
.form-group label { display: block; margin-bottom: 4px; font-weight: 700; color: var(--ds-color-primary); font-size: 11px; letter-spacing: 0.06em; text-transform: uppercase; }
.form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px 12px; border: 1.5px solid #dfe4dd; border-radius: var(--ds-radius-sm); font-family: inherit; font-size: 12.5px; color: var(--ds-color-text); background: #fff; transition: var(--ds-transition); box-sizing: border-box; }
.form-group select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23647067' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 34px; }
.form-group input::placeholder, .form-group textarea::placeholder { color: #a3a8a4; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: var(--ds-color-primary); box-shadow: 0 0 0 3px rgba(58, 100, 80, 0.1); }
.form-group textarea { resize: vertical; min-height: 60px; }
</style>