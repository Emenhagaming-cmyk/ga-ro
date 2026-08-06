<template>
  <section class="career-page">
    <div class="top-bar">
      <button type="button" class="back-button" @click="goBack">
        <span class="back-icon"><</span>
      </button>
    </div>

    <div class="page-header">
      <div>
        <span class="page-label">Career Center</span>
        <h1>Kesempatan Magang &amp; Lowongan untuk Siswa</h1>
        <p>
          Temukan perusahaan dan program yang sedang mencari siswa SMK untuk
          magang, kerja, dan praktek industri.
        </p>
      </div>
      <div class="header-actions">
        <div class="action-panel">
          <div class="summary-pill">
            <strong>12</strong>
            <span>Lowongan terbaru</span>
          </div>
          <div class="summary-pill">
            <strong>8</strong>
            <span>Perusahaan aktif</span>
          </div>
        </div>
      </div>
    </div>

    <div class="search-row">
      <input type="text" placeholder="Cari perusahaan, jurusan, atau lokasi" />
      <button>Filter</button>
    </div>

    <div class="company-list">
      <article class="company-card featured">
        <div class="company-card-top">
          <div>
            <span class="company-label">Magang</span>
            <h2>PT Bina Mandiri</h2>
            <p>Magang Admin Support / Front Office</p>
          </div>
          <span class="company-badge">Terbuka</span>
        </div>
        <div class="company-details">
          <div>
            <strong>Lokasi</strong>
            <span>Surabaya</span>
          </div>
          <div>
            <strong>Durasi</strong>
            <span>3 bulan</span>
          </div>
          <div>
            <strong>Jurusan</strong>
            <span>AKL / Multimedia</span>
          </div>
        </div>
        <div class="company-meta">
          <span>Deadline: 20 Agustus 2026</span>
          <a href="#">Lihat detail</a>
        </div>
      </article>

      <article class="company-card">
        <div class="company-card-top">
          <div>
            <span class="company-label">Kerja</span>
            <h2>CV Digital Nusantara</h2>
            <p>Posisi Teknis Jaringan &amp; Support</p>
          </div>
          <span class="company-badge badge-secondary">Terbuka</span>
        </div>
        <div class="company-details">
          <div>
            <strong>Lokasi</strong>
            <span>Surabaya</span>
          </div>
          <div>
            <strong>Shift</strong>
            <span>Senin-Jumat</span>
          </div>
          <div>
            <strong>Jurusan</strong>
            <span>TKJ / RPL</span>
          </div>
        </div>
        <div class="company-meta">
          <span>Deadline: 26 Agustus 2026</span>
          <a href="#">Lihat detail</a>
        </div>
      </article>

      <article class="company-card">
        <div class="company-card-top">
          <div>
            <span class="company-label">BKK</span>
            <h2>PT Sinar Maju</h2>
            <p>Program magang produksi &amp; quality control</p>
          </div>
          <span class="company-badge badge-secondary">Terbuka</span>
        </div>
        <div class="company-details">
          <div>
            <strong>Lokasi</strong>
            <span>Sidoarjo</span>
          </div>
          <div>
            <strong>Durasi</strong>
            <span>4 bulan</span>
          </div>
          <div>
            <strong>Jurusan</strong>
            <span>TKJ / AKL</span>
          </div>
        </div>
        <div class="company-meta">
          <span>Deadline: 28 Agustus 2026</span>
          <a href="#">Lihat detail</a>
        </div>
      </article>

      <article class="company-card">
        <div class="company-card-top">
          <div>
            <span class="company-label">Magang</span>
            <h2>SMK Bahrul Ulum</h2>
            <p>Program pelatihan kerja praktik dan mentoring guru</p>
          </div>
          <span class="company-badge badge-secondary">Terbuka</span>
        </div>
        <div class="company-details">
          <div>
            <strong>Lokasi</strong>
            <span>Surabaya</span>
          </div>
          <div>
            <strong>Durasi</strong>
            <span>2 bulan</span>
          </div>
          <div>
            <strong>Jurusan</strong>
            <span>Semua jurusan</span>
          </div>
        </div>
        <div class="company-meta">
          <span>Deadline: 15 September 2026</span>
          <a href="#">Lihat detail</a>
        </div>
      </article>
    </div>
  </section>
