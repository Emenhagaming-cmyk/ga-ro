<script setup>
import { CheckCircle2, CircleAlert, TriangleAlert, Info, X } from "lucide-vue-next";
import { useToast } from "@/composable/useToast";

const { toasts, dismissToast } = useToast();

const ICONS = { success: CheckCircle2, error: CircleAlert, warning: TriangleAlert, info: Info };
</script>

<template>
  <div class="toast-area" aria-live="polite">
    <TransitionGroup name="toast">
      <div v-for="t in toasts" :key="t.id" class="toast" :class="`toast--${t.type}`" role="status">
        <span class="toast-icon">
          <component :is="ICONS[t.type] || Info" :size="17" :stroke-width="2.5" />
        </span>
        <p class="toast-msg">{{ t.msg }}</p>
        <button type="button" class="toast-close" aria-label="Tutup notifikasi" @click="dismissToast(t.id)">
          <X :size="14" :stroke-width="2.5" />
        </button>
        <span class="toast-progress" :style="{ animationDuration: t.duration + 'ms' }"></span>
      </div>
    </TransitionGroup>
  </div>
</template>

<style scoped>
.toast-area {
  position: fixed; top: 18px; left: 50%; transform: translateX(-50%);
  z-index: 3000; display: flex; flex-direction: column; gap: 10px;
  width: min(92vw, 420px); pointer-events: none;
}
.toast {
  --t-accent: #2b6cb0; --t-soft: #eaf2fb;
  position: relative; overflow: hidden;
  display: flex; align-items: center; gap: 12px;
  padding: 12px 12px 14px 14px;
  background: #fff; color: #1c2a23;
  border: 1px solid #e3e8e3; border-left: 4px solid var(--t-accent);
  border-radius: 14px;
  box-shadow: 0 12px 32px rgba(28, 42, 35, .18);
  pointer-events: auto;
}
.toast--success { --t-accent: #3a6450; --t-soft: #e8f0eb; }
.toast--error   { --t-accent: #c0444f; --t-soft: #fdecec; }
.toast--warning { --t-accent: #b7791f; --t-soft: #fff3df; }
.toast--info    { --t-accent: #2b6cb0; --t-soft: #eaf2fb; }

.toast-icon {
  flex-shrink: 0; width: 36px; height: 36px; border-radius: 11px;
  display: grid; place-items: center;
  background: var(--t-soft); color: var(--t-accent);
}
.toast--success .toast-icon { animation: toast-pop .45s cubic-bezier(.2, 1.6, .4, 1); }
.toast-msg {
  flex: 1; min-width: 0; margin: 0;
  font-size: 13.5px; font-weight: 700; line-height: 1.5;
}
.toast-close {
  flex-shrink: 0; width: 36px; height: 36px; border: none; border-radius: 9px;
  background: transparent; color: #9aa79e;
  display: grid; place-items: center; transition: background .15s, color .15s;
}
.toast-close:hover { background: #f1f4f1; color: #1c2a23; }
.toast-progress {
  position: absolute; left: 0; bottom: 0; height: 3px; width: 100%;
  background: var(--t-accent); opacity: .5; transform-origin: left;
  animation-name: toast-progress;
  animation-timing-function: linear;
  animation-fill-mode: forwards;
}
@keyframes toast-progress { from { transform: scaleX(1); } to { transform: scaleX(0); } }
@keyframes toast-pop { from { transform: scale(.3); } to { transform: scale(1); } }

.toast-enter-active { transition: opacity .3s ease, transform .35s cubic-bezier(.21, 1.02, .55, 1); }
.toast-leave-active { transition: opacity .22s ease, transform .22s ease; }
.toast-enter-from { opacity: 0; transform: translateY(-16px) scale(.96); }
.toast-leave-to { opacity: 0; transform: translateY(-10px) scale(.96); }
.toast-move { transition: transform .3s ease; }
</style>
