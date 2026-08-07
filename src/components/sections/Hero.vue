<script setup>
const BACKEND = "http://localhost:8000";

const scrollToSection = (id) => {
  const target = document.getElementById(id);

  if (target) {
    target.scrollIntoView({ behavior: "smooth", block: "start" });
  }
};
</script>

<template>
  <section class="hero" id="top">
    <div class="grid"></div>
    <div class="ambient ambient1"></div>
    <div class="ambient ambient2"></div>

    <div class="container">

      <h1>
        Selamat Datang Di<br />
      </h1>

      <h1 class="warna">
        SMK Bahrul Ulum<br />
      </h1>

      <div class="bg-word">SCHOOL</div>

      <p>
        SMK Bahrul Ulum Surabaya adalah sekolah menengah kejuruan yang berfokus pada pengembangan keterampilan dan pengetahuan siswa dalam berbagai bidang keahlian. Dengan kurikulum yang inovatif dan fasilitas modern, SMK Bahrul Ulum Surabaya mempersiapkan siswa untuk menghadapi tantangan dunia kerja dan membekali mereka dengan keterampilan praktis yang relevan dengan industri saat ini.
      </p>

      <div class="buttons">
        <a :href="`${BACKEND}/login`" class="primary btn-login">
          Login
        </a>
        <a :href="`${BACKEND}/register`" class="secondary btn-register">
          Daftar
        </a>
      </div>
    </div>
  </section>
</template>

<style scoped>
.warna{
  color: #04944e;
}
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

.btn-login,
.btn-register,
.btn-dashboard {
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

.secondary {
  background: rgba(255, 255, 255, 0.85);
  border: 1px solid #dfe4dd;
  color: #1c2a23;
}

.secondary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 18px rgba(28, 42, 35, 0.06);
}


.line {
  margin-top: 10px;
  width: 2px;
  height: 50px;
  background: #3a6450;
  animation: scroll 1.6s ease-in-out infinite;
}

@keyframes scroll {
  0% {
    transform: scaleY(0);
    transform-origin: top;
  }
  50% {
    transform: scaleY(1);
    transform-origin: top;
  }
  100% {
    transform: scaleY(0);
    transform-origin: bottom;
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

  .btn-login,
  .btn-register {
    width: 100%;
  }
}

@media (max-width: 520px) {
  .hero {
    padding: 140px 18px 64px;
  }

  .badge {
    font-size: 12px;
  }

  .scroll {
    display: none;
  }
}
</style>
