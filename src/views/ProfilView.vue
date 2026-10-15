<template>
  <div class="pf">
    <h1 class="pf-header-title">Profil Siswa</h1>

    <div v-if="loading" class="pf-card pf-card--empty" role="status">Memuat profil…</div>

    <div v-else-if="loadError" class="pf-card pf-card--empty" role="alert">
      <p>{{ loadError }}</p>
      <button type="button" class="pf-btn" @click="reload">Coba Lagi</button>
    </div>

    <template v-else>
      <!-- Kartu profil -->
      <section class="pf-card pf-card--profile">
        <router-link
          v-if="canEdit"
          to="/dashboard-siswa"
          class="pf-card-pencil"
          title="Edit data pendaftaran"
          aria-label="Edit data pendaftaran"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
        </router-link>

        <div class="pf-profile-top">
          <div class="pf-avatar" :class="{ 'is-loading': uploading }" id="pfAvatar">
            <img v-if="user?.avatar" :src="user.avatar" :alt="`Foto profil ${nama}`">
            <span v-else aria-hidden="true">{{ inisial }}</span>

            <label for="pfAvatarInput" class="pf-avatar-cam" title="Ganti foto profil" aria-label="Ganti foto profil">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
            </label>
          </div>
          <input type="file" name="avatar" id="pfAvatarInput" class="pf-file-input" accept="image/jpeg,image/png,image/webp" hidden @change="onAvatarChange">

          <div class="pf-profile-meta">
            <h2 class="pf-profile-name">{{ nama }}</h2>
            <ul class="pf-profile-list">
              <li><span class="pf-muted">NISN</span><strong>{{ val(pendaftaran?.nisn) }}</strong></li>
              <li><span class="pf-muted">Jurusan</span><strong>{{ val(pendaftaran?.jurusan_pilihan) }}</strong></li>
              <li><span class="pf-muted">Status</span><span class="pf-pill" :class="`pf-pill--${statusClass}`">{{ statusLabel }}</span></li>
            </ul>
          </div>
        </div>

        <p v-if="user?.avatar" class="pf-upload-hint">
          <button type="button" class="pf-link-btn" @click="removeAvatar">Hapus foto profil</button>
        </p>
        <p v-else class="pf-upload-hint">Klik ikon kamera untuk unggah foto profil (JPG/PNG/WebP, maks 512&nbsp;KB).</p>
      </section>

      <!-- Tabs -->
      <div class="pf-tabs" role="tablist" aria-label="Section profil">
        <button
          v-for="t in tabs"
          :key="t.key"
          class="pf-tab"
          :class="{ 'is-active': tab === t.key }"
          role="tab"
          :aria-selected="tab === t.key ? 'true' : 'false'"
          :tabindex="tab === t.key ? 0 : -1"
          @click="activate(t.key)"
          @keydown.right="activate(nextTab(t.key), true)"
          @keydown.left="activate(prevTab(t.key), true)"
        >{{ t.label }}</button>
      </div>

      <!-- Panel: Profil -->
      <div v-show="tab === 'profil'" class="pf-panel" role="tabpanel">
        <h2 class="pf-block-title">Informasi Akademik</h2>
        <div class="pf-grid">
          <article v-for="s in stats" :key="s.label" class="pf-stat">
            <span class="pf-stat-label">{{ s.label }}</span>
            <span class="pf-stat-value">{{ s.value }}</span>
            <button type="button" class="pf-stat-link" @click="activate('pendaftaran', true)">Lihat detail</button>
          </article>
        </div>

        <section class="pf-card">
          <div class="pf-card-head">
            <h3>Informasi Pribadi</h3>
            <router-link
              v-if="canEdit"
              to="/dashboard-siswa"
              class="pf-card-pencil pf-card-pencil--inline"
              title="Edit data"
              aria-label="Edit data pribadi"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
            </router-link>
          </div>
          <div class="pf-rows">
            <div class="pf-row"><span class="pf-row-label">Nama Lengkap</span><span class="pf-row-value">{{ nama }}</span></div>
            <div class="pf-row"><span class="pf-row-label">NISN</span><span class="pf-row-value">{{ val(pendaftaran?.nisn) }}</span></div>
            <div class="pf-row"><span class="pf-row-label">NIK</span><span class="pf-row-value">{{ val(pendaftaran?.nik) }}</span></div>
            <div class="pf-row"><span class="pf-row-label">Tempat, Tanggal Lahir</span><span class="pf-row-value">{{ tempatTanggalLahir }}</span></div>
            <div class="pf-row"><span class="pf-row-label">Jenis Kelamin</span><span class="pf-row-value">{{ val(pendaftaran?.jenis_kelamin) }}</span></div>
            <div class="pf-row"><span class="pf-row-label">Agama</span><span class="pf-row-value">{{ val(pendaftaran?.agama) }}</span></div>
            <div class="pf-row"><span class="pf-row-label">Kewarganegaraan</span><span class="pf-row-value">{{ val(pendaftaran?.kewarnegaraan) }}</span></div>
            <div class="pf-row"><span class="pf-row-label">No. HP</span><span class="pf-row-value">{{ val(pendaftaran?.no_hp || user?.username) }}</span></div>
            <div class="pf-row"><span class="pf-row-label">Email</span><span class="pf-row-value">{{ pendaftaran?.email || user?.email || "-" }}</span></div>
            <div class="pf-row"><span class="pf-row-label">Alamat</span><span class="pf-row-value">{{ alamatLengkap }}</span></div>
            <div class="pf-row"><span class="pf-row-label">Username</span><span class="pf-row-value">{{ user?.username || "-" }}</span></div>
            <div class="pf-row"><span class="pf-row-label">Terdaftar Pada</span><span class="pf-row-value">{{ tgl(pendaftaran?.created_at) }}</span></div>
          </div>
        </section>
      </div>

      <!-- Panel: Pendaftaran -->
      <div v-show="tab === 'pendaftaran'" class="pf-panel" role="tabpanel">
        <template v-if="pendaftaran">
          <section class="pf-card">
            <div class="pf-card-head"><h3>Data Sekolah &amp; Seleksi</h3></div>
            <div class="pf-rows">
              <div class="pf-row"><span class="pf-row-label">Asal Sekolah</span><span class="pf-row-value">{{ val(pendaftaran.asal_sekolah) }}</span></div>
              <div class="pf-row"><span class="pf-row-label">Gelombang</span><span class="pf-row-value">{{ val(pendaftaran.gelombang) }}</span></div>
              <div class="pf-row"><span class="pf-row-label">Tahun Lulus</span><span class="pf-row-value">{{ val(pendaftaran.tahun_lulus) }}</span></div>
              <div class="pf-row"><span class="pf-row-label">Rata-rata Nilai</span><span class="pf-row-value">{{ val(pendaftaran.rata_rata_nilai) }}</span></div>
              <div class="pf-row"><span class="pf-row-label">Jurusan Pilihan</span><span class="pf-row-value">{{ val(pendaftaran.jurusan_pilihan) }}</span></div>
              <div class="pf-row"><span class="pf-row-label">Kategori Pendaftar</span><span class="pf-row-value">{{ val(pendaftaran.kategori_pendaftar) }}</span></div>
              <div class="pf-row"><span class="pf-row-label">Jenis Pembayaran</span><span class="pf-row-value">{{ val(pendaftaran.jenis_pembayaran) }}</span></div>
              <div class="pf-row"><span class="pf-row-label">Berkas Tambahan</span><span class="pf-row-value">{{ val(pendaftaran.berkas_tambahan) }}</span></div>
            </div>
          </section>

          <section class="pf-card">
            <div class="pf-card-head"><h3>Status Pendaftaran</h3></div>
            <div class="pf-rows">
              <div class="pf-row"><span class="pf-row-label">Status</span><span class="pf-row-value"><span class="pf-pill" :class="`pf-pill--${statusClass}`">{{ statusLabel }}</span></span></div>
              <div class="pf-row"><span class="pf-row-label">Tanggal Daftar</span><span class="pf-row-value">{{ tglFull(pendaftaran.created_at) }}</span></div>
              <div class="pf-row"><span class="pf-row-label">Terakhir Diperbarui</span><span class="pf-row-value">{{ tglFull(pendaftaran.status_updated_at || pendaftaran.updated_at) }}</span></div>
              <div class="pf-row"><span class="pf-row-label">Data Dikonfirmasi</span><span class="pf-row-value">{{ pendaftaran.data_confirmed ? "Sudah" : "Belum" }}</span></div>
            </div>
          </section>

          <section class="pf-card">
            <div class="pf-card-head"><h3>Berkas Upload</h3></div>
            <div class="pf-rows">
              <div v-for="b in berkas" :key="b.label" class="pf-row">
                <span class="pf-row-label">{{ b.label }}</span>
                <span class="pf-row-value pf-file-state" :class="b.file ? 'is-ok' : 'is-empty'">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                    <template v-if="b.file"><path d="M20 6L9 17l-5-5"/></template>
                    <template v-else><circle cx="12" cy="12" r="9"/><path d="M15 9l-6 6M9 9l6 6"/></template>
                  </svg>
                  {{ b.file ? "Terunggah" : "Belum diunggah" }}
                </span>
              </div>
            </div>
          </section>
        </template>
        <div v-else class="pf-card pf-card--empty">
          <p>Kamu belum mengisi formulir pendaftaran. Lengkapi data dulu supaya profil dan status pendaftaran bisa tampil di sini.</p>
          <a class="pf-btn" :href="`${BACKEND}/pendaftaran/create`">Isi Formulir Pendaftaran</a>
        </div>
      </div>

      <!-- Panel: Keluarga -->
      <div v-show="tab === 'keluarga'" class="pf-panel" role="tabpanel">
        <template v-if="pendaftaran">
          <section class="pf-card">
            <div class="pf-card-head"><h3>Data Keluarga</h3></div>
            <div class="pf-rows">
              <div class="pf-row"><span class="pf-row-label">Jumlah Saudara</span><span class="pf-row-value">{{ val(pendaftaran.jumlah_saudara) }}</span></div>
              <div class="pf-row"><span class="pf-row-label">Anak Ke</span><span class="pf-row-value">{{ val(pendaftaran.anak_ke) }}</span></div>
              <div class="pf-row"><span class="pf-row-label">Status Keluarga</span><span class="pf-row-value">{{ val(pendaftaran.status_keluarga) }}</span></div>
              <div class="pf-row"><span class="pf-row-label">Email Orang Tua</span><span class="pf-row-value">{{ val(pendaftaran.email_orang_tua) }}</span></div>
            </div>
          </section>

          <div class="pf-duo">
            <section class="pf-card">
              <div class="pf-card-head"><h3>Ayah</h3></div>
              <div class="pf-rows">
                <div class="pf-row"><span class="pf-row-label">Nama</span><span class="pf-row-value">{{ val(pendaftaran.nama_ayah) }}</span></div>
                <div v-for="r in rowsAyah" :key="r.label" class="pf-row"><span class="pf-row-label">{{ r.label }}</span><span class="pf-row-value">{{ val(r.value) }}</span></div>
              </div>
            </section>

            <section class="pf-card">
              <div class="pf-card-head"><h3>Ibu</h3></div>
              <div class="pf-rows">
                <div class="pf-row"><span class="pf-row-label">Nama</span><span class="pf-row-value">{{ val(pendaftaran.nama_ibu) }}</span></div>
                <div v-for="r in rowsIbu" :key="r.label" class="pf-row"><span class="pf-row-label">{{ r.label }}</span><span class="pf-row-value">{{ val(r.value) }}</span></div>
              </div>
            </section>
          </div>

          <section class="pf-card">
            <div class="pf-card-head"><h3>Wali</h3></div>
            <div class="pf-rows">
              <div class="pf-row"><span class="pf-row-label">Nama Wali</span><span class="pf-row-value">{{ val(pendaftaran.nama_wali) }}</span></div>
              <div class="pf-row"><span class="pf-row-label">Hubungan</span><span class="pf-row-value">{{ val(pendaftaran.hubungan_wali) }}</span></div>
            </div>
          </section>
        </template>
        <div v-else class="pf-card pf-card--empty">
          <p>Data keluarga akan tampil di sini setelah formulir pendaftaran kamu diisi.</p>
          <a class="pf-btn" :href="`${BACKEND}/pendaftaran/create`">Isi Formulir Pendaftaran</a>
        </div>
      </div>

      <div class="pf-footer">
        <router-link class="pf-btn pf-btn--ghost" to="/dashboard-siswa">Dashboard Siswa</router-link>
        <button type="button" class="pf-btn pf-btn--logout" @click="logout">Logout</button>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import { fetchJson } from "@/services/fetchJson";
