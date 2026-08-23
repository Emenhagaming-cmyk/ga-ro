<template>
  <div class="career-layout">
    <div class="career-backdrop" :class="{ open: sidebarOpen }" @click="sidebarOpen = false" />

    <aside class="career-sidebar" :class="{ open: sidebarOpen }">
      <div class="sidebar-brand">
        <img src="/logo.png" alt="Logo" />
        <div>
          <span class="brand-name">Career Center</span>
          <span class="brand-sub">SMK Bahrul Ulum</span>
        </div>
      </div>

      <nav class="sidebar-nav">
        <router-link
          v-for="item in menuItems"
          :key="item.path"
          :to="item.path"
          class="sidebar-link"
          :class="{ active: $route.path === item.path }"
          @click="sidebarOpen = false"
        >
          <i :class="item.icon"></i>
          <span>{{ item.label }}</span>
        </router-link>
      </nav>

      <div class="sidebar-footer">
        <router-link to="/" class="sidebar-link footer-link">
          <i class="fas fa-arrow-left"></i>
          <span>Kembali ke Beranda</span>
        </router-link>
      </div>
    </aside>

    <div class="career-main">
      <header class="career-topbar">
        <button class="hamburger" @click="sidebarOpen = !sidebarOpen">
          <i class="fas fa-bars"></i>
        </button>
        <h1 class="topbar-title">{{ currentTitle }}</h1>
      </header>
      <div class="career-content">
        <router-view />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const sidebarOpen = ref(false)

const menuItems = [
  { path: '/career-center/dashboard', label: 'Dashboard', icon: 'fas fa-gauge-high' },
  { path: '/career-center/search', label: 'Cari Lowongan', icon: 'fas fa-magnifying-glass' },
  { path: '/career-center/applications', label: 'Lamaran Saya', icon: 'fas fa-file-lines' },
  { path: '/career-center/messages', label: 'Pesan', icon: 'fas fa-envelope' },
  { path: '/career-center/statistics', label: 'Statistik', icon: 'fas fa-chart-simple' },
  { path: '/career-center/news', label: 'Berita Karir', icon: 'fas fa-newspaper' },
]

const currentTitle = computed(() => {
  const item = menuItems.find(m => route.path.startsWith(m.path))
  return item?.label || 'Career Center'
})
</script>

<style scoped>
.career-layout {
  display: flex;
  min-height: 100vh;
  background: var(--background);
}

/* Sidebar */
.career-sidebar {
  width: 260px;
  background: var(--primary-dark);
  color: #fff;
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  z-index: 200;
  display: flex;
  flex-direction: column;
  transition: transform 0.3s ease;
}

.sidebar-brand {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 24px 20px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.12);
}

.sidebar-brand img {
  height: 40px;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.9);
  padding: 3px;
}

.brand-name {
  font-size: 16px;
  font-weight: 800;
  color: #fff;
}

.brand-sub {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.6);
  display: block;
  margin-top: 2px;
}

.sidebar-nav {
  flex: 1;
  padding: 18px 14px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.sidebar-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 11px 14px;
  border-radius: 12px;
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  font-size: 14px;
  font-weight: 700;
  transition: all 0.2s ease;
}

.sidebar-link:hover {
  background: rgba(255, 255, 255, 0.08);
  color: #fff;
}

.sidebar-link.active {
  background: #fff;
  color: var(--primary);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.sidebar-footer {
  padding: 16px 14px;
  border-top: 1px solid rgba(255, 255, 255, 0.12);
}

/* Main content */
.career-main {
  flex: 1;
  margin-left: 260px;
  min-width: 0;
  display: flex;
  flex-direction: column;
}

.career-topbar {
  position: sticky;
  top: 0;
  z-index: 100;
  background: rgba(255, 255, 255, 0.96);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid var(--border);
  padding: 0 32px;
  height: 64px;
  display: flex;
  align-items: center;
  gap: 16px;
}

.hamburger {
  display: none;
  background: var(--primary);
  color: #fff;
  border: none;
  border-radius: 10px;
  width: 40px;
  height: 40px;
  cursor: pointer;
  align-items: center;
  justify-content: center;
}

.topbar-title {
  font-size: 18px;
  font-weight: 800;
  color: var(--text);
}

.career-content {
  max-width: 1200px;
  width: 100%;
  margin: 0 auto;
  padding: 32px;
}

/* Backdrop */
.career-backdrop {
  display: none;
}

/* Mobile */
@media (max-width: 900px) {
  .career-sidebar {
    transform: translateX(-100%);
  }

  .career-sidebar.open {
    transform: translateX(0);
  }

  .career-main {
    margin-left: 0;
  }

  .hamburger {
    display: inline-flex;
  }

  .career-topbar {
    padding: 0 16px;
  }

  .career-content {
    padding: 24px 16px;
  }

  .career-backdrop {
    display: block;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.4);
    z-index: 150;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
  }

  .career-backdrop.open {
    opacity: 1;
    pointer-events: auto;
  }
}
</style>
