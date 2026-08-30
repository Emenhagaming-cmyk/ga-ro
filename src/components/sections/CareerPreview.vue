<template>
  <section id="karir" class="career-preview">
    <div class="cp-shell" v-reveal>
      <div class="cp-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path><rect x="2" y="6" width="20" height="14" rx="2"></rect></svg>
      </div>
      <div class="cp-body">
        <h3>Career Center</h3>
        <p>Lowongan magang &amp; kerja dari mitra industri untuk siswa dan alumni.</p>
      </div>
      <div class="cp-count">
        <span class="cp-num">{{ lowonganCount }}</span>
        <span class="cp-label">Lowongan Aktif</span>
      </div>
      <router-link to="/career-center" class="cp-btn">
        Lihat Peluang
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
      </router-link>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useAuthSession } from "@/composable/useAuthSession";

const { BACKEND } = useAuthSession();
const lowonganCount = ref(0);

onMounted(async () => {
  try {
    const res = await fetch(`${BACKEND}/lowongan/count`, { credentials: "include" });
    const data = await res.json();
    lowonganCount.value = data.total || 0;
  } catch (e) {
    lowonganCount.value = 0;
  }
});
</script>

<style scoped>
.career-preview {
  padding: 48px 7%;
  background: #f2f4f1;
  scroll-margin-top: 90px;
}

.cp-shell {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  gap: 24px;
  padding: 26px 30px;
  border-radius: 20px;
  background: linear-gradient(135deg, #2d5a3d 0%, #3a6450 100%);
  border: 1px solid #2d5a3d;
  color: #fff;
  box-shadow: 0 18px 40px rgba(35, 55, 42, 0.2);
}

.cp-icon {
  flex-shrink: 0;
  display: grid;
  place-items: center;
  width: 48px;
  height: 48px;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.14);
  color: #fff;
}

.cp-body {
  flex: 1;
  min-width: 0;
}

.cp-body h3 {
  margin: 0;
  font-family: "Quicksand", sans-serif;
  font-size: 19px;
  font-weight: 800;
}

.cp-body p {
  margin: 4px 0 0;
  font-size: 13px;
  color: rgba(255, 255, 255, 0.72);
  line-height: 1.5;
}

.cp-count {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 0 26px;
  border-left: 1px solid rgba(255, 255, 255, 0.18);
  border-right: 1px solid rgba(255, 255, 255, 0.18);
}

.cp-num {
  font-family: "Quicksand", sans-serif;
  font-size: 34px;
  font-weight: 800;
  line-height: 1;
  font-variant-numeric: tabular-nums;
}

.cp-label {
  margin-top: 4px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.6);
}

.cp-btn {
  flex-shrink: 0;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 13px 24px;
  border-radius: 13px;
  background: #fff;
  color: #1a3a28;
  font-size: 14px;
  font-weight: 700;
  text-decoration: none;
  transition: transform 0.2s ease, background 0.2s ease;
}

.cp-btn:hover {
  background: #e8f0eb;
  transform: translateY(-2px);
}

@media (max-width: 900px) {
  .career-preview {
    padding: 32px 5%;
  }

  .cp-shell {
    flex-wrap: wrap;
    gap: 16px;
  }

  .cp-count {
    width: 100%;
    border-left: none;
    border-right: none;
    border-top: 1px solid rgba(255, 255, 255, 0.18);
    border-bottom: 1px solid rgba(255, 255, 255, 0.18);
    padding: 14px 0;
  }

  .cp-btn {
    width: 100%;
    justify-content: center;
  }
}
</style>