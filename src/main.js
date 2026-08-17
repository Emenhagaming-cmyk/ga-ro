import { createApp } from "vue"
import App from "./App.vue"
import router from "./router"
import "./assets/css/variable.css"
import "./style.css"
import "@fortawesome/fontawesome-free/css/all.min.css"
const app = createApp(App)

app.directive("reveal", {
  mounted(el, binding) {
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
    const delay = Number(binding.value) || 0;
    el.classList.add("reveal");
    if (delay) el.style.transitionDelay = delay + "s";
    const obs = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) {
          obs.disconnect();
          el.classList.add("revealed");
          el.addEventListener(
            "transitionend",
            () => {
              el.style.transitionDelay = "";
            },
            { once: true }
          );
        }
      },
      { threshold: 0.15 }
    );
    obs.observe(el);
  },
})

app.use(router)
app.mount("#app")