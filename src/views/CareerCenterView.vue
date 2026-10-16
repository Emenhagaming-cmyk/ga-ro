<template>
  <div class="career-layout">
    <header class="career-hero">
      <div class="career-hero-text">
        <h1>Career Center</h1>
        <p>Temukan lowongan, lamar, dan pantau progres karirmu — dikelola BKK SMK Bahrul Ulum.</p>
      </div>
    </header>

    <nav class="career-nav" aria-label="Menu Career Center">
      <router-link
        v-for="tab in tabs"
        :key="tab.to"
        :to="tab.to"
        class="nav-item"
      >
        <i class="fas" :class="tab.icon"></i>
        <span>{{ tab.label }}</span>
      </router-link>
    </nav>

    <div class="career-content">
      <RouterView />
    </div>
  </div>
</template>

<script setup>
const tabs = [
  { to: '/career-center/search', label: 'Cari Lowongan', icon: 'fa-magnifying-glass' },
  { to: '/career-center/bookmark', label: 'Tersimpan', icon: 'fa-bookmark' },
  { to: '/career-center/dashboard', label: 'Dashboard', icon: 'fa-gauge-high' },
  { to: '/career-center/applications', label: 'Lamaran Saya', icon: 'fa-file-lines' },
  { to: '/career-center/messages', label: 'Pesan', icon: 'fa-envelope' },
  { to: '/career-center/statistics', label: 'Statistik', icon: 'fa-chart-simple' },
]
</script>

<style scoped>
.career-layout {
  min-height: calc(100vh - 100px);
}

.career-hero {
  padding: 8px 0 18px;
}

.career-hero h1 {
  font-size: 26px;
  font-weight: 800;
  color: var(--text, #1c2a23);
  margin: 0 0 6px;
}

.career-hero p {
  font-size: 13.5px;
  color: var(--text-secondary, #647067);
  margin: 0;
}

.career-nav {
  display: flex;
  gap: 6px;
  padding: 8px;
  background: var(--surface, #fff);
  border: 1px solid var(--border, #e5e7e6);
  border-radius: var(--radius, 16px);
  overflow-x: auto;
  scrollbar-width: none;
  position: sticky;
  top: 12px;
  z-index: 100;
  box-shadow: var(--shadow-sm);
}

.career-nav::-webkit-scrollbar {
  display: none;
}

.nav-item {
  flex: 1;
  justify-content: center;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  border-radius: var(--radius-sm, 10px);
  text-decoration: none;
  font-size: 13px;
  font-weight: 700;
  color: var(--text-secondary, #647067);
  white-space: nowrap;
  transition: color 0.2s, background 0.2s;
}

.nav-item i {
  font-size: 12px;
}

.nav-item:hover {
  color: var(--primary, #3a6450);
  background: var(--primary-light, rgba(58, 100, 80, 0.06));
}

.nav-item.router-link-active {
  color: #fff;
  background: var(--primary, #3a6450);
}

.career-content {
  padding-top: 20px;
}

@media (max-width: 600px) {
  .career-hero h1 {
    font-size: 22px;
  }

  .career-nav {
    position: static;
  }

  .nav-item {
    flex: 0 0 auto;
    padding: 9px 13px;
    font-size: 12.5px;
  }
}
</style>
