<template>
  <section
    ref="target"
    class="fade-section"
    :class="{ show: visible }"
  >
    <slot/>
  </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue"

const target = ref(null)
const visible = ref(false)

let observer

onMounted(() => {
  observer = new IntersectionObserver(
    ([entry]) => {
      if (entry.isIntersecting) {
        visible.value = true
        observer.unobserve(entry.target)
      }
    },
    {
      threshold: 0.15
    }
  )

  observer.observe(target.value)
})

onUnmounted(() => {
  observer?.disconnect()
})
</script>

<style scoped>

.fade-section{

opacity:0;

transform:
translateY(70px);

transition:

opacity .8s ease,

transform .8s cubic-bezier(.22,.61,.36,1);

}

.fade-section.show{

opacity:1;

transform:
translateY(0);

}

</style>