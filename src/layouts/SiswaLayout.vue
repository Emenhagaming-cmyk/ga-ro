<template>
  <div class="siswa-layout">
    <div class="siswa-backdrop" :class="{ open: sidebarOpen }" @click="sidebarOpen = false" />

    <aside class="siswa-sidebar" :class="{ open: sidebarOpen }">
      <div class="sidebar-brand">
        <img src="/logo.webp" alt="Logo" />
        <div>
          <span class="brand-name">Panel Siswa</span>
          <span class="brand-sub">SMK Bahrul Ulum</span>
        </div>
      </div>

      <nav class="sidebar-nav">
        <router-link
          to="/dashboard-siswa"
          class="sidebar-link"
          :class="{ active: isActive('/dashboard-siswa') }"
          @click="sidebarOpen = false"
        >
          <LayoutDashboard :size="18" :stroke-width="2.2" />
          <span>Status Pendaftaran</span>
        </router-link>

        <router-link
          to="/tabungan"
          class="sidebar-link"
          :class="{ active: isActive('/tabungan') }"
          @click="sidebarOpen = false"
        >
          <Wallet :size="18" :stroke-width="2.2" />
          <span>Tabungan</span>
        </router-link>

        <router-link
          to="/spp"
          class="sidebar-link"
          :class="{ active: isActive('/spp') }"
          @click="sidebarOpen = false"
        >
          <Receipt :size="18" :stroke-width="2.2" />
          <span>SPP</span>
        </router-link>

        <router-link
          to="/koperasi"
          class="sidebar-link"
          :class="{ active: isActive('/koperasi') }"
          @click="sidebarOpen = false"
        >
          <ShoppingBag :size="18" :stroke-width="2.2" />
          <span>Koperasi</span>
        </router-link>

        <router-link
          to="/koperasi-riwayat"
          class="sidebar-link"
          :class="{ active: isActive('/koperasi-riwayat') }"
          @click="sidebarOpen = false"
        >
          <History :size="18" :stroke-width="2.2" />
          <span>Riwayat Koperasi</span>
        </router-link>

        <button
          class="sidebar-link sidebar-group"
          :class="{ active: isActive('/career-center'), open: careerOpen }"
          type="button"
          @click="careerOpen = !careerOpen"
        >
          <Briefcase :size="18" :stroke-width="2.2" />
          <span>Career Center</span>
          <ChevronDown :size="15" class="chev" />
        </button>
        <div v-show="careerOpen" class="sidebar-sub">
          <router-link
            v-for="c in careerMenu"
            :key="c.path"
            :to="c.path"
            class="sidebar-sublink"
            :class="{ active: route.path === c.path }"
            @click="sidebarOpen = false"
          >{{ c.label }}</router-link>
        </div>

        <div class="sidebar-divider" />

        <router-link
          to="/profil"
          class="sidebar-link"
          :class="{ active: isActive('/profil') }"
          @click="sidebarOpen = false"
        >
          <User :size="18" :stroke-width="2.2" />
          <span>Profil</span>
        </router-link>
        <a v-if="isDiterima" :href="backendPagePath('/pendaftaran/bukti')" class="sidebar-link">
          <Download :size="18" :stroke-width="2.2" />
          <span>Bukti Diterima</span>
        </a>
      </nav>

      <div class="sidebar-footer">
        <router-link to="/" class="sidebar-link" @click="sidebarOpen = false">
          <ChevronLeft :size="18" :stroke-width="2.2" />
          <span>Kembali ke Beranda</span>
        </router-link>
      </div>
    </aside>

    <button
      class="sidebar-fab"
      :class="{ 'fab-hidden': sidebarOpen }"
      type="button"
      aria-label="Buka menu siswa"
      @click="sidebarOpen = true"
    >
      <Menu :size="20" :stroke-width="2.4" />
    </button>

    <main class="siswa-main">
      <RouterView />
    </main>
  </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { useRoute } from "vue-router";
import {
  LayoutDashboard,
  Wallet,
  Receipt,
  ShoppingBag,
  History,
  Briefcase,
  User,
  Download,
  ChevronLeft,
  ChevronDown,
  Menu,
} from "lucide-vue-next";
import { useAuthSession } from "@/composable/useAuthSession";

const route = useRoute();
const { session, BACKEND, backendPagePath } = useAuthSession();