import { getCsrfToken } from "@/services/csrf";
import { useAuthSession, BACKEND } from "@/composable/useAuthSession";
import { useToast } from "@/composable/useToast";

const router = useRouter();
const { clearSession } = useAuthSession();
const { showToast } = useToast();

const loading = ref(true);
const loadError = ref("");
const data = ref(null); // { user, pendaftaran, can_edit }
const uploading = ref(false);
const tab = ref("profil");

const user = computed(() => data.value?.user || null);
const pendaftaran = computed(() => data.value?.pendaftaran || null);
const canEdit = computed(() => !!data.value?.can_edit);

const nama = computed(() => pendaftaran.value?.nama_lengkap || user.value?.name || "-");
const inisial = computed(() => (nama.value || "?").charAt(0).toUpperCase());

const STATUS_LABEL = { baru: "Baru", diproses: "Diproses", diterima: "Diterima", ditolak: "Ditolak" };
const statusLabel = computed(() => STATUS_LABEL[pendaftaran.value?.status] || "Belum Mendaftar");
const statusClass = computed(() => pendaftaran.value?.status || "kosong");

const tabs = [
  { key: "profil", label: "Profil Siswa" },
  { key: "pendaftaran", label: "Informasi Pendaftaran" },
  { key: "keluarga", label: "Data Anggota Keluarga" },
];
function activate(key, moveFocus) {
  if (!key) return;
  tab.value = key;
  if (moveFocus) document.querySelector(`.pf-tab:nth-child(${tabs.findIndex((t) => t.key === key) + 1})`)?.focus();
}
function nextTab(key) {
  const i = tabs.findIndex((t) => t.key === key);
  return tabs[(i + 1) % tabs.length].key;
}
function prevTab(key) {
  const i = tabs.findIndex((t) => t.key === key);
  return tabs[(i - 1 + tabs.length) % tabs.length].key;
}

