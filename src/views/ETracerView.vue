<template>
  <section class="tracer-page">
    <div class="top-bar">
      <button type="button" class="back-button" @click="goBack">
        <span class="back-icon">&lt;</span>
      </button>
    </div>

    <div class="page-header">
      <div>
        <span class="page-label">E-Tracer Study</span>
        <h1>Tracer Study Alumni</h1>
        <p>Trace perjalanan lulusan SMK Bahrul Ulum setelah menyelesaikan pendidikan. Data ini penting untuk pengembangan kurikulum dan peningkatan mutu pendidikan.</p>
      </div>
      <div class="header-actions">
        <div class="action-panel">
          <div class="summary-pill">
            <strong>{{ stats.total }}</strong>
            <span>Alumni terdata</span>
          </div>
          <div class="summary-pill">
            <strong>{{ stats.working }}%</strong>
            <span>Bekerja</span>
          </div>
          <div class="summary-pill">
            <strong>{{ stats.college }}%</strong>
            <span>Kuliah</span>
          </div>
        </div>
      </div>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon" style="background: #eaf2f8;">💼</div>
        <div class="stat-info">
          <strong>{{ stats.working }}%</strong>
          <span>Bekerja</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background: #eafaf1;">🎓</div>
        <div class="stat-info">
          <strong>{{ stats.college }}%</strong>
          <span>Kuliah</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background: #fef9e7;">🏢</div>
        <div class="stat-info">
          <strong>{{ stats.internship }}%</strong>
          <span>PKL/Magang</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background: #f4ecf7;">🚀</div>
        <div class="stat-info">
          <strong>{{ stats.entrepreneur }}%</strong>
          <span>Wirausaha</span>
        </div>
      </div>
    </div>

    <div class="section-title">
      <h2>Data Tracer Study</h2>
      <p>Isi form berikut untuk membantu sekolah memantau perkembangan lulusan</p>
    </div>

    <form class="tracer-form" @submit.prevent="submitForm">
      <div class="form-section">
        <h3>Data Diri</h3>
        <div class="form-grid">
          <div class="form-group">
            <label for="nama">Nama Lengkap *</label>
            <select id="nama" v-model="form.nama" required>
              <option value="">-- Pilih Nama --</option>
              <option v-for="name in alumniNames" :key="name" :value="name">{{ name }}</option>
            </select>
          </div>
          <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" v-model="form.email" placeholder="email@contoh.com" required />
          </div>
          <div class="form-group">
            <label for="nik">NIK</label>
            <input type="text" id="nik" v-model="form.nik" placeholder="Nomor Induk Kependudukan" />
          </div>
          <div class="form-group">
            <label for="jenisKelamin">Jenis Kelamin *</label>
            <div class="radio-group">
              <label class="radio-label">
                <input type="radio" v-model="form.jenisKelamin" value="Laki-laki" required />
                Laki-laki
              </label>
              <label class="radio-label">
                <input type="radio" v-model="form.jenisKelamin" value="Perempuan" />
                Perempuan
              </label>
            </div>
          </div>
          <div class="form-group">
            <label for="tempatLahir">Tempat Lahir</label>
            <input type="text" id="tempatLahir" v-model="form.tempatLahir" placeholder="Kota/Kabupaten" />
          </div>
          <div class="form-group">
            <label for="tanggalLahir">Tanggal Lahir</label>
            <input type="date" id="tanggalLahir" v-model="form.tanggalLahir" />
          </div>
          <div class="form-group">
            <label for="tahunLulus">Tahun Lulus *</label>
            <select id="tahunLulus" v-model="form.tahunLulus" required>
              <option value="">-- Pilih Tahun --</option>
              <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
            </select>
          </div>
          <div class="form-group">
            <label for="noHp">No. HP/WA</label>
            <input type="tel" id="noHp" v-model="form.noHp" placeholder="08xxxxxxxxxx" />
          </div>
          <div class="form-group">
            <label for="pendidikanTerakhir">Pendidikan Terakhir</label>
            <select id="pendidikanTerakhir" v-model="form.pendidikanTerakhir">
              <option value="">-- Pilih --</option>
              <option>SMA/SMK</option>
              <option>D1</option>
              <option>D2</option>
              <option>D3</option>
              <option>D4/S1</option>
              <option>S2</option>
              <option>S3</option>
            </select>
          </div>
        </div>
      </div>

      <div class="form-section">
        <h3>Status Setelah Lulus</h3>
        <div class="form-group">
          <label>Setelah Lulus, Anda Saat Ini: *</label>
          <div class="radio-group">
            <label class="radio-label">
              <input type="radio" v-model="form.status" value="Bekerja" required />
              Bekerja
            </label>
            <label class="radio-label">
              <input type="radio" v-model="form.status" value="Kuliah" />
              Kuliah
            </label>
            <label class="radio-label">
              <input type="radio" v-model="form.status" value="PKL/Magang" />
              PKL/Magang
            </label>
            <label class="radio-label">
              <input type="radio" v-model="form.status" value="Wirausaha" />
              Wirausaha
            </label>
            <label class="radio-label">
              <input type="radio" v-model="form.status" value="Mencari Kerja" />
              Mencari Kerja
            </label>
          </div>
        </div>
      </div>

      <div class="form-section" v-if="form.status === 'Kuliah'">
        <h3>Data Kuliah</h3>
        <div class="form-grid">
          <div class="form-group">
            <label for="namaUniversitas">Nama Universitas/Sekolah</label>
            <input type="text" id="namaUniversitas" v-model="form.namaUniversitas" placeholder="Nama institusi pendidikan" />
          </div>
          <div class="form-group">
            <label for="tahunMasuk">Tahun Masuk</label>
            <input type="number" id="tahunMasuk" v-model="form.tahunMasuk" min="2000" max="2099" placeholder="2024" />
          </div>
        </div>
      </div>

      <div class="form-section" v-if="form.status === 'Bekerja'">
        <h3>Data Bekerja</h3>
        <div class="form-grid">
          <div class="form-group">
            <label for="namaPerusahaan">Nama Perusahaan</label>
            <input type="text" id="namaPerusahaan" v-model="form.namaPerusahaan" placeholder="Nama perusahaan" />
          </div>
          <div class="form-group">
            <label for="bidang">Bidang</label>
            <input type="text" id="bidang" v-model="form.bidang" placeholder="Teknologi, Akuntansi, dll" />
          </div>
          <div class="form-group">
            <label for="jabatan">Jabatan</label>
            <input type="text" id="jabatan" v-model="form.jabatan" placeholder="Software Engineer, Staff Accounting, dll" />
          </div>
          <div class="form-group">
            <label for="alamatPerusahaan">Alamat Perusahaan</label>
            <input type="text" id="alamatPerusahaan" v-model="form.alamatPerusahaan" placeholder="Alamat perusahaan" />
          </div>
        </div>
      </div>

      <div class="form-section">
        <h3>Data Kependudukan</h3>
        <div class="form-grid">
          <div class="form-group">
            <label for="kecamatan">Kecamatan (KTP)</label>
            <input type="text" id="kecamatan" v-model="form.kecamatan" placeholder="Kecamatan" />
          </div>
          <div class="form-group">
            <label for="kelurahan">Kelurahan (KTP)</label>
            <input type="text" id="kelurahan" v-model="form.kelurahan" placeholder="Kelurahan" />
          </div>
          <div class="form-group">
            <label for="rtRw">RT/RW (KTP)</label>
            <input type="text" id="rtRw" v-model="form.rtRw" placeholder="001/002" />
          </div>
          <div class="form-group">
            <label for="alamatDomisili">Alamat Domisili</label>
            <input type="text" id="alamatDomisili" v-model="form.alamatDomisili" placeholder="Alamat lengkap domisili saat ini" />
          </div>
        </div>
      </div>

      <div class="form-section">
        <h3>Pesan untuk Sekolah</h3>
        <div class="form-group full">
          <label for="pesan">Saran dan Pesan</label>
          <textarea id="pesan" v-model="form.pesan" rows="4" placeholder="Tuliskan saran atau pesan untuk sekolah..."></textarea>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-submit" :disabled="submitted">
          <span v-if="!submitted">Kirim Data</span>
          <span v-else>✓ Terkirim</span>
        </button>
      </div>
    </form>

    <div v-if="submitted" class="success-banner">
      <div class="success-icon">✅</div>
      <div class="success-text">
        <strong>Terima kasih!</strong> Data Anda telah berhasil dikirim. Kontribusi Anda sangat membantu pengembangan kualitas pendidikan di SMK Bahrul Ulum Surabaya.
      </div>
    </div>

    <div class="info-banner">
      <div class="info-icon">📊</div>
      <div class="info-text">
        <strong>Mengapa Tracer Study?</strong> Data alumni membantu sekolah mengevaluasi kurikulum, meningkatkan mutu pembelajaran, dan memperkuat link & match dengan dunia kerja. Partisipasi Anda sangat berharga.
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed } from "vue";

