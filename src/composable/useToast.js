import { ref } from "vue";

const message = ref("");
const visible = ref(false);
let timer = null;

export function useToast() {
  function showToast(text, duration = 3000) {
    message.value = text;
    visible.value = true;
    clearTimeout(timer);
    timer = setTimeout(() => {
      visible.value = false;
    }, duration);
  }

  return { message, visible, showToast };
}