const stats = computed(() => [
  { label: "Jurusan", value: val(pendaftaran.value?.jurusan_pilihan) },
  { label: "Nilai Rata-rata", value: val(pendaftaran.value?.rata_rata_nilai) },
  { label: "Status", value: statusLabel.value },
  { label: "Gelombang", value: val(pendaftaran.value?.gelombang) },
]);

const berkas = computed(() => [
  { label: "Foto 3×4", file: pendaftaran.value?.foto_3x4 },
  { label: "Kartu Keluarga", file: pendaftaran.value?.kk_file },
  { label: "Ijazah / SKL", file: pendaftaran.value?.ijazah_file },
  { label: "SKTM", file: pendaftaran.value?.sktm_file },
]);

const rowsAyah = computed(() => [
  { label: "Pendidikan", value: pendaftaran.value?.pendidikan_ayah },
  { label: "Pekerjaan", value: pendaftaran.value?.pekerjaan_ayah },
  { label: "Penghasilan", value: pendaftaran.value?.penghasilan_ayah },
  { label: "Alamat", value: pendaftaran.value?.alamat_ayah },
  { label: "No. HP", value: pendaftaran.value?.hp_ayah },
]);
const rowsIbu = computed(() => [
  { label: "Pendidikan", value: pendaftaran.value?.pendidikan_ibu },
  { label: "Pekerjaan", value: pendaftaran.value?.pekerjaan_ibu },
  { label: "Penghasilan", value: pendaftaran.value?.penghasilan_ibu },
  { label: "Alamat", value: pendaftaran.value?.alamat_ibu },
  { label: "No. HP", value: pendaftaran.value?.hp_ibu },
]);

