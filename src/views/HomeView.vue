<script setup>
import { ref } from "vue";
import CursorGlow from "../components/common/CursorGlow.vue";
import LoadingScreen from "@/components/loading/LoadingScreen.vue";
import Navbar from "@/components/layout/Navbar.vue";
import Footer from "@/components/layout/Footer.vue";
import Hero from "@/components/sections/Hero.vue";
import AboutSchool from "@/components/sections/AboutSchool.vue";
import Feature from "@/components/sections/feature.vue";
import News from "@/components/sections/News.vue";
import BackgroundFX from "@/components/common/BackgroundFX.vue";
import FloatingAi from "@/components/chatbot/FloatingAi.vue";
import { useAuthSession } from "@/composable/useAuthSession";

const skipIntro =
  new URLSearchParams(window.location.search).get("no-intro") === "1";
const showIntro = ref(!skipIntro && !sessionStorage.getItem("intro"));

if (showIntro.value || skipIntro) {
  sessionStorage.setItem("intro", "true");
}

const { session, BACKEND } = useAuthSession();
const showStudentCard = () =>
  session.value.logged_in && session.value.role === "siswa";
const statusLabel = () => (session.value.status || "").toUpperCase();
</script>

<template>
  <div class="page-wrapper">
    <FloatingAi />

    <LoadingScreen v-if="showIntro" @finish="showIntro = false" />

    <BackgroundFX />
    <CursorGlow />

    <div class="page">
      <Navbar />

      <div v-if="showStudentCard()" class="student-card" id="student-access">
        <div class="sc-icon">
          <svg
            width="22"
            height="22"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
            <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
          </svg>
        </div>
        <div class="sc-body">
          <p class="sc-kicker">Aktivitas</p>
          <h3 v-if="session.has_pendaftaran">
            Anda telah mendaftar di SPMB SMK Bahrul Ulum
          </h3>
          <h3 v-else>
            Hai {{ session.name }}, lengkapi pendaftaran SPMB Dulu Ya
          </h3>
          <p class="sc-status" v-if="session.has_pendaftaran">
            Status pendaftaran Anda Saat Ini: <strong>{{ statusLabel() }}</strong>
          </p>
          <p class="sc-desc" v-else>
            Kamu sudah punya akun, tetapi belum mengirim formulir pendaftaran.
          </p>
        </div>
        <a
          :href="
            session.has_pendaftaran
              ? BACKEND + '/dashboard-siswa'
              : BACKEND + '/pendaftaran/create'
          "
          class="sc-btn"
        >
          {{
            session.has_pendaftaran
              ? "Buka Dashboard Siswa"
              : "Lengkapi Pendaftaran"
          }}
        </a>
      </div>
      <Hero />
      <AboutSchool />
      <Feature />
      <News />
      <Footer />
    </div>
  </div>
</template>
<style>
* {
  box-sizing: border-box;
}

html,
body,
#app {
  margin: 0;
  padding: 0;

  width: 100%;

  overflow-x: hidden;

  font-family: "Quicksand", sans-serif;

  background: #f8faff;
}

.page-wrapper {
  position: relative;
  min-height: 100vh;
  background:
    radial-gradient(
      circle at top left,
      rgba(125, 184, 141, 0.14),
      transparent 28%
    ),
    #f2f4f1;
}

.page-wrapper::before {
  content: "";
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgba(58, 100, 80, 0.035) 1px, transparent 1px),
    linear-gradient(90deg, rgba(58, 100, 80, 0.035) 1px, transparent 1px);
  background-size: 72px 72px;
  pointer-events: none;
  z-index: -1;
}

.page {
  width: 100%;

  overflow-x: hidden;
}

.student-card {
  display: flex;
  align-items: center;
  gap: 16px;
  max-width: 1180px;
  margin: 108px auto 0;
  padding: 18px 24px;
  border-radius: 20px;
  background: linear-gradient(135deg, #eef7ee 0%, #fbfcfa 100%);
  border: 1px solid rgba(58, 100, 80, 0.18);
  box-shadow: 0 14px 34px rgba(35, 55, 42, 0.08);
}

.sc-icon {
  flex-shrink: 0;
  display: grid;
  place-items: center;
  width: 48px;
  height: 48px;
  border-radius: 14px;
  background: #3a6450;
  color: #fff;
}

.sc-body {
  flex: 1;
  min-width: 0;
}

.sc-kicker {
  margin: 0 0 2px;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #070707;
}

.sc-body h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 800;
  color: #1c2a23;
  letter-spacing: -0.02em;
}

.sc-status,
.sc-desc {
  margin: 4px 0 0;
  font-size: 13px;
  color: #647067;
}

.sc-status strong {
  color: #3a6450;
}

.sc-btn {
  flex-shrink: 0;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  height: 44px;
  padding: 0 20px;
  border-radius: 12px;
  background: #3a6450;
  color: #fff !important;
  font-size: 14px;
  font-weight: 700;
  text-decoration: none;
  transition: background 0.2s ease, transform 0.2s ease;
}

.sc-btn:hover {
  background: #2a5238;
  color: #fff !important;
  transform: translateY(-1px);
}

.sc-btn:hover {
  background: #2a5238;
  transform: translateY(-1px);
}

@media (max-width: 900px) {
  .student-card {
    margin: 92px 16px 0;
    flex-direction: column;
    align-items: stretch;
    text-align: center;
  }

  .sc-icon {
    margin: 0 auto;
  }

  .sc-btn {
    justify-content: center;
  }
}
</style>