const sidebarOpen = ref(false);
const careerOpen = ref(route.path.startsWith("/career-center"));

const careerMenu = [
  { path: "/career-center/dashboard", label: "Dashboard" },
  { path: "/career-center/search", label: "Cari Lowongan" },
  { path: "/career-center/bookmark", label: "Lowongan Tersimpan" },
  { path: "/career-center/applications", label: "Lamaran Saya" },
  { path: "/career-center/messages", label: "Pesan" },
  { path: "/career-center/statistics", label: "Statistik" },
];

const isDiterima = computed(() => session.value.status === "diterima");
const isActive = (p) => route.path === p || route.path.startsWith(p + "/");

watch(
  () => route.path,
  (p) => {
    if (p.startsWith("/career-center")) careerOpen.value = true;
  }
);
</script>

<style scoped>
.siswa-layout {
  min-height: 100vh;
  background: #f6f8f4;
}

/* ---------- Sidebar ---------- */
.siswa-sidebar {
  width: 260px;
  background: #1c2a23;
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
  overflow-y: auto;
}
.sidebar-nav::-webkit-scrollbar { width: 5px; }
.sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.15); border-radius: 999px; }

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
  background: none;
  border: none;
  cursor: pointer;
  font-family: inherit;
  text-align: left;
  transition: background 0.2s ease, color 0.2s ease;
}
.sidebar-link:hover { background: rgba(255, 255, 255, 0.08); color: #fff; }
.sidebar-link.active {
  background: #3a6450;
  color: #fff;
  box-shadow: inset 3px 0 0 #7db88d;
}

.sidebar-group span { flex: 1; }
.sidebar-group .chev { transition: transform 0.2s ease; }
.sidebar-group.open .chev { transform: rotate(180deg); }

.sidebar-sub {
  display: flex;
  flex-direction: column;
  gap: 2px;
  padding: 2px 0 2px 30px;
}
.sidebar-sublink {
  padding: 8px 12px;
  border-radius: 10px;
  color: rgba(255, 255, 255, 0.62);
  text-decoration: none;
  font-size: 13px;
  font-weight: 600;
  transition: background 0.2s ease, color 0.2s ease;
}
.sidebar-sublink:hover { color: #fff; background: rgba(255, 255, 255, 0.06); }
.sidebar-sublink.active { color: #fff; background: rgba(255, 255, 255, 0.14); }

.sidebar-divider {
  height: 1px;
  background: rgba(255, 255, 255, 0.12);
  margin: 10px 14px;
}

.sidebar-footer {
  padding: 16px 14px;
  border-top: 1px solid rgba(255, 255, 255, 0.12);
}

/* ---------- Area konten ---------- */
.siswa-main {
  margin-left: 260px;
  min-height: 100vh;
  box-sizing: border-box;
  /* KUNCI UTAMA: padding sisi yang menjaga konten maks 1120px & tetap center di layar lebar.
     Ganti angka 1120 jika ingin semua halaman panel lebih lebar/sempit. */
  padding: 28px max(32px, calc((100% - 260px - 1120px) / 2)) 64px;
}

.sidebar-fab { display: none; }
.siswa-backdrop { display: none; }

/* ---------- Mobile ---------- */
@media (max-width: 900px) {
  .siswa-sidebar { transform: translateX(-100%); }
  .siswa-sidebar.open { transform: translateX(0); }

  /* padding bawah besar agar konten tidak tertutup FAB */
  .siswa-main { margin-left: 0; padding: 18px 16px 110px; }

  .sidebar-fab {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    position: fixed;
    left: 16px;
    bottom: 20px;
    z-index: 210;
    width: 48px;
    height: 48px;
    border: none;
    border-radius: 50%;
    background: #3a6450;
    color: #fff;
    cursor: pointer;
    box-shadow: 0 10px 24px rgba(28, 42, 35, 0.28);
    transition: opacity 0.2s ease, transform 0.2s ease;
  }
  .sidebar-fab.fab-hidden {
    opacity: 0;
    pointer-events: none;
    transform: scale(0.85);
  }

  .siswa-backdrop {
    display: block;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.4);
    z-index: 150;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
  }
  .siswa-backdrop.open {
    opacity: 1;
    pointer-events: auto;
  }
}
</style>
