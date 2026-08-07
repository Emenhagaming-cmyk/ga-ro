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
          <a :href="spmbTarget()" class="dropdown-item">
            <span class="di-text">
              <span class="di-title">SPMB Online</span>
            </span>
          </a>
          <a href="/koperasi" class="dropdown-item">
            <span class="di-text">
              <span class="di-title">Koperasi</span>
            </span>
          </a>
          <a href="/produk-siswa" class="dropdown-item">
            <span class="di-text">
              <span class="di-title">Produk Siswa</span>
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

    <button class="ppdb" type="button" @click="goSPMB">
      SPMB
    </button>

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
            <a :href="spmbTarget()" @click="closeMenu">SPMB Online</a>
            <a href="/koperasi" @click="closeMenu">Koperasi</a>
            <a href="/produk-siswa" @click="closeMenu">Produk Siswa</a>
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
        <a :href="spmbTarget()" class="mobile-ppdb">SPMB</a>
      </nav>
    </Transition>
  </header>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useAuthSession } from "@/composable/useAuthSession";

const scrolled = ref(false);
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
const { spmbTarget } = useAuthSession();

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
  window.location.href = spmbTarget();
  closeMenu();
};

const scrollToSection = (id) => {
  const target = document.getElementById(id);

  if (target) {
    target.scrollIntoView({ behavior: "smooth", block: "start" });
  }

  closeMenu();
};

const handleScroll = () => {
  scrolled.value = window.scrollY > 80;
};

onMounted(() => {
  window.addEventListener("scroll", handleScroll);
});

onUnmounted(() => {
  window.removeEventListener("scroll", handleScroll);
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
}

.di-desc {
  font-size: 12px;
  color: #6c7a6e;
  line-height: 1.3;
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

@media (max-width: 900px) {
  .desktop-nav,
  .ppdb {
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
    backdrop-filter: blur(18px);
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
    margin-top: 4px;
    background: #3a6450;
    color: #fff;
    text-align: center;
  }

  .mobile-nav .mobile-ppdb:hover {
    background: #2a5238;
    color: #fff;
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
  }
}
</style>
