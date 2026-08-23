<template>
  <div class="placeholder-page">
    <div class="stat-cards">
      <div class="stat-card">
        <i class="fas fa-briefcase"></i>
        <div class="stat-info">
          <span class="stat-number">12</span>
          <span class="stat-label">Lowongan Aktif</span>
        </div>
      </div>
      <div class="stat-card">
        <i class="fas fa-file-lines"></i>
        <div class="stat-info">
          <span class="stat-number">5</span>
          <span class="stat-label">Lamaran Masuk</span>
        </div>
      </div>
      <div class="stat-card">
        <i class="fas fa-building"></i>
        <div class="stat-info">
          <span class="stat-number">8</span>
          <span class="stat-label">Mitra Perusahaan</span>
        </div>
      </div>
      <div class="stat-card">
        <i class="fas fa-check-circle"></i>
        <div class="stat-info">
          <span class="stat-number">3</span>
          <span class="stat-label">Diterima</span>
        </div>
      </div>
    </div>
    <div class="placeholder-content">
      <i class="fas fa-gauge-high"></i>
      <h2>Dashboard Career Center</h2>
      <p>Ringkasan aktivitas lowongan, lamaran, dan statistik karir siswa.</p>
    </div>
  </div>
</template>

<style scoped>
.stat-cards {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-bottom: 32px;
}

.stat-card {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 20px;
  background: #fff;
  border-radius: var(--radius-lg, 20px);
  border: 1px solid var(--border, #dfe4dd);
  box-shadow: var(--shadow-sm);
}

.stat-card i {
  width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: var(--primary-light, #e8f0e6);
  color: var(--primary, #3a6450);
  font-size: 18px;
}

.stat-number {
  display: block;
  font-size: 22px;
  font-weight: 800;
  color: var(--text, #1c2a23);
}

.stat-label {
  font-size: 12px;
  color: var(--text-secondary, #647067);
}

.placeholder-content {
  text-align: center;
  padding: 60px 20px;
  color: var(--text-secondary, #647067);
}

.placeholder-content i {
  font-size: 48px;
  margin-bottom: 16px;
  opacity: 0.2;
}

.placeholder-content h2 {
  font-size: 20px;
  color: var(--text, #1c2a23);
  margin-bottom: 8px;
}

.placeholder-content p {
  font-size: 14px;
}

@media (max-width: 768px) {
  .stat-cards {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>
