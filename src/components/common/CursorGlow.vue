<template>
  <div
    v-if="desktop"
    ref="el"
    class="cursor"
  />
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";

const el = ref(null);
const desktop = ref(false);
let raf = 0;

// ponytail: tulis langsung ke el.style di requestAnimationFrame — tanpa Vue
// reactivity (ref tiap mousemove memicu re-render) & tanpa CSS transition pada
// left/top. INP: biaya mousemove ~1 gaya/tempat layout bukan patch DOM.
function move(e) {
  if (raf) return;
  raf = requestAnimationFrame(() => {
    raf = 0;
    if (el.value) {
      el.value.style.left = e.clientX + "px";
      el.value.style.top = e.clientY + "px";
    }
  });
}

onMounted(() => {
  desktop.value = window.innerWidth > 900;
  if (desktop.value) window.addEventListener("mousemove", move, { passive: true });
});

onUnmounted(() => {
  window.removeEventListener("mousemove", move);
  if (raf) cancelAnimationFrame(raf);
});
</script>

<style scoped>

.cursor{

position:fixed;

width:260px;
height:260px;

border-radius:50%;

pointer-events:none;

transform:translate(-50%,-50%);

background:
radial-gradient(
circle,
rgba(125,184,141,.14),
transparent 70%
);

filter:blur(20px);

z-index:0;

}

</style>