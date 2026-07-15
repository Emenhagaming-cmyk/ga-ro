<template>
  <div
    v-if="desktop"
    class="cursor"
    :style="{
      left: x + 'px',
      top: y + 'px'
    }"
  />
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";

const x = ref(0);
const y = ref(0);
const desktop = ref(false);

function move(e) {
  x.value = e.clientX;
  y.value = e.clientY;
}

onMounted(() => {
  desktop.value = window.innerWidth > 900;

  if (desktop.value) {
    window.addEventListener("mousemove", move);
  }
});

onUnmounted(() => {
  window.removeEventListener("mousemove", move);
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
rgba(91,127,255,.12),
transparent 70%
);

filter:blur(20px);

z-index:0;

transition:
left .08s linear,
top .08s linear;

}

</style>