const submitted = ref(false);

const stats = ref({
  total: 1250,
  working: 42,
  college: 38,
  internship: 5,
  entrepreneur: 3,
});

const alumniNames = [
  "Abdilah Muhajir", "Abdul Adhim", "Abdur Rohman Hanif Rifai", "Achmad Abid Syahputra",
  "Ahmad Fauzi", "Ahmad Husain Romadhon", "Ainur Rakhmah", "Aisyah Imamatus Sholihah",
  "Aldo Roy Saputra", "Alfa Bintang Samudra", "Alvianatun Nekmah", "Amilatus Sholihah",
  "Anastasya Putria Dewi", "Andi Ghofur Syaroni", "Angellyla Trahsia", "Anis Fitria",
  "Anisa Arofatul Jannah", "Aprilia Dwi Sekar A", "Aretha Dwi Jasika", "Ari Kurniawan",
  "Aulia Rahmawati", "Avan Jahrur Rozi", "Bagas Maulana Saputra", "Bastian Oktaf Tri Setya",
  "Calvin Candra Wijaya", "Camellia Syadza S", "Chelsea Bunga Lestari", "Citra Anggun Pratiwi",
  "Danuar Dwi Aryanto", "Devi Anggraini", "Dewi Mawar Dani", "Dito Wiguna",
  "Dwi Galuh Nur Azizah", "Eka Mohammad Soleh", "Elisa Elly Julia Nurfaida", "Fadli Alif Akbar",
  "Faizah Fanya Salsabilah", "Ferdi Setiyawan", "Fika Aulia Azzahratun", "Fitri Mariani",
  "Galuh Bagas Saputra", "Hidrotul Rohmah", "Ibnu Raka Andika", "Ilham Firmasyah",
  "Ima Aulia Maghviroh", "Jamila Javier Firdaus", "Khusnul Khotimah", "Kurnia Kurniawan",
];

