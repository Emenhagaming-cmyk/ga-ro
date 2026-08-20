<template>
  <header class="navbar" :class="{ shrink: scrolled }">
    <a href="#top" class="logo">
      <img src="/logo.png" alt="Logo Sekolah" class="logo-img" />

      <div>
        <h2>SMK Bahrul Ulum</h2>
        <span class="pp">Surabaya</span>
      </div>
    </a>

    <nav class="desktop-nav">
      <a href="#top">Beranda</a>
      <div class="nav-dropdown" ref="dropdownRef" @mouseenter="dropdownOpen = true" @mouseleave="dropdownOpen = false">
        <button class="dropdown-trigger" type="button">
          Layanan
          <svg class="chevron" :class="{ open: dropdownOpen }" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
        </button>
        <div class="dropdown-panel" :class="{ open: dropdownOpen }">
          <a v-if="!isSiswa" :href="spmbTarget()" class="dropdown-item">
            <span class="di-text">
              <span class="di-title">SPMB Online</span>
            </span>
          </a>
          <a href="/koperasi" class="dropdown-item" @click="guardSiswa">
            <span class="di-text">
              <span class="di-title">Koperasi</span>
            </span>
          </a>
          <a href="/produk-siswa" class="dropdown-item" @click="guardSiswa">
            <span class="di-text">
              <span class="di-title">Produk Siswa</span>
            </span>
          </a>
          <a href="/career-center" class="dropdown-item" @click="guardSiswa">
            <span class="di-text">
              <span class="di-title">Career Center</span>
            </span>
          </a>
        </div>
      </div>
      <div class="nav-dropdown" ref="informasiDropdownRef" @mouseenter="informasiDropdownOpen = true" @mouseleave="informasiDropdownOpen = false">
        <button class="dropdown-trigger" type="button">
          Informasi
          <svg class="chevron" :class="{ open: informasiDropdownOpen }" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
        </button>
        <div class="dropdown-panel" :class="{ open: informasiDropdownOpen }">
          <a href="/spmb-info" class="dropdown-item">
            <span class="di-text">
              <span class="di-title">Informasi Biaya</span>
            </span>
          </a>
          <a href="/berita" class="dropdown-item">
            <span class="di-text">
              <span class="di-title">Berita</span>
            </span>
          </a>
          <a href="/kelulusan" class="dropdown-item">
            <span class="di-text">
              <span class="di-title">Kelulusan</span>
            </span>
          </a>
          <a href="/e-learning" class="dropdown-item">
            <span class="di-text">
              <span class="di-title">E-Learning</span>
            </span>
          </a>
          <a href="/e-tracer" class="dropdown-item">
            <span class="di-text">
              <span class="di-title">E-Tracer Study</span>
            </span>
          </a>
        </div>
      </div>
      <div class="nav-dropdown" ref="tentangDropdownRef" @mouseenter="tentangDropdownOpen = true" @mouseleave="tentangDropdownOpen = false">
        <button class="dropdown-trigger" type="button">
          Tentang
          <svg class="chevron" :class="{ open: tentangDropdownOpen }" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
        </button>
        <div class="dropdown-panel" :class="{ open: tentangDropdownOpen }">
          <a href="#tentang" class="dropdown-item" @click="closeTentangDropdown">
            <span class="di-text">
              <span class="di-title">Profil Sekolah</span>
            </span>
          </a>
          <a href="#tentang" class="dropdown-item" @click="closeTentangDropdown">
            <span class="di-text">
              <span class="di-title">Visi & Misi</span>
            </span>
          </a>
          <a href="#tentang" class="dropdown-item" @click="closeTentangDropdown">
            <span class="di-text">
              <span class="di-title">Sejarah Sekolah</span>
            </span>
          </a>
        </div>
      </div>
      <a href="#contact">Kontak</a>
    </nav>

    <div class="nav-right">
      <a v-if="isSiswa" :href="BACKEND + '/profil'" class="nav-profile-link" :title="'Profil ' + session.name">
        <span class="nav-avatar">{{ initial }}</span>
        <span class="nav-profile-text">
          <span class="nav-profile-name">{{ session.name }}</span>
          <span class="nav-profile-role">Siswa</span>
        </span>
      </a>
      <button v-if="!isSiswa" class="ppdb" type="button" @click="goSPMB">SPMB</button>
    </div>

    <button
      class="menu"
      :class="{ active: menuOpen }"
      type="button"
      aria-label="Buka menu"
      :aria-expanded="menuOpen"
      @click="toggleMenu"
    >
      <span></span>
      <span></span>
      <span></span>
    </button>

    <Transition name="mobile-menu">
      <nav v-if="menuOpen" class="mobile-nav">
        <a href="#top" @click="closeMenu">Beranda</a>
        <div class="mobile-dropdown">
          <button class="mobile-dropdown-trigger" type="button" @click="layananOpen = !layananOpen">
            Layanan
            <svg class="chevron" :class="{ open: layananOpen }" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
          </button>
          <div class="mobile-dropdown-items" :class="{ open: layananOpen }">
            <a v-if="!isSiswa" :href="spmbTarget()" @click="closeMenu">SPMB Online</a>
            <a href="/koperasi" @click="guardSiswa(); closeMenu()">Koperasi</a>
            <a href="/produk-siswa" @click="guardSiswa(); closeMenu()">Produk Siswa</a>
            <a href="/career-center" @click="guardSiswa(); closeMenu()">Career Center</a>
          </div>
        </div>
        <div class="mobile-dropdown">
          <button class="mobile-dropdown-trigger" type="button" @click="informasiOpen = !informasiOpen">
            Informasi
            <svg class="chevron" :class="{ open: informasiOpen }" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
          </button>
          <div class="mobile-dropdown-items" :class="{ open: informasiOpen }">
            <a href="/berita" @click="closeMenu">Berita</a>
            <a href="/kelulusan" @click="closeMenu">Kelulusan</a>
            <a href="/e-learning" @click="closeMenu">E-Learning</a>
            <a href="/e-tracer" @click="closeMenu">E-Tracer Study</a>
          </div>
        </div>
        <div class="mobile-dropdown">
          <button class="mobile-dropdown-trigger" type="button" @click="tentangOpen = !tentangOpen">
            Tentang Sekolah
            <svg class="chevron" :class="{ open: tentangOpen }" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
          </button>
          <div class="mobile-dropdown-items" :class="{ open: tentangOpen }">
            <a href="#tentang" @click="closeMenu">Profil Sekolah</a>
            <a href="#tentang" @click="closeMenu">Visi & Misi</a>
            <a href="#tentang" @click="closeMenu">Sejarah Sekolah</a>
          </div>
        </div>
        <a href="#contact" @click="closeMenu">Kontak</a>
        <div class="mobile-bottom">
          <a v-if="isSiswa" :href="BACKEND + '/profil'" class="mobile-profile" @click="closeMenu">
            <span class="mobile-avatar">{{ initial }}</span>
            <span class="mobile-profile-text">
              <span class="mobile-profile-name">{{ session.name }}</span>
              <span class="mobile-profile-role">Siswa</span>
            </span>
          </a>
          <a v-if="!isSiswa" :href="spmbTarget()" class="mobile-ppdb" @click="closeMenu">SPMB</a>
        </div>
      </nav>
    </Transition>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useAuthSession } from "@/composable/useAuthSession";
