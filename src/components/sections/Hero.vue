<script setup>
import { computed, ref } from "vue";
import { useAuthSession } from "@/composable/useAuthSession";
const { BACKEND, session } = useAuthSession();

const isSiswa = computed(() => session.value.role === "siswa");

const loginExpanded = ref(false);
const toggleLogin = () => {
  loginExpanded.value = !loginExpanded.value;
};
</script>

<template>
  <section class="hero" id="top">
    <div class="grid"></div>
    <div class="ambient ambient1"></div>
    <div class="ambient ambient2"></div>

    <div class="container">

      <span class="badge">Pendaftaran SPMB Dibuka</span>

      <h1>
        Belajar<br />
        Berkarya<br />
        Berprestasi
      </h1>

      <p>
        Sekolah menengah kejuruan unggulan yang membekali siswa dengan keterampilan nyata untuk masa depan.
      </p>

      <div class="bg-word">SCHOOL</div>

      <div class="buttons">
        <a
          v-if="isSiswa"
          :href="`${BACKEND}/dashboard-siswa`"
          class="primary btn-login"
        >
          Dashboard Siswa
        </a>
        <div class="btn-group-login" v-else>
          <button class="primary btn-login" @click="toggleLogin">
            Login
          </button>
          <div class="sub-buttons" :class="{ open: loginExpanded }">
            <a :href="`${BACKEND}/login`" class="sub-btn">Login Siswa</a>
            <a :href="`${BACKEND}/login`" class="sub-btn">Login Pendaftar</a>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.hero {
  position: relative;
  min-height: 88vh;
  overflow: hidden;
  background: #f2f4f1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 140px 7% 96px;
  color: #1c2a23;
  background:
    radial-gradient(
      circle at top left,
      rgba(125, 184, 141, 0.14),
      transparent 28%
    ),
    #f2f4f1;
}

.grid {
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgba(58, 100, 80, 0.035) 1px, transparent 1px),
    linear-gradient(90deg, rgba(58, 100, 80, 0.035) 1px, transparent 1px);
  background-size: 72px 72px;
  pointer-events: none;
}

.ambient {
  position: absolute;
  border-radius: 50%;
  opacity: 0.38;
  pointer-events: none;
}

.ambient1 {
  width: 240px;
  height: 240px;
  background: #7db88d;
  left: -60px;
  top: 140px;
  filter: blur(70px);
}

.ambient2 {
  width: 280px;
  height: 280px;
  background: #c7d9c3;
  right: -90px;
  bottom: 80px;
  filter: blur(70px);
}

.container {
  position: relative;
  z-index: 2;
  width: 100%;
  max-width: 1180px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 10px 18px;
  border-radius: 999px;
  background: #e8f0e6;
  color: #3a6450;
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 0.16em;
  border: 1px solid rgba(58, 100, 80, 0.08);
}

h1 {
  margin: 14px 0 0;
  color: #1c2a23;
  font-size: clamp(48px, 6vw, 68px);
  line-height: 0.98;
  letter-spacing: -0.05em;
  position: relative;
  z-index: 2;
  font-weight: 800;
}

.bg-word {
  position: absolute;
  top: 52px;
  left: -8px;
  color: #3a6450;
  opacity: 0.06;
  font-size: clamp(90px, 11vw, 130px);
  font-weight: 800;
  pointer-events: none;
  user-select: none;
}

p {
  margin-top: 24px;
  max-width: 560px;
  color: #647067;
  font-size: 17px;
  line-height: 1.75;
}

.buttons {
  display: flex;
  gap: 14px;
  margin-top: 32px;
  align-items: flex-start;
}

.btn-group-login {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.sub-buttons {
  display: flex;
  flex-direction: row;
  gap: 8px;
  max-height: 0;
  opacity: 0;
  overflow: hidden;
  transition: max-height 0.3s ease, opacity 0.25s ease, margin 0.3s ease;
  margin-top: 0;
  width: 100%;
  justify-content: center;
}

.sub-buttons.open {
  max-height: 60px;
  opacity: 1;
  margin-top: 10px;
}

.sub-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 42px;
  padding: 0 20px;
  border-radius: 10px;
  font-family: inherit;
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
  background: rgba(58, 100, 80, 0.08);
  color: #3a6450;
  border: 1px solid rgba(58, 100, 80, 0.12);
  cursor: pointer;
  transition: background 0.2s ease, transform 0.15s ease;
  white-space: nowrap;
}

.sub-btn:hover {
  background: rgba(58, 100, 80, 0.15);
  transform: translateY(-1px);
}

button {
  height: 48px;
  padding: 0 24px;
  border-radius: 12px;
  font-family: inherit;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  transition:
    transform 0.2s ease,
    box-shadow 0.2s ease,
    background 0.2s ease;
}

.btn-login {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  height: 48px;
  padding: 0 24px;
  border-radius: 12px;
  font-family: inherit;
  font-size: 15px;
  font-weight: 700;
  text-decoration: none;
  cursor: pointer;
  transition:
    transform 0.2s ease,
    box-shadow 0.2s ease,
    background 0.2s ease;
}

.primary {
  border: none;
  background: #3a6450;
  color: #fff;
  box-shadow: 0 10px 24px rgba(58, 100, 80, 0.17);
}

.primary:hover {
  transform: translateY(-2px);
}

.primary:active {
  transform: translateY(1px) scale(0.98);
}

.secondary {
  background: rgba(255, 255, 255, 0.85);
  border: 1px solid #dfe4dd;
  color: #1c2a23;
}

.secondary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 18px rgba(28, 42, 35, 0.06);
}


.badge,
h1,
p,
.buttons {
  animation: rise 0.7s cubic-bezier(0.22, 0.61, 0.36, 1) both;
}

.badge {
  animation-delay: 0.05s;
}

h1 {
  animation-delay: 0.15s;
}

p {
  animation-delay: 0.3s;
}

.buttons {
  animation-delay: 0.45s;
}

@keyframes rise {
  from {
    opacity: 0;
    transform: translateY(18px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (max-width: 900px) {
  .hero {
    padding: 160px 24px 72px;
  }

  h1 {
    font-size: clamp(42px, 10vw, 54px);
  }

  .bg-word {
    top: 44px;
    left: -4px;
  }

  p {
    font-size: 16px;
  }

  .buttons {
    flex-direction: column;
  }

  .btn-login {
    width: 100%;
  }

  .sub-buttons {
    width: 100%;
  }

  .sub-btn {
    flex: 1;
  }
}

@media (max-width: 520px) {
  .hero {
    padding: 140px 18px 64px;
  }

  .badge {
    font-size: 12px;
  }
}
</style>