const tempatTanggalLahir = computed(() => {
  const p = pendaftaran.value;
  if (!p?.tempat_lahir) return "-";
  return `${p.tempat_lahir}, ${tgl(p.tanggal_lahir)}`;
});
const alamatLengkap = computed(() => {
  const p = pendaftaran.value;
  if (!p?.alamat) return "-";
  return [p.alamat, p.rt_rw, p.kode_pos].filter(Boolean).join(", ");
});

function val(v) {
  return v === null || v === undefined || v === "" ? "-" : v;
}
function tgl(value) {
  if (!value) return "-";
  return new Date(value).toLocaleDateString("id-ID", { day: "numeric", month: "long", year: "numeric" });
}
function tglFull(value) {
  if (!value) return "-";
  const d = new Date(value);
  return (
    d.toLocaleDateString("id-ID", { day: "numeric", month: "long", year: "numeric" }) +
    ", " +
    d.toLocaleTimeString("id-ID", { hour: "2-digit", minute: "2-digit" })
  );
}

async function loadData() {
  loadError.value = "";
  try {
    const res = await fetchJson(`${BACKEND}/siswa/data`);
    if (!res.ok) throw new Error("Server sibuk, coba lagi nanti.");
    data.value = await res.json();
  } catch (e) {
    loadError.value = e.message || "Tidak bisa memuat profil.";
  } finally {
    loading.value = false;
  }
}
function reload() {
  loading.value = true;
  loadData();
}