import { useToast } from "@/composable/useToast";

const { session, spmbTarget, fetchStatus, BACKEND } = useAuthSession();
const { showToast } = useToast();

const scrolled = ref(false);
let scrollSentinel = null;
let scrollObserver = null;
const menuOpen = ref(false);
const dropdownOpen = ref(false);
const informasiDropdownOpen = ref(false);
const tentangDropdownOpen = ref(false);
const layananOpen = ref(false);
const informasiOpen = ref(false);
const tentangOpen = ref(false);
const dropdownRef = ref(null);
const informasiDropdownRef = ref(null);
const tentangDropdownRef = ref(null);

const isSiswa = computed(() => session.value.role === "siswa");
const initial = computed(() => (session.value.name || "?").trim().charAt(0).toUpperCase());

const toggleMenu = () => {
  menuOpen.value = !menuOpen.value;
};

const closeMenu = () => {
  menuOpen.value = false;
  layananOpen.value = false;
  informasiOpen.value = false;
  tentangOpen.value = false;
};

const closeDropdown = () => {
  dropdownOpen.value = false;
};

const closeTentangDropdown = () => {
  tentangDropdownOpen.value = false;
};

const goSPMB = () => {
  window.location.href = `${BACKEND}/login`;
  closeMenu();
};

const guardSiswa = async (event) => {
  if (session.value.role !== "siswa") {
    await fetchStatus();
    if (session.value.role !== "siswa") {
      event.preventDefault();
      showToast("Khusus siswa, silakan login terlebih dahulu");
    }
  }
};

const scrollToSection = (id) => {
  const target = document.getElementById(id);

  if (target) {
    target.scrollIntoView({ behavior: "smooth", block: "start" });
  }

  closeMenu();
};

onMounted(() => {
  scrollSentinel = document.createElement("div");
  scrollSentinel.style.cssText = "position:absolute;top:81px;left:0;width:1px;height:1px;pointer-events:none;";
  document.body.appendChild(scrollSentinel);
  scrollObserver = new IntersectionObserver(
    ([entry]) => {
      scrolled.value = !entry.isIntersecting;
    },
    { threshold: 0 }
  );
  scrollObserver.observe(scrollSentinel);
});

