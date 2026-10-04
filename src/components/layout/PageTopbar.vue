<template>
  <header class="page-topbar">
    <div class="topbar-left">
      <button type="button" class="topbar-back" aria-label="Kembali" @click="goBack">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
      </button>
        <img src="/logo.webp" alt="Logo" class="topbar-logo" width="34" height="34" />
      <span class="topbar-brand">{{ brand }}</span>
    </div>
  </header>
</template>

<script setup>
import { computed } from "vue";
import { useRouter } from "vue-router";

const props = defineProps({
  brand: { type: String, required: true },
  bg: { type: String, default: "#f2f4f1" },
});

const router = useRouter();

const barBg = computed(() => {
  const hex = props.bg.replace("#", "");
  const full = hex.length === 3 ? hex.split("").map((c) => c + c).join("") : hex;
  const num = parseInt(full, 16);
  if (Number.isNaN(num)) return "rgba(242, 244, 241, 0.92)";
  const r = (num >> 16) & 255;
  const g = (num >> 8) & 255;
  const b = num & 255;
  return `rgba(${r}, ${g}, ${b}, 0.92)`;
});

function goBack() {
  if (window.history.length > 1) {
    router.back();
  } else {
    router.push("/");
  }
}
</script>

<style scoped>
.page-topbar {
  position: sticky;
  top: 0;
  z-index: 100;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 14px max(5%, 32px);
  background: v-bind(barBg);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border-bottom: 1px solid #e3e8e3;
}

.topbar-left {
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 0;
}

.topbar-back {
  width: 40px;
  height: 40px;
  border: 1px solid #e3e8e3;
  border-radius: 50%;
  background: #fff;
  color: #1c2a23;
  display: grid;
  place-items: center;
  cursor: pointer;
  transition: background 0.2s ease;
  flex-shrink: 0;
}

.topbar-back:hover {
  background: #eef3ee;
}

.topbar-logo {
  height: 34px;
  width: auto;
  border-radius: 9px;
  background: #fff;
  border: 1px solid #e3e8e3;
  padding: 3px;
}

.topbar-brand {
  font-size: 16px;
  font-weight: 800;
  letter-spacing: -0.02em;
  color: #1c2a23;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

@media (max-width: 680px) {
  .page-topbar {
    padding: 12px max(4%, 18px);
  }

  .topbar-brand {
    font-size: 14px;
  }
}
</style>
