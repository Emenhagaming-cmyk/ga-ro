import { ref } from "vue";

// State di modul → satu stack notifikasi dipakai semua view
const toasts = ref([]);
let seq = 0;

export function useToast() {
  function showToast(msg, type = "info", duration = 3200) {
    const id = ++seq;
    toasts.value.push({ id, msg, type, duration });
    if (toasts.value.length > 3) toasts.value.shift(); // maksimal 3 sekaligus
    setTimeout(() => dismissToast(id), duration);
  }

  function dismissToast(id) {
    toasts.value = toasts.value.filter((t) => t.id !== id);
  }

  return { toasts, showToast, dismissToast };
}