async function onAvatarChange(e) {
  const file = e.target.files?.[0];
  if (!file) return;

  const allowed = ["image/jpeg", "image/png", "image/webp"];
  if (!allowed.includes(file.type) || file.size > 512 * 1024) {
    showToast("Foto harus format JPG, PNG, atau WebP dan maksimal 512 KB.");
    e.target.value = "";
    return;
  }

  uploading.value = true;
  try {
    const csrf = await getCsrfToken();
    const fd = new FormData();
    fd.append("avatar", file);
    const res = await fetchJson(`${BACKEND}/profil/avatar`, {
      method: "POST",
      headers: { "X-CSRF-TOKEN": csrf },
      body: fd,
    });
    const out = await res.json().catch(() => ({}));
    if (!res.ok) {
      const first = out.errors ? Object.values(out.errors)[0]?.[0] : null;
      throw new Error(first || out.message || "Gagal mengunggah foto.");
    }
    if (data.value) data.value.user = { ...data.value.user, avatar: out.avatar };
    showToast(out.message || "Foto profil berhasil diperbarui.");
  } catch (err) {
    showToast(err.message || "Gagal mengunggah foto.");
  } finally {
    uploading.value = false;
    e.target.value = "";
  }
}

async function removeAvatar() {
  if (!confirm("Hapus foto profil?")) return;
  try {
    const csrf = await getCsrfToken();
    const res = await fetchJson(`${BACKEND}/profil/avatar`, {
      method: "DELETE",
      headers: { "X-CSRF-TOKEN": csrf },
    });
    const out = await res.json().catch(() => ({}));
    if (!res.ok) throw new Error(out.message || "Gagal menghapus foto.");
    if (data.value) data.value.user = { ...data.value.user, avatar: null };
    showToast(out.message || "Foto profil telah dihapus.");
  } catch (err) {
    showToast(err.message || "Gagal menghapus foto.");
  }
}

async function logout() {
  try {
    const csrf = await getCsrfToken();
    await fetchJson(`${BACKEND}/logout`, {
      method: "POST",
      headers: { "X-CSRF-TOKEN": csrf },
    });
  } catch {
    /* sesi mungkin sudah mati — tetap bersihkan state lokal */
  }
  clearSession();
  showToast("Berhasil keluar.");
  router.push("/");
}

onMounted(loadData);
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700;800&display=swap");

.pf {
  font-family: "Quicksand", sans-serif;
  --pf-primary: #3a6450;
  --pf-primary-dark: #2f5b45;
  --pf-text: #1c2a23;
  --pf-text-soft: #647067;
  --pf-border: #e6ebe4;
  --pf-bg: #f4f7f2;
  --pf-radius: 18px;
  --pf-shadow: 0 8px 24px rgba(28, 42, 35, 0.07);

  max-width: 880px;
  margin: 0 auto;
  padding: 24px 20px 48px;
  color: var(--pf-text);
}

.pf-header-title {
  margin: 0 0 18px;
  font-size: 20px;
  font-weight: 800;
  text-align: center;
  letter-spacing: -0.01em;
}

