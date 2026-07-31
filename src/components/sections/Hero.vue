<template>
  <section class="hero">
    <div class="grid"></div>
    <div class="blur blur1"></div>
    <div class="blur blur2"></div>

    <div class="container">
      <div class="left">
        <span class="badge">SMK Bahrul Ulum</span>

        <h1>
          Belajar.<br>
          Berkarya.<br>
          Berkembang.
        </h1>

        <div class="bg-word">SCHOOL</div>

        <p>
          Platform digital sekolah yang menghubungkan
          SPMB, Career Center, Produk Siswa,
          dan ChatBot dalam satu tempat.
        </p>

        <div class="buttons">
          <button class="primary">Jelajahi</button>
          <button class="secondary">SPMB</button>
        </div>
      </div>

      <div class="right">
        <article class="profile-card">
          <div class="card-accent"></div>
          <div class="content">
            <span class="mini-title">Profil sekolah</span>

            <h2>SMK Bahrul Ulum</h2>

            <p>
              Membentuk generasi berkarakter, religius, mandiri,
              gotong royong, dan berintegritas.
            </p>

            <button class="learn" type="button">Kenali sekolah <span>→</span></button>
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
  min-height: 100vh;
  overflow: hidden;
  background: #f2f4f1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 120px 7%;
  color: #1c2a23;
}

.grid {
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgba(58, 100, 80, 0.04) 1px, transparent 1px),
    linear-gradient(90deg, rgba(58, 100, 80, 0.04) 1px, transparent 1px);
  background-size: 76px 76px;
}

.blur {
  position: absolute;
  border-radius: 50%;
  filter: blur(90px);
  opacity: 0.45;
}

.blur1 {
  width: 300px;
  height: 300px;
  background: #7db88d;
  left: -70px;
  top: 150px;
}

.blur2 {
  width: 340px;
  height: 340px;
  background: #a8c9a8;
  right: -110px;
  bottom: 90px;
}

.container {
  position: relative;
  z-index: 2;
  width: 100%;
  max-width: 1300px;
  display: grid;
  grid-template-columns: minmax(0, 1fr) 430px;
  gap: 72px;
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
}

h1 {
  margin: 18px 0 0;
  color: #1c2a23;
  font-size: clamp(64px, 8vw, 92px);
  line-height: 0.95;
  letter-spacing: -0.06em;
  position: relative;
  z-index: 2;
}

.bg-word {
  position: absolute;
  top: 72px;
  left: -8px;
  color: #3a6450;
  opacity: 0.05;
  font-size: clamp(110px, 14vw, 170px);
  font-weight: 800;
  pointer-events: none;
}

p {
  margin-top: 34px;
  max-width: 620px;
  color: #6c7a6e;
  font-size: 19px;
  line-height: 1.9;
}

.buttons {
  display: flex;
  gap: 18px;
  margin-top: 42px;
}

button {
  height: 58px;
  padding: 0 30px;
  border-radius: 16px;
  font-family: inherit;
  font-size: 16px;
  cursor: pointer;
  transition: transform 0.28s ease, box-shadow 0.28s ease, background 0.28s ease;
}

.primary {
  border: none;
  background: #3a6450;
  color: #fff;
  box-shadow: 0 16px 34px rgba(58, 100, 80, 0.22);
}

.primary:hover {
  transform: translateY(-3px);
}

.secondary {
  background: rgba(255, 255, 255, 0.78);
  border: 1px solid #dfe4dd;
  color: #1c2a23;
  backdrop-filter: blur(10px);
}

.secondary:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 26px rgba(28, 42, 35, 0.07);
}

.profile-card {
  position: relative;
  overflow: hidden;
  background: rgba(255, 255, 255, 0.9);
  border: 1px solid #dfe4dd;
  border-radius: 6px;
  box-shadow: 0 20px 40px rgba(35, 55, 42, 0.09);
  backdrop-filter: blur(16px);
  transition: transform 0.35s ease, box-shadow 0.35s ease;
}

.profile-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 26px 50px rgba(35, 55, 42, 0.12);
}

.profile-card::before {
  content: "";
  position: absolute;
  width: 220px;
  height: 220px;
  right: -104px;
  top: -116px;
  border-radius: 50%;
  background: #e8f0e6;
}

.card-accent {
  position: relative;
  height: 6px;
  background: linear-gradient(90deg, #2a5238 0%, #3a6450 58%, #7db88d 100%);
}

.content {
  position: relative;
  padding: 26px 28px;
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
  margin: 16px 0 12px;
  color: #1c2a23;
  font-size: 32px;
  line-height: 1.1;
  letter-spacing: -0.045em;
}

.profile-card p {
  margin: 0;
  color: #6c7a6e;
  font-size: 15px;
  line-height: 1.8;
}

.learn {
  display: inline-flex;
  align-items: center;
  gap: 9px;
  width: max-content;
  height: auto;
  margin-top: 22px;
  padding: 0;
  border: 0;
  border-radius: 0;
  background: transparent;
  color: #3a6450;
  font-size: 14px;
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
}

.line {
  margin-top: 10px;
  width: 2px;
  height: 50px;
  background: #3a6450;
  animation: scroll 1.6s infinite;
}

@keyframes scroll {
  0% { transform: scaleY(0); transform-origin: top; }
  50% { transform: scaleY(1); transform-origin: top; }
  100% { transform: scaleY(0); transform-origin: bottom; }
}

@media (max-width: 900px) {
  .hero {
    padding: 140px 24px 84px;
  }

  .container {
    grid-template-columns: 1fr;
    gap: 44px;
  }

  h1 {
    font-size: clamp(52px, 12vw, 66px);
  }

  .bg-word {
    top: 58px;
    left: -4px;
  }

  p {
    font-size: 17px;
  }

  .buttons {
    flex-direction: column;
  }

  button {
    width: 100%;
  }

  .content {
    padding: 26px 28px;
  }

  .school-mark {
    width: 48px;
    height: 48px;
    font-size: 15px;
  }

  .profile-card h2 {
    font-size: 26px;
  }

  .school-stats {
    gap: 0;
    margin: 20px 0 22px;
  }
}

@media (max-width: 520px) {
  .hero {
    padding: 128px 18px 76px;
  }

  .badge {
    font-size: 12px;
  }

  .profile-card h2 {
    font-size: 26px;
  }

  .scroll {
    display: none;
  }
}
</style>
