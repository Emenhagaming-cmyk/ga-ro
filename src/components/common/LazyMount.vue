<template>
  <div ref="root" class="lazy-mount">
    <slot v-if="shown" />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";

// ponytail: mount children hanya saat mendekati viewport (IntersectionObserver).
// Menunda render + fetch section di bawah fold → load awal cepat, tanpa content
// yang benar-benar perlu di hydrate dulu. rootMargin memuat ~600px sebelum masuk.
const root = ref(null);
const shown = ref(false);
let obs = null;

onMounted(() => {
  if (!root.value) return;
  if (!("IntersectionObserver" in window)) {
    shown.value = true;
    return;
  }
  obs = new IntersectionObserver(
    ([entry]) => {
      if (entry.isIntersecting) {
        shown.value = true;
        obs.disconnect();
      }
    },
    { rootMargin: "600px 0px" }
  );
  obs.observe(root.value);
});

onUnmounted(() => obs?.disconnect());
</script>