/* Card */
.pf-card {
  position: relative;
  background: #fff;
  border: 1px solid var(--pf-border);
  border-radius: var(--pf-radius);
  box-shadow: var(--pf-shadow);
  padding: 20px;
  margin-bottom: 16px;
}
.pf-card--profile { padding: 22px; }
.pf-card-head {
  display: flex; align-items: center; justify-content: space-between; gap: 12px;
    margin-bottom: 12px;
}
.pf-card-head h3 { margin: 0; font-size: 15px; font-weight: 800; letter-spacing: -0.01em; }
.pf-card-pencil {
    position: absolute; top: 18px; right: 18px;
    width: 34px; height: 34px;
    display: flex; align-items: center; justify-content: center;
    border-radius: 10px;
    background: var(--pf-bg);
    color: var(--pf-primary);
    border: 1px solid var(--pf-border);
    text-decoration: none;
    transition: all 0.2s ease;
}
.pf-card-pencil svg { width: 16px; height: 16px; }
.pf-card-pencil:hover { background: var(--pf-primary); color: #fff; border-color: var(--pf-primary); }
.pf-card-pencil--inline { position: static; }

/* Profile card */
.pf-profile-top { display: flex; align-items: center; gap: 20px; }
.pf-avatar {
    position: relative;
    width: 96px; height: 96px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2f5b45, #4a7a62);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 36px; font-weight: 800;
    box-shadow: 0 8px 20px rgba(58, 100, 80, 0.28);
    overflow: visible;
    flex-shrink: 0;
}
.pf-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; display: block; }
.pf-avatar-cam {
    position: absolute;
    right: -2px; bottom: -2px;
    width: 32px; height: 32px;
    border-radius: 50%;
    background: #fff;
    color: var(--pf-primary);
    border: 1px solid var(--pf-border);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(28, 42, 35, 0.16);
    transition: all 0.2s ease;
}
.pf-avatar-cam svg { width: 15px; height: 15px; }
.pf-avatar-cam:hover { background: var(--pf-primary); color: #fff; }
.pf-avatar.is-loading { opacity: 0.6; }

.pf-profile-meta { min-width: 0; }
.pf-profile-name {
    margin: 0 0 8px;
    font-size: 22px;
    font-weight: 800;
    letter-spacing: -0.02em;
    line-height: 1.2;
    word-break: break-word;
    padding-right: 40px;
}
.pf-profile-list { list-style: none; margin: 0; padding: 0; display: grid; gap: 5px; }
.pf-profile-list li { display: flex; align-items: center; gap: 8px; font-size: 13.5px; }
.pf-profile-list strong { font-weight: 700; }
.pf-muted { color: var(--pf-text-soft); min-width: 58px; }

.pf-upload-hint { margin: 14px 0 0; font-size: 12.5px; color: var(--pf-text-soft); line-height: 1.6; }
.pf-link-btn {
    background: none; border: none; padding: 0;
    color: #99332b; font-size: 12.5px; font-weight: 700;
    cursor: pointer; text-decoration: underline; text-underline-offset: 3px;
    font-family: inherit;
}
.pf-link-btn:hover { color: #7a2820; }

/* Pill status */
.pf-pill {
    display: inline-block;
    padding: 3px 11px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
}
.pf-pill--baru { background: #eef1f0; color: #4c5a63; }
.pf-pill--diproses { background: #fff3d6; color: #8a6d1a; }
.pf-pill--diterima { background: #d4edda; color: #155724; }
.pf-pill--ditolak { background: #f8d7da; color: #842029; }
.pf-pill--kosong { background: #f1f3f1; color: #7a857e; }

/* Tabs */
.pf-tabs {
    display: flex;
    gap: 4px;
    background: #fff;
    border: 1px solid var(--pf-border);
    border-radius: 14px;
    padding: 5px;
    margin-bottom: 18px;
    box-shadow: var(--pf-shadow);
}
.pf-tab {
    flex: 1;
    padding: 10px 8px;
    border: none;
    background: none;
    border-radius: 10px;
    font-size: 13.5px;
    font-weight: 700;
    color: var(--pf-text-soft);
    cursor: pointer;
    font-family: inherit;
    transition: all 0.2s ease;
}
.pf-tab:hover { color: var(--pf-primary); background: var(--pf-bg); }
.pf-tab.is-active { background: var(--pf-primary); color: #fff; box-shadow: 0 4px 12px rgba(58, 100, 80, 0.28); }

/* Kartu statistik */
.pf-block-title {
    font-size: 12px;
    font-weight: 800;
    color: var(--pf-primary);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin: 0 0 10px 2px;
}
.pf-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 16px;
}
.pf-stat {
    background: #2f5b45;
    color: #fff;
    border-radius: var(--pf-radius);
    padding: 16px 14px;
    min-height: 128px;
    display: flex;
    flex-direction: column;
    box-shadow: 0 8px 20px rgba(58, 100, 80, 0.22);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.pf-stat:nth-child(2) { background: linear-gradient(150deg, #2f5b45 0%, #437159 100%); }
.pf-stat:nth-child(3) { background: linear-gradient(150deg, #4a7a62 0%, #639a80 100%); }
.pf-stat:nth-child(4) { background: linear-gradient(150deg, #35604a 0%, #4c7f65 100%); }
.pf-stat:hover { transform: translateY(-3px); box-shadow: 0 14px 28px rgba(58, 100, 80, 0.26); }
.pf-stat-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    opacity: 0.85;
}
.pf-stat-value {
    font-size: 20px;
    font-weight: 800;
    margin: 8px 0 auto;
    letter-spacing: -0.02em;
    line-height: 1.2;
    word-break: break-word;
}
.pf-stat-link {
    margin-top: 12px;
    align-self: flex-start;
    background: rgba(255, 255, 255, 0.16);
    border: none;
    color: #fff;
    font-family: inherit;
    font-size: 11.5px;
    font-weight: 700;
    padding: 5px 10px;
    border-radius: 999px;
    cursor: pointer;
    transition: background 0.2s ease;
}
.pf-stat-link:hover { background: rgba(255, 255, 255, 0.3); }

/* Rows */
.pf-rows { display: grid; }
.pf-row {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 16px;
    padding: 11px 0;
    border-bottom: 1px solid #f1f4f0;
}
.pf-row:last-child { border-bottom: none; }
.pf-row-label { font-size: 13px; color: var(--pf-text-soft); font-weight: 600; flex-shrink: 0; }
.pf-row-value { font-size: 13.5px; font-weight: 700; text-align: right; word-break: break-word; }

.pf-file-state { display: inline-flex; align-items: center; gap: 6px; }
.pf-file-state svg { width: 15px; height: 15px; }
.pf-file-state.is-ok { color: #2c6b46; }
.pf-file-state.is-empty { color: #8a938c; font-weight: 600; }

.pf-duo { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.pf-duo .pf-card { margin-bottom: 16px; }

/* Empty & footer */
.pf-card--empty { text-align: center; padding: 32px 24px; }
.pf-card--empty p { margin: 0 0 18px; color: var(--pf-text-soft); font-size: 14px; line-height: 1.7; }

.pf-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid var(--pf-border);
    flex-wrap: wrap;
}
.pf-btn {
    display: inline-block;
    padding: 12px 22px;
    background: var(--pf-primary);
    color: #fff;
    border-radius: 13px;
    font-weight: 700;
    font-size: 13.5px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    font-family: inherit;
    box-shadow: 0 6px 16px rgba(58, 100, 80, 0.2);
    transition: all 0.2s ease;
}
.pf-btn:hover { background: var(--pf-primary-dark); color: #fff; transform: translateY(-2px); }
.pf-btn--ghost { background: #fff; color: var(--pf-primary); border: 1px solid var(--pf-border); box-shadow: none; }
.pf-btn--ghost:hover { background: var(--pf-bg); color: var(--pf-primary-dark); }
.pf-btn--logout { background: #fff; color: #b3362c; border: 1px solid #e6c3bf; box-shadow: none; }
.pf-btn--logout:hover { background: #fdf1f0; color: #99332b; }

@media (max-width: 760px) {
    .pf-grid { grid-template-columns: repeat(2, 1fr); }
    .pf-duo { grid-template-columns: 1fr; }
}
@media (max-width: 520px) {
    .pf { padding: 18px 14px 40px; }
    .pf-profile-top { flex-direction: column; text-align: center; }
    .pf-profile-name { padding-right: 0; font-size: 19px; }
    .pf-profile-list { justify-items: center; }
    .pf-profile-list li { justify-content: center; }
    .pf-muted { min-width: auto; }
    .pf-row { flex-direction: column; align-items: flex-start; gap: 3px; }
    .pf-row-value { text-align: left; }
    .pf-stat { min-height: 112px; padding: 14px 12px; }
    .pf-stat-value { font-size: 17px; }
    .pf-footer { flex-direction: column-reverse; }
    .pf-footer .pf-btn { width: 100%; text-align: center; }
}
</style>