</template>

<script setup>
const goBack = () => window.history.back();
</script>

<style scoped>
.career-page {
  padding: 80px 7%;
  min-height: 100vh;
  background: #eef4ec;
  color: #1c2a23;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 24px;
  margin-bottom: 28px;
}

.top-bar {
  display: flex;
  justify-content: flex-start;
  margin-bottom: 24px;
}

.header-actions {
  display: flex;
  justify-content: flex-end;
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
  transition:
    background 0.2s ease,
    transform 0.2s ease;
}

.back-button:hover {
  background: rgba(58, 100, 80, 0.08);
  transform: translateY(-1px);
}

.back-icon {
  display: inline-flex;
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

.page-header h1 {
  margin: 16px 0 10px;
  font-size: clamp(36px, 4vw, 52px);
  line-height: 1.05;
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
  min-width: 180px;
  padding: 18px 20px;
  border-radius: 20px;
  background: #ffffff;
  border: 1px solid rgba(58, 100, 80, 0.12);
  box-shadow: 0 12px 24px rgba(35, 55, 42, 0.06);
}

.summary-pill strong {
  font-size: 28px;
  font-weight: 800;
}

.summary-pill span {
  color: #6c7f6f;
  font-size: 13px;
}

.search-row {
  display: flex;
  gap: 12px;
  margin-bottom: 32px;
}

.search-row input {
  flex: 1;
  padding: 16px 20px;
  border: 1px solid rgba(58, 100, 80, 0.18);
  border-radius: 18px;
  background: #ffffff;
  font-size: 14px;
  color: #1c2a23;
}

.search-row button {
  padding: 16px 24px;
  border: none;
  border-radius: 18px;
  background: #2f5b45;
  color: #ffffff;
  font-weight: 700;
  cursor: pointer;
}

.company-list {
  display: grid;
  gap: 20px;
}

.company-card {
  padding: 24px;
  border-radius: 24px;
  background: #ffffff;
  border: 1px solid rgba(58, 100, 80, 0.12);
  box-shadow: 0 18px 40px rgba(35, 55, 42, 0.06);
  transition:
    transform 0.25s ease,
    box-shadow 0.25s ease;
}

.company-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 24px 50px rgba(35, 55, 42, 0.1);
}

.company-card.featured {
  border-color: rgba(58, 100, 80, 0.2);
  background: linear-gradient(180deg, #f3fbf5 0%, #ffffff 100%);
}

.company-card-top {
  display: flex;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 18px;
}

.company-label {
  display: inline-flex;
  padding: 8px 14px;
  border-radius: 999px;
  background: rgba(47, 91, 58, 0.14);
  color: #2f5b45;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
}

.company-card-top h2 {
  margin: 6px 0 6px;
  font-size: 22px;
}

.company-card-top p {
  margin: 0;
  color: #5d6e5f;
  line-height: 1.6;
}

.company-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 10px 14px;
  border-radius: 999px;
  background: rgba(47, 91, 58, 0.12);
  color: #2f5b45;
  font-size: 12px;
  font-weight: 700;
}

.badge-secondary {
  background: rgba(95, 113, 90, 0.1);
}

.company-details {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 16px;
  margin-bottom: 18px;
}

.company-details div {
  padding: 16px;
  border-radius: 18px;
  background: #f7fbf6;
}

.company-details strong {
  display: block;
  font-size: 12px;
  color: #5a6c5e;
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.company-details span {
  color: #1c2a23;
  font-weight: 700;
}

.company-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
}

.company-meta span {
  color: #6c7f6f;
  font-size: 14px;
}

.company-meta a {
  color: #2f5b45;
  font-weight: 700;
  text-decoration: none;
}

@media (max-width: 980px) {
  .career-page {
    padding: 70px 6%;
  }

  .page-header,
  .company-meta {
    flex-direction: column;
    align-items: stretch;
  }

  .company-details {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 700px) {
  .page-header {
    gap: 16px;
  }

  .search-row {
    flex-direction: column;
  }

  .search-row button {
    width: 100%;
  }
}
</style>
