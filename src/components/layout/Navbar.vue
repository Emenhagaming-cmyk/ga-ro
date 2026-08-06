<template>
  <header class="navbar" :class="{ shrink: scrolled }">
    <a href="#top" class="logo">
      <img src="/logo.png" alt="Logo Sekolah" class="logo-img" />

      <div>
        <h2>SMK Bahrul Ulum</h2>
        <span>Smart School</span>
      </div>
    </a>

    <nav class="desktop-nav">
      <a href="#top">Beranda</a>
      <a href="#layanan">Layanan</a>
      <a href="/berita">Berita</a>
      <a href="#tentang">Tentang</a>
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
        <a href="#layanan" @click="closeMenu">Layanan</a>
        <a href="/berita">Berita</a>
        <a href="#tentang" @click="closeMenu">Tentang</a>
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
const { spmbTarget } = useAuthSession();

const toggleMenu = () => {
  menuOpen.value = !menuOpen.value;
};

const closeMenu = () => {
  menuOpen.value = false;
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
  border: 1px solid rgba(223, 228, 221, 0.9);
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
}

.logo span {
  font-size: 13px;
  color: #3a6450;
  font-weight: 600;
}

.desktop-nav {
  display: flex;
  gap: 28px;
}

.desktop-nav a {
  text-decoration: none;
  color: #5b6475;
  font-weight: 700;
  font-size: 15px;
  transition: color 0.25s ease;
}

.desktop-nav a:hover {
  color: #3a6450;
}

.ppdb {
  height: 48px;
  padding: 0 22px;
  border: none;
  border-radius: 16px;
  background: #3a6450;
  color: white;
  font-weight: 700;
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
  border: 1px solid #dfe4dd;
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
    border: 1px solid rgba(223, 228, 221, 0.95);
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
