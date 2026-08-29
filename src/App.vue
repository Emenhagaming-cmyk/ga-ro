<template>
  <RouterView />
  <Analytics />
  <Teleport to="body">
    <Transition name="toast">
      <div v-if="visible" class="toast">{{ message }}</div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { Analytics } from "@vercel/analytics/vue";
import { useToast } from "@/composable/useToast";
const { message, visible } = useToast();
</script>

<style>
.toast {
  position: fixed;
  bottom: 32px;
  left: 50%;
  transform: translateX(-50%);
  padding: 14px 28px;
  border-radius: 14px;
  background: #1c2a23;
  color: #fff;
  font-size: 14px;
  font-weight: 700;
  font-family: "Quicksand", sans-serif;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
  z-index: 9999;
  pointer-events: none;
  white-space: nowrap;
}

.toast-enter-active,
.toast-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateX(-50%) translateY(12px);
}
</style>