const currentYear = new Date().getFullYear();
const years = computed(() => {
  const arr = [];
  for (let y = currentYear; y >= currentYear - 30; y--) arr.push(y);
  return arr;
});

const form = ref({
  nama: "",
  email: "",
  nik: "",
  jenisKelamin: "",
  tempatLahir: "",
  tanggalLahir: "",
  tahunLulus: "",
  noHp: "",
  pendidikanTerakhir: "",
  status: "",
  namaUniversitas: "",
  tahunMasuk: "",
  namaPerusahaan: "",
  bidang: "",
  jabatan: "",
  alamatPerusahaan: "",
  kecamatan: "",
  kelurahan: "",
  rtRw: "",
  alamatDomisili: "",
  pesan: "",
});

function submitForm() {
  submitted.value = true;
  window.scrollTo({ top: 0, behavior: "smooth" });
}

function goBack() {
  window.history.back();
}
</script>

<style scoped>
.tracer-page {
  padding: 80px 7%;
  min-height: 100vh;
  min-height: 100dvh;
  background: #eef4ec;
  color: #1c2a23;
}

.top-bar {
  margin-bottom: 20px;
}

.back-button {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 12px 18px;
  border: 1px solid rgba(47, 91, 58, 0.16);
  background: #ffffff;
  color: #2f5b45;
  border-radius: 18px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}

.back-button:hover {
  background: rgba(58, 100, 80, 0.08);
  transform: translateY(-1px);
}

.back-icon {
  font-size: 18px;
  line-height: 1;
}

.page-label {
  display: inline-flex;
  padding: 10px 16px;
  border-radius: 999px;
  background: rgba(58, 100, 80, 0.14);
  color: #2f5b45;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.12em;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 24px;
  margin-bottom: 28px;
}

.page-header h1 {
  margin: 16px 0 10px;
  font-size: clamp(32px, 4vw, 48px);
  line-height: 1.05;
  font-weight: 800;
}

.page-header p {
  max-width: 640px;
  color: #4e6456;
  line-height: 1.8;
}

.action-panel {
  display: grid;
  gap: 12px;
}

.summary-pill {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  min-width: 160px;
  padding: 18px 20px;
  border-radius: 20px;
  background: #ffffff;
  border: 1px solid rgba(58, 100, 80, 0.12);
  box-shadow: 0 12px 24px rgba(35, 55, 42, 0.06);
}

