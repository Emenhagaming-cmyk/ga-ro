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
      <div class="left">
        <span class="badge">SMK Bahrul Ulum</span>

        <h1>
          Belajar.<br />
          Berkarya.<br />
          Berkembang.
        </h1>

        <div class="bg-word">SCHOOL</div>

        <p>
          Platform digital sekolah yang menghubungkan SPMB, Career Center,
          Produk Siswa, dan ChatBot dalam satu tempat.
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

      <div class="right">
        <article class="profile-card">
          <div class="card-accent"></div>
          <div class="content">
            <span class="mini-title">Profil sekolah</span>

            <h2>SMK Bahrul Ulum</h2>

            <p>
              Membentuk generasi berkarakter, religius, mandiri, gotong royong,
              dan berintegritas.
            </p>

            <button
              class="learn"
              type="button"
              @click="scrollToSection('layanan')"
            >
              Kenali sekolah <span></span>
            </button>
          </div>
        </article>
      </div>
    </div>

    <div class="scroll">
      Scroll
      <div class="line"></div>
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
  padding: 96px 7%;
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
  display: grid;
  grid-template-columns: minmax(0, 1fr) 380px;
  gap: 56px;
  align-items: center;
}

.left {
  position: relative;
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

.profile-card {
  position: relative;
  overflow: hidden;
  background: #fbfcfa;
  border: 1px solid #dfe4dd;
  border-radius: 22px;
  box-shadow: 0 12px 24px rgba(35, 55, 42, 0.06);
  transition:
    transform 0.2s ease,
    box-shadow 0.2s ease;
}

.profile-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 14px 28px rgba(35, 55, 42, 0.08);
}

.profile-card::before {
  content: "";
  position: absolute;
  width: 180px;
  height: 180px;
  right: -70px;
  top: -90px;
  border-radius: 50%;
  background: rgba(125, 184, 141, 0.12);
}

.card-accent {
  position: relative;
  height: 5px;
  background: linear-gradient(90deg, #2a5238 0%, #3a6450 58%, #7db88d 100%);
}

.content {
  position: relative;
  padding: 22px 24px;
}

.mini-title {
  display: inline-flex;
  color: #3a6450;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.15em;
  text-transform: uppercase;
}

.profile-card h2 {
  margin: 14px 0 10px;
  color: #1c2a23;
  font-size: 27px;
  line-height: 1.1;
  letter-spacing: -0.045em;
}

.profile-card p {
  margin: 0;
  color: #6c7a6e;
  font-size: 14px;
  line-height: 1.75;
}

.learn {
  display: inline-flex;
  align-items: center;
  gap: 9px;
  width: max-content;
  height: auto;
  margin-top: 18px;
  padding: 0;
  border: 0;
  border-radius: 0;
  background: transparent;
  color: #3a6450;
  font-size: 13px;
  font-weight: 700;
  box-shadow: none;
}

.learn span {
  font-size: 18px;
  line-height: 1;
  transition: transform 0.25s ease;
}

.learn:hover {
  transform: none;
  box-shadow: none;
}

.learn:hover span {
  transform: translateX(4px);
}

.scroll {
  position: absolute;
  bottom: 28px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  flex-direction: column;
  align-items: center;
  font-size: 13px;
  letter-spacing: 0.16em;
  color: #6c7a6e;
  user-select: none;
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
    padding: 120px 24px 72px;
  }

  .container {
    grid-template-columns: 1fr;
    gap: 36px;
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

  button {
    width: 100%;
  }

  .content {
    padding: 22px 24px;
  }

  .school-mark {
    width: 48px;
    height: 48px;
    font-size: 15px;
  }

  .profile-card h2 {
    font-size: 24px;
  }

  .school-stats {
    gap: 0;
    margin: 20px 0 22px;
  }
}

@media (max-width: 520px) {
  .hero {
    padding: 108px 18px 64px;
  }

  .badge {
    font-size: 12px;
  }

  .profile-card h2 {
    font-size: 24px;
  }

  .scroll {
    display: none;
  }
}
</style>