onUnmounted(() => {
  scrollObserver?.disconnect();
  scrollSentinel?.remove();
});


</script>

<style scoped>
.pp {
 gap: 4px;
  font-size: 13px;
}
.navbar {
  position: fixed;
  left: 50%;
  top: 24px;
  transform: translateX(-50%);
  width: min(1180px, 92%);
  height: 74px;
  padding: 0 24px;
  background: rgba(255, 255, 255, 0.82);
  backdrop-filter: blur(18px);
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-radius: 28px;
  box-shadow: 0 16px 36px rgba(28, 42, 35, 0.08);
  transition: all 0.35s ease;
  z-index: 999;
}

.navbar.shrink {
  height: 64px;
  width: min(980px, 88%);
  border-radius: 999px;
}

.logo {
  display: flex;
  align-items: center;
  gap: 14px;
  text-decoration: none;
}

.logo-img {
  width: 50px;
  height: 50px;
  object-fit: contain;
  transition: transform 0.3s ease;
}

.logo:hover .logo-img {
  transform: rotate(-5deg) scale(1.05);
}

.logo h2 {
  margin: 0;
  font-size: 20px;
  font-weight: 800;
  color: #1c2a23;
  font-style: normal;
}

.logo span {
  font-size: 13px;
  color: #3a6450;
  font-weight: 600;
  font-style: normal;
}

.desktop-nav {
  display: flex;
  gap: 28px;
  align-items: center;
}

.desktop-nav a {
  text-decoration: none;
  color: #5b6475;
  font-weight: 700;
  font-size: 15px;
  font-style: normal;
  line-height: 1;
  transition: color 0.25s ease;
}

.desktop-nav a:hover {
  color: #3a6450;
}

.nav-dropdown {
  position: relative;
}

.nav-dropdown::before {
  content: "";
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  height: 14px;
}

.dropdown-trigger {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  background: none;
  border: none;
  outline: none;
  color: #5b6475;
  font-family: inherit;
  font-weight: 700;
  font-size: 15px;
  font-style: normal;
  cursor: pointer;
  padding: 0;
  transition: color 0.25s ease;
  line-height: 1;
}

.dropdown-trigger:hover {
  color: #3a6450;
}

.dropdown-trigger:focus,
.dropdown-trigger:focus-visible {
  outline: none;
  box-shadow: none;
}

.chevron {
  transition: transform 0.2s ease;
}

.chevron.open {
  transform: rotate(180deg);
}

.dropdown-panel {
  position: absolute;
  top: calc(100% + 14px);
  left: 50%;
  transform: translateX(-50%) translateY(6px);
  width: max-content;
  padding: 10px;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.96);
  box-shadow: 0 18px 40px rgba(35, 55, 42, 0.12);
  backdrop-filter: blur(18px);
  opacity: 0;
  visibility: hidden;
  pointer-events: none;
  transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s;
  z-index: 1000;
}

.dropdown-panel::before {
  content: "";
  position: absolute;
  top: -6px;
  left: 50%;
  transform: translateX(-50%) rotate(45deg);
  width: 12px;
  height: 12px;
  background: rgba(255, 255, 255, 0.96);
}

.dropdown-panel.open {
  opacity: 1;
  visibility: visible;
  pointer-events: auto;
  transform: translateX(-50%) translateY(0);
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 12px;
  border-radius: 10px;
  text-decoration: none;
  color: #1c2a23;
  transition: background 0.2s ease;
}

.dropdown-item:hover {
  background: #e8f0e6;
}

.di-text {
  display: flex;
  flex-direction: column;
}

.di-title {
  font-size: 14px;
  font-weight: 700;
  font-style: normal;
  line-height: 1.2;
  white-space: nowrap;
}

.di-desc {
  font-size: 12px;
  color: #6c7a6e;
  line-height: 1.3;
  white-space: nowrap;
}

.ppdb {
  height: 48px;
  padding: 0 22px;
  border: none;
  border-radius: 16px;
  background: #3a6450;
  color: white;
  font-weight: 700;
  font-style: normal;
  cursor: pointer;
  transition:
    transform 0.3s ease,
    box-shadow 0.3s ease;
  box-shadow: 0 10px 24px rgba(58, 100, 80, 0.2);
}

.ppdb:hover {
  transform: translateY(-3px);
  box-shadow: 0 14px 30px rgba(58, 100, 80, 0.25);
}

.menu {
  display: none;
  width: 42px;
  height: 42px;
  padding: 0;
  border-radius: 12px;
  background: #fff;
  cursor: pointer;
}

.menu span {
  display: block;
  width: 19px;
  height: 2px;
  margin: 4px auto;
  border-radius: 999px;
  background: #1c2a23;
  transition:
    transform 0.3s ease,
    opacity 0.2s ease;
}