.summary-pill strong {
  font-size: 28px;
  font-weight: 800;
  color: #3a6450;
}

.summary-pill span {
  color: #6c7f6f;
  font-size: 13px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-bottom: 40px;
}

.stat-card {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 20px;
  border-radius: 18px;
  background: #fff;
  border: 1px solid rgba(58, 100, 80, 0.1);
  box-shadow: 0 8px 20px rgba(35, 55, 42, 0.04);
}

.stat-icon {
  width: 56px;
  height: 56px;
  border-radius: 16px;
  display: grid;
  place-items: center;
  font-size: 28px;
  flex-shrink: 0;
}

.stat-info strong {
  display: block;
  font-size: 24px;
  font-weight: 800;
  color: #1a2620;
}

.stat-info span {
  font-size: 13px;
  color: #6c7f6f;
}

.section-title {
  margin-bottom: 24px;
}

.section-title h2 {
  margin: 0 0 6px;
  font-size: 24px;
  font-weight: 800;
  color: #1a2620;
}

.section-title p {
  margin: 0;
  color: #6c7f6f;
  font-size: 14px;
}

.tracer-form {
  max-width: 900px;
}

.form-section {
  margin-bottom: 32px;
  padding: 28px;
  border-radius: 20px;
  background: #fff;
  border: 1px solid rgba(58, 100, 80, 0.1);
  box-shadow: 0 8px 24px rgba(35, 55, 42, 0.04);
}

.form-section h3 {
  margin: 0 0 20px;
  font-size: 18px;
  font-weight: 800;
  color: #1a2620;
  padding-bottom: 12px;
  border-bottom: 1px solid rgba(58, 100, 80, 0.08);
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group.full {
  grid-column: 1 / -1;
}

.form-group label {
  font-size: 13px;
  font-weight: 700;
  color: #3d4d41;
}

.form-group input,
.form-group select,
.form-group textarea {
  padding: 14px 16px;
  border: 1px solid rgba(58, 100, 80, 0.18);
  border-radius: 14px;
  background: #f9fbf8;
  font-size: 14px;
  color: #1c2a23;
  outline: none;
  transition: border-color 0.2s, background 0.2s;
  font-family: inherit;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  border-color: #3a6450;
  background: #fff;
}

.form-group textarea {
  resize: vertical;
}

.radio-group {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
}

.radio-label {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 14px;
  color: #3d4d41;
  cursor: pointer;
}

.radio-label input[type="radio"] {
  accent-color: #3a6450;
  width: 18px;
  height: 18px;
}

.form-actions {
  margin-top: 8px;
}

.btn-submit {
  padding: 16px 40px;
  border: none;
  border-radius: 16px;
  background: #3a6450;
  color: #fff;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 10px 24px rgba(58, 100, 80, 0.2);
}

.btn-submit:hover:not(:disabled) {
  background: #2f5b45;
  transform: translateY(-2px);
  box-shadow: 0 14px 30px rgba(58, 100, 80, 0.25);
}

.btn-submit:disabled {
  background: #6c7f6f;
  cursor: default;
}

.success-banner {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  margin-top: 24px;
  padding: 20px 24px;
  border-radius: 16px;
  background: linear-gradient(135deg, #eafaf1, #f0faf4);
  border: 1px solid rgba(46, 204, 113, 0.2);
}

.success-icon {
  font-size: 28px;
  flex-shrink: 0;
}

.success-text {
  font-size: 14px;
  color: #1a6b3c;
  line-height: 1.6;
}

.success-text strong {
  color: #145a30;
}

.info-banner {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  margin-top: 24px;
  padding: 18px 22px;
  border-radius: 16px;
  background: linear-gradient(135deg, #eef7ee, #f3fbf5);
  border: 1px solid rgba(58, 100, 80, 0.12);
}

.info-icon {
  font-size: 24px;
  flex-shrink: 0;
}

.info-text {
  font-size: 13.5px;
  color: #3d4d41;
  line-height: 1.6;
}

.info-text strong {
  color: #1a2620;
}

@media (max-width: 1024px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .tracer-page {
    padding: 70px 5%;
  }

  .page-header {
    flex-direction: column;
  }

  .stats-grid {
    grid-template-columns: 1fr 1fr;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .radio-group {
    flex-direction: column;
    gap: 10px;
  }
}

@media (max-width: 520px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
}
</style>
