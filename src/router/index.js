import { createRouter, createWebHistory } from "vue-router";
import { useAuthSession, BACKEND } from "../composable/useAuthSession";

const routes = [
  {
    path: "/",
    component: () => import("../views/HomeView.vue"),
  },
  {
    path: "/login",
    beforeEnter: () => {
      window.location.href = `${BACKEND}/login`;
    },
  },
  {
    path: "/register",
    beforeEnter: () => {
      window.location.href = `${BACKEND}/register`;
    },
  },
  {
    path: "/spmb-info",
    component: () => import("../views/SpmbInfoView.vue"),
  },
  {
    path: "/chat",
    component: () => import("../views/ChatView.vue"),
  },
  {
    path: "/career-center",
    component: () => import("../views/CareerCenterView.vue"),
    meta: { requiresSiswa: true }
  },
  {
    path: "/berita",
    component: () => import("../views/NewsView.vue"),
  },
  {
    path: "/koperasi",
    component: () => import("../views/KoperasiView.vue"),
    meta: { requiresSiswa: true }
  },
  {
    path: "/produk-siswa",
    component: () => import("../views/ProdukSiswaView.vue"),
    meta: { requiresSiswa: true }
  },
  {
    path: "/e-learning",
    component: () => import("../views/ELearningView.vue"),
  },
  {
    path: "/e-tracer",
    component: () => import("../views/ETracerView.vue"),
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to) => {
  const { session, fetchStatus } = useAuthSession();

  if (to.meta.requiresSiswa) {
    // cache sessionStorage dulu → navigasi instan; refresh hanya jika belum yakin siswa
    if (session.value.role !== "siswa") {
      await fetchStatus();
      if (session.value.role !== "siswa") {
        return "/";
      }
    } else {
      fetchStatus();
    }
  }

  return true;
});

export default router;