.menu.active span:nth-child(1) {
  transform: translateY(6px) rotate(45deg);
}

.menu.active span:nth-child(2) {
  opacity: 0;
}

.menu.active span:nth-child(3) {
  transform: translateY(-6px) rotate(-45deg);
}

.mobile-nav {
  display: none;
}

.mobile-menu-enter-active,
.mobile-menu-leave-active {
  transition:
    opacity 0.25s ease,
    transform 0.25s ease;
}

.mobile-menu-enter-from,
.mobile-menu-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

.nav-right {
  display: flex;
  align-items: center;
  gap: 10px;
}

.nav-profile-link {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 5px 16px 5px 6px;
  height: 52px;
  border-radius: 999px;
  background: #f0f4ef;
  text-decoration: none;
  transition:
    background 0.2s ease,
    box-shadow 0.2s ease,
    transform 0.2s ease;
}

.nav-profile-link:hover {
  background: #e2ebe0;
  box-shadow: 0 8px 18px rgba(58, 100, 80, 0.15);
  transform: translateY(-1px);
}

.nav-avatar {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: linear-gradient(135deg, #2f5b45 0%, #3a6450 100%);
  color: #fff;
  font-size: 15px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.25);
  flex-shrink: 0;
}

.nav-profile-text {
  display: flex;
  flex-direction: column;
  line-height: 1.15;
  text-align: left;
}

.nav-profile-name {
  font-size: 13px;
  font-weight: 800;
  color: #1c2a23;
  max-width: 110px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.nav-profile-role {
  font-size: 11px;
  font-weight: 700;
  color: #3a6450;
}

.mobile-bottom {
  display: flex;
  gap: 8px;
}

.mobile-profile {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 6px 16px 6px 6px;
  height: 52px;
  border-radius: 999px;
  background: #f0f4ef;
  color: #3a6450;
  font-weight: 700;
  font-size: 14px;
  text-decoration: none;
  flex: 1;
}

.mobile-avatar {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: linear-gradient(135deg, #2f5b45 0%, #3a6450 100%);
  color: #fff;
  font-size: 15px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.mobile-profile-text {
  display: flex;
  flex-direction: column;
  line-height: 1.15;
  text-align: left;
}

.mobile-profile-name {
  font-size: 14px;
  font-weight: 800;
  color: #1c2a23;
  max-width: 150px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.mobile-profile-role {
  font-size: 11px;
  font-weight: 700;
  color: #3a6450;
}

@media (max-width: 900px) {
  .desktop-nav,
  .ppdb,
  .nav-right {
    display: none;
  }

  .menu {
    display: block;
  }

  .mobile-nav {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    left: 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 12px;
    border-radius: 20px;
    background: rgba(251, 252, 250, 0.98);
    box-shadow: 0 18px 38px rgba(35, 55, 42, 0.12);
  }

  .mobile-nav a {
    padding: 13px 15px;
    border-radius: 12px;
    color: #1c2a23;
    font-size: 15px;
    font-weight: 700;
    font-style: normal;
    text-decoration: none;
    transition:
      background 0.2s ease,
      color 0.2s ease;
  }

  .mobile-nav a:hover {
    background: #e8f0e6;
    color: #3a6450;
  }

  .mobile-nav .mobile-ppdb {
    margin-top: 0;
    background: #3a6450;
    color: #fff;
    text-align: center;
    text-decoration: none;
  }

  .mobile-nav .mobile-ppdb:hover {
    background: #2a5238;
    color: #fff;
  }

  .mobile-nav .mobile-bottom {
    margin-top: 4px;
  }

  .mobile-dropdown {
    display: flex;
    flex-direction: column;
  }

  .mobile-dropdown-trigger {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 13px 15px;
    border: 0;
    border-radius: 12px;
    background: none;
    color: #1c2a23;
    font-family: inherit;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    text-align: left;
    transition: background 0.2s ease;
  }

  .mobile-dropdown-trigger:hover {
    background: #e8f0e6;
  }

  .mobile-dropdown-items {
    display: flex;
    flex-direction: column;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease, padding 0.3s ease;
  }

  .mobile-dropdown-items.open {
    max-height: 300px;
    padding: 4px 0 0;
  }

  .mobile-dropdown-items a {
    padding: 10px 15px 10px 40px;
    border-radius: 10px;
    color: #3a6450;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: background 0.2s ease;
  }

  .mobile-dropdown-items a:hover {
    background: #e8f0e6;
  }

  .logo h2 {
    font-size: 18px;
  }

  .navbar {
    height: 66px;
    padding: 0 18px;
    width: 92%;
    background: rgba(255, 255, 255, 0.97);
  }
}
</style>
