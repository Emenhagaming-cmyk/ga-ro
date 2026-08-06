<script setup>
// Foto sekolah dari folder public
const schoolImage = "/sklh.jpg";
</script>

<template>
  <section class="about-school" id="tentang">
    <div class="container">
      <div class="grid-layout">
        <div class="big-card">
          <div class="big-card-content">
            <span class="section-label">Tentang Sekolah</span>
            <h2>Sejarah & Tentang Sekolah Kami</h2>
            <p>
              SMK Bahrul Ulum berdiri sejak 1998 dengan visi mencetak generasi
              muda yang kompeten, kreatif, dan berwawasan teknologi. Kami fokus
              pada pendidikan vokasi yang menggabungkan pengetahuan akademis
              dengan pengalaman praktik langsung.
            </p>
            <p>
              Sejak awal, sekolah ini berkembang dari sebuah lembaga kecil
              menjadi pusat pendidikan kejuruan yang dipercaya oleh masyarakat.
              Perjalanan kami dilandasi nilai disiplin, kerja sama, dan inovasi,
              sehingga setiap lulusan siap menyongsong dunia kerja dan studi
              lanjut.
            </p>
          </div>
          <div class="big-card-image">
            <div class="image-wrapper">
              <img
                :src="schoolImage"
                alt="SMK Bahrul Ulum"
                class="school-image"
              />
              <div class="image-overlay"></div>
            </div>
          </div>
        </div>

        <div class="stats-card">
          <div class="stats-row">
            <div class="stat-item">
              <div class="stat-value">1.280</div>
              <div class="stat-label">Siswa Aktif</div>
            </div>
            <div class="stat-item">
              <div class="stat-value">7</div>
              <div class="stat-label">Jurusan Saat Ini</div>
            </div>
            <div class="stat-item">
              <div class="stat-value">24</div>
              <div class="stat-label">Program Keahlian</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.about-school {
  position: relative;
  padding: 56px 7%;
  background:
    radial-gradient(
      circle at bottom right,
      rgba(125, 184, 141, 0.15),
      transparent 35%
    ),
    linear-gradient(180deg, #f7f9f4 0%, #eef4eb 50%, #f7f9f4 100%);
  overflow: hidden;
}

.about-school::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(58, 100, 80, 0.12),
    transparent
  );
}

.container {
  position: relative;
  max-width: 1320px;
  margin: 0 auto;
}

.grid-layout {
  display: grid;
  grid-template-rows: auto auto;
  gap: 28px;
}

.big-card {
  display: grid;
  grid-template-columns: 1.2fr 0.8fr;
  gap: 24px;
  padding: 26px;
  border-radius: 22px;
  background: #ffffff;
  box-shadow: 0 16px 40px rgba(35, 55, 42, 0.1);
}

.big-card-content {
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
}

.section-label {
  display: inline-block;
  padding: 7px 14px;
  border-radius: 999px;
  background: rgba(125, 184, 141, 0.18);
  color: #2f5b45;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  margin-bottom: 12px;
}

h2 {
  margin: 0 0 12px;
  color: #13231c;
  font-size: clamp(24px, 2.8vw, 34px);
  line-height: 1.15;
  letter-spacing: -0.03em;
  font-weight: 800;
}

.big-card-content p {
  color: #55655a;
  font-size: 14px;
  line-height: 1.7;
  margin-bottom: 10px;
}

.big-card-image {
  display: flex;
  align-items: flex-start;
}

.image-wrapper {
  position: relative;
  width: 100%;
  height: 300px;
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 12px 28px rgba(35, 55, 42, 0.12);
}

.school-image {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.image-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    135deg,
    rgba(125, 184, 141, 0.16) 0%,
    rgba(58, 100, 80, 0.04) 100%
  );
  pointer-events: none;
}

.stats-card {
  padding: 18px 22px;
  border-radius: 20px;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), #f3f7f0);
  border: 1px solid rgba(125, 184, 141, 0.18);
  box-shadow: 0 14px 34px rgba(35, 55, 42, 0.1);
}

.stats-row {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 14px;
  align-items: stretch;
}

.stat-item {
  padding: 14px 14px;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.95);
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 6px;
  box-shadow: inset 0 0 0 1px rgba(125, 184, 141, 0.08);
}

.stat-value {
  color: #21412d;
  font-size: 26px;
  font-weight: 800;
  letter-spacing: -0.03em;
}

.stat-label {
  color: #5d7666;
  font-size: 12.5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
}

@media (max-width: 1024px) {
  .about-school {
    padding: 48px 7%;
  }

  .big-card {
    grid-template-columns: 1fr;
    padding: 22px;
  }

  .grid-layout {
    gap: 16px;
  }
}

@media (max-width: 768px) {
  .about-school {
    padding: 40px 20px;
  }

  .big-card {
    padding: 18px;
  }

  h2 {
    font-size: clamp(22px, 5.5vw, 28px);
  }

  .big-card-content p {
    font-size: 13.5px;
  }

  .stats-card {
    padding: 16px 14px;
  }

  .stats-row {
    display: grid;
    grid-template-columns: 1fr;
    gap: 10px;
  }

  .stat-item {
    padding: 12px;
  }
}
</style